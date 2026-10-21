<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use App\Models\PhanCong;
use App\Models\PhieuKiemTra;
use App\Models\PhuTung;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class XacNhanSuaChuaController extends Controller
{
    private const MAX_TOTAL = 999999999999999;

    public function preview(Request $request, $maLH)
{
    $lichHen = $this->technicianAppointment($request, $maLH);
    $phieu = $lichHen->phieuKiemTra;

    if (!$this->isDraft($lichHen, $phieu)) {
        return redirect()
            ->route('ktv.lichhen.show', $maLH)
            ->with(
                'error',
                'Chỉ gửi được phiếu nháp của xe đang kiểm tra.'
            );
    }

    $snapshot = $this->draftSnapshot($lichHen, $phieu);
    $baoHanhs = $this->warrantySources($lichHen)->groupBy('MaHD');
    $phienBan = $this->draftToken($snapshot);
    $loiHangMuc = $this->itemProblem($lichHen);

    return view('ktv.kiemtra.confirm', compact(
        'lichHen',
        'phieu',
        'snapshot',
        'baoHanhs',
        'phienBan',
        'loiHangMuc'
    ));
}

    public function send(Request $request, $maLH)
    {
        $this->technicianAppointment($request, $maLH);
        $data = $request->validate([
            'phien_ban' => ['required', 'string', 'size:64'],
            'MaHDBaoHanh' => ['nullable', 'integer', 'min:1'],
            'bao_hanh_dich_vus' => ['nullable', 'array'],
            'bao_hanh_dich_vus.*' => ['required', 'integer', 'min:1', 'distinct'],
        ], [
            'phien_ban.required' => 'Vui lòng tải lại phiếu trước khi gửi.',
            'MaHDBaoHanh.integer' => 'Hóa đơn bảo hành không hợp lệ.',
            'bao_hanh_dich_vus.array' => 'Danh sách dịch vụ bảo hành không hợp lệ.',
            'bao_hanh_dich_vus.*.integer' => 'Dịch vụ bảo hành không hợp lệ.',
            'bao_hanh_dich_vus.*.distinct' => 'Dịch vụ bảo hành bị trùng.',
        ]);

        DB::transaction(function () use ($request, $maLH, $data) {
            $lichHen = LichHen::where('MaLH', $maLH)->lockForUpdate()->firstOrFail();
            $assignment = PhanCong::where('MaLH', $maLH)
                ->where('MaKTV', $request->user()->MaTK)->lockForUpdate()->first();
            abort_unless($assignment, 404);
            $phieu = PhieuKiemTra::where('MaLH', $maLH)->lockForUpdate()->first();
            if (!$this->isDraft($lichHen, $phieu)) {
                $this->fail('phien_ban', 'Phiếu đã được gửi hoặc lịch đã chuyển trạng thái.');
            }

            $lichHen->load(['chiTietLichHens.dichVu.loaiDichVu', 'chiTietLichHens.phuTung']);
            $snapshot = $this->draftSnapshot($lichHen, $phieu);
            if (!hash_equals($this->draftToken($snapshot), $data['phien_ban'])) {
                $this->fail('phien_ban', 'Nội dung nháp đã thay đổi. Vui lòng tải lại trang để kiểm tra.');
            }
            if (trim((string) $phieu->KetLuan) === '') {
                $this->fail('phien_ban', 'Vui lòng quay lại nhập kết luận và lưu nháp trước khi gửi.');
            }
            $chosen = $lichHen->chiTietLichHens->where('KTVDeXuat', true);
            if ($chosen->isEmpty()) {
                $this->fail('phien_ban', 'Chưa có hạng mục đề xuất. Vui lòng quay lại cập nhật phiếu.');
            }
            $loiHangMuc = $this->itemProblem($lichHen, true);

if ($loiHangMuc !== null) {
    $this->fail('phien_ban', $loiHangMuc);
}

            $warrantyIds = array_map('intval', $data['bao_hanh_dich_vus'] ?? []);
            if (count($warrantyIds) !== count(array_unique($warrantyIds))) {
                $this->fail('bao_hanh_dich_vus', 'Danh sách bảo hành bị trùng.');
            }
            $invoiceId = $data['MaHDBaoHanh'] ?? null;
            if ((bool) $invoiceId !== (count($warrantyIds) > 0)) {
                $this->fail('MaHDBaoHanh', 'Muốn áp dụng bảo hành, hãy chọn hóa đơn cũ và ít nhất một dịch vụ đúng hạng mục.');
            }
            $sources = $this->warrantySources($lichHen)
                ->where('MaHD', (int) $invoiceId)->keyBy('MaDV');
            $chosenServices = $chosen->whereNotNull('MaDV')->pluck('MaDV')->map(fn ($id) => (int) $id)->all();
            foreach ($warrantyIds as $id) {
                if (!in_array($id, $chosenServices, true) || !$sources->has($id)) {
                    $this->fail('bao_hanh_dich_vus', 'Dịch vụ bảo hành phải đang được đề xuất, đúng xe, đúng hóa đơn đã thanh toán và còn hạn lúc nhận xe.');
                }
            }
            foreach ($snapshot['HangMuc'] as &$item) {
                $source = $item['KTVDeXuat'] && in_array($item['MaDV'], $warrantyIds, true)
                    ? $sources->get($item['MaDV']) : null;
                $item['MaCTHDBaoHanh'] = $source?->MaCTHD;
                $lichHen->chiTietLichHens()->where('MaCTLH', $item['MaCTLH'])
                    ->update(['MaCTHDBaoHanh' => $item['MaCTHDBaoHanh']]);
            }
            unset($item);
            $snapshot['MaHDBaoHanh'] = $invoiceId ? (int) $invoiceId : null;
            $snapshot = $this->price($snapshot, $warrantyIds ? 30 : 0);
            $phieu->update([
                ...$this->totals($snapshot),
                'NoiDungDaGui' => $snapshot,
                'NgayGui' => now('Asia/Ho_Chi_Minh'),
                'TrangThaiXacNhan' => PhieuKiemTra::CHO_XAC_NHAN,
            ]);
            $lichHen->update([
                'TrangThai' => LichHen::CHO_XAC_NHAN,
                'MaHDBaoHanh' => $snapshot['MaHDBaoHanh'],
            ]);
        }, 3);

        return redirect()->route('ktv.lichhen.show', $maLH)
            ->with('success', 'Đã gửi phiếu. Chờ khách xác nhận trước khi sửa chữa.');
    }

    public function show(Request $request, $maLH)
    {
        $lichHen = LichHen::with(['xe', 'phieuKiemTra'])
            ->where('MaTK', $request->user()->MaTK)->findOrFail($maLH);
        $phieu = $lichHen->phieuKiemTra;
        abort_unless($phieu && $phieu->TrangThaiXacNhan !== PhieuKiemTra::NHAP, 404);
        $snapshot = $this->sentSnapshot($phieu);
        $diemKhaDung = $this->availablePoints($request->user(), $lichHen->MaLH);
        $coThePhanHoi = $lichHen->TrangThai === LichHen::CHO_XAC_NHAN
            && $phieu->TrangThaiXacNhan === PhieuKiemTra::CHO_XAC_NHAN;
        $banKhongDiem = $this->price($snapshot, $snapshot['LoaiUuDai'] === 'BAO_HANH' ? 30 : 0);
        $banDoiDiem = $snapshot['LoaiUuDai'] !== 'BAO_HANH'
            ? $this->price($snapshot, 5) : null;
        $phienBan = $this->sentToken($phieu);

        return view('public.xacnhan.show', compact(
            'lichHen', 'phieu', 'snapshot', 'diemKhaDung', 'coThePhanHoi',
            'banKhongDiem', 'banDoiDiem', 'phienBan'
        ));
    }

    public function approve(Request $request, $maLH)
    {
        LichHen::where('MaTK', $request->user()->MaTK)->findOrFail($maLH);
        $data = $request->validate([
            'phien_ban' => ['required', 'string', 'size:64'],
            'su_dung_diem' => ['required', 'boolean'],
            'dong_y' => ['accepted'],
        ], [
            'dong_y.accepted' => 'Vui lòng xác nhận đã đọc và đồng ý hạng mục cùng chi phí.',
            'su_dung_diem.required' => 'Vui lòng chọn phương án ưu đãi.',
            'su_dung_diem.boolean' => 'Phương án sử dụng điểm không hợp lệ.',
            'phien_ban.required' => 'Vui lòng tải lại phiếu trước khi xác nhận.',
        ]);

        DB::transaction(function () use ($request, $maLH, $data) {
            // Cùng tài khoản chỉ được giữ chỗ một lượng điểm thực sự có.
            $account = TaiKhoan::where('MaTK', $request->user()->MaTK)->lockForUpdate()->firstOrFail();
            [$lichHen, $phieu] = $this->pending($account->MaTK, $maLH, $data['phien_ban']);
            $snapshot = $this->sentSnapshot($phieu);
            $rate = $snapshot['LoaiUuDai'] === 'BAO_HANH' ? 30 : 0;
            if ((bool) $data['su_dung_diem']) {
                if ($rate === 30) $this->fail('su_dung_diem', 'Không cộng dồn đổi điểm và bảo hành.');
                if ($this->availablePoints($account, $maLH) < 50) {
                    $this->fail('su_dung_diem', 'Không còn đủ 50 điểm khả dụng. Vui lòng chọn không sử dụng điểm.');
                }
                $rate = 5;
            }
            $snapshot = $this->price($snapshot, $rate);
            if ($rate === 5 && $snapshot['GiaTriDuocGiam'] === 0) {
                $this->fail('su_dung_diem', 'Phiếu không có dịch vụ đủ điều kiện giảm bằng điểm.');
            }
            if (DB::table('PHIEUSUACHUA')->where('MaLH', $maLH)->exists()) {
                $this->fail('dong_y', 'Lịch đã có phiếu sửa chữa. Vui lòng tải lại trang.');
            }
            $phieu->update([
                ...$this->totals($snapshot),
                'NoiDungDaGui' => $snapshot,
                'TrangThaiXacNhan' => PhieuKiemTra::DONG_Y,
                'NgayPhanHoi' => now('Asia/Ho_Chi_Minh'),
                'NoiDungPhanHoi' => 'Đồng ý các hạng mục và chi phí theo phương án ưu đãi đã chọn.',
            ]);
            DB::table('PHIEUSUACHUA')->insert([
                'MaLH' => $lichHen->MaLH,
                'MaPKT' => $phieu->MaPKT,
            ]);
            $lichHen->update(['TrangThai' => LichHen::CHO_SUA_CHUA]);
            // Chỉ ghi nhận 50 điểm được giữ cho đơn này, chưa trừ điểm ở tài khoản.
            // Lúc xác nhận thanh toán phải khóa tài khoản trước rồi mới khóa lịch,
            // trừ DiemSuDung và cộng 10 đúng một lần theo DaXuLyDiem.
        }, 3);

        return redirect()->route('public.xacnhan.show', $maLH)
            ->with('success', 'Đã xác nhận sửa chữa. Kỹ thuật viên sẽ tiếp tục xử lý xe.');
    }

    public function reject(Request $request, $maLH)
    {
        LichHen::where('MaTK', $request->user()->MaTK)->findOrFail($maLH);
        if (is_string($request->input('LyDo'))) $request->merge(['LyDo' => trim($request->input('LyDo'))]);
        $data = $request->validate([
            'phien_ban' => ['required', 'string', 'size:64'],
            'LyDo' => ['required', 'string', 'max:1000'],
        ], [
            'LyDo.required' => 'Vui lòng nhập lý do từ chối sửa chữa.',
            'LyDo.string' => 'Lý do phải là văn bản.',
            'LyDo.max' => 'Lý do không được vượt quá 1.000 ký tự.',
            'phien_ban.required' => 'Vui lòng tải lại phiếu trước khi từ chối.',
        ]);
        DB::transaction(function () use ($request, $maLH, $data) {
            $account = TaiKhoan::where('MaTK', $request->user()->MaTK)->lockForUpdate()->firstOrFail();
            [$lichHen, $phieu] = $this->pending($account->MaTK, $maLH, $data['phien_ban']);
            $now = now('Asia/Ho_Chi_Minh');
            $phieu->update([
                'TrangThaiXacNhan' => PhieuKiemTra::TU_CHOI,
                'NgayPhanHoi' => $now,
                'NoiDungPhanHoi' => $data['LyDo'],
            ]);
            $lichHen->update([
                'TrangThai' => LichHen::DA_HUY,
                'LyDoHuy' => $data['LyDo'],
                'NgayHuy' => $now,
            ]);
        }, 3);
        return redirect()->route('public.xacnhan.show', $maLH)
            ->with('success', 'Đã từ chối sửa chữa và hủy lịch. Lịch sử vẫn được giữ lại.');
    }

    private function technicianAppointment(Request $request, $id): LichHen
    {
        return LichHen::with([
            'xe', 'taiKhoan', 'phieuKiemTra',
            'chiTietLichHens.dichVu.loaiDichVu', 'chiTietLichHens.phuTung',
        ])->whereHas('phanCong', fn ($query) => $query->where('MaKTV', $request->user()->MaTK))->findOrFail($id);
    }

    private function isDraft(LichHen $lichHen, ?PhieuKiemTra $phieu): bool
    {
        return $lichHen->TrangThai === LichHen::DANG_KIEM_TRA
            && $phieu && $phieu->TrangThaiXacNhan === PhieuKiemTra::NHAP;
    }

    private function draftSnapshot(LichHen $lichHen, PhieuKiemTra $phieu): array
    {
        $items = $lichHen->chiTietLichHens->sortBy('MaCTLH')->map(fn ($row) => [
            'MaCTLH' => (int) $row->MaCTLH,
            'MaLH' => (int) $row->MaLH,
            'MaDV' => $row->MaDV !== null ? (int) $row->MaDV : null,
            'MaPT' => $row->MaPT !== null ? (int) $row->MaPT : null,
            'KhachDaChon' => (bool) $row->KhachDaChon,
            'KTVDeXuat' => (bool) $row->KTVDeXuat,
            'SoLuong' => (int) $row->SoLuong,
            'DonGia' => (int) $row->DonGia,
            'TenHangMuc' => $row->MaDV !== null ? ($row->dichVu?->TenDV ?? 'Dịch vụ đã ngừng sử dụng') : ($row->phuTung?->TenPT ?? 'Phụ tùng đã ngừng sử dụng'),
            'DonViTinh' => $row->MaDV !== null ? 'Gói' : ($row->phuTung?->DonViTinh ?? 'Cái'),
            'GhiChu' => $row->GhiChu,
            'MaCTHDBaoHanh' => null,
        ])->filter(fn ($item) => $item['KhachDaChon'] || $item['KTVDeXuat'])->values()->all();
        return $this->price([
            'phienBan' => 1, 'KetLuan' => $phieu->KetLuan,
            'GhiChu' => $phieu->GhiChu, 'HangMuc' => $items, 'MaHDBaoHanh' => null,
        ], 0);
    }

    private function warrantySources(LichHen $lichHen)
    {
        if (!$lichHen->NgayNhanXe) return collect();
        $received = $lichHen->NgayNhanXe->format('Y-m-d H:i:s');
        return DB::table('CTHOADON as ct')
            ->join('HOADON as hd', 'hd.MaHD', '=', 'ct.MaHD')
            ->join('PHIEUSUACHUA as sc', 'sc.MaPSC', '=', 'hd.MaPSC')
            ->join('LICHHEN as cu', 'cu.MaLH', '=', 'hd.MaLH')
            ->join('CHITIETLICHHEN as item', 'item.MaCTLH', '=', 'ct.MaCTLH')
            ->where('hd.TrangThai', 'DA_THANH_TOAN')
            ->where('cu.MaTK', $lichHen->MaTK)->where('cu.MaXe', $lichHen->MaXe)
            ->where('cu.MaLH', '<>', $lichHen->MaLH)
            ->where('ct.LoaiHangMuc', 'DICH_VU')
            ->where('sc.NgayHoanThanh', '<=', $received)
            ->where('sc.BaoHanhDen', '>=', $received)
            ->select('ct.MaCTHD', 'ct.MaHD', 'ct.TenHangMuc', 'item.MaDV', 'sc.BaoHanhDen')
            ->orderByDesc('hd.MaHD')->get();
    }

    private function price(array $snapshot, int $rate): array
    {
        $gross = $eligible = $discount = 0;
        foreach ($snapshot['HangMuc'] as &$item) {
            $selected = (bool) ($item['KTVDeXuat'] ?? false);
            $unit = (int) $item['DonGia'];
            $quantity = (int) $item['SoLuong'];
            if ($unit < 0 || $quantity < 1 || ($unit > 0 && $quantity > intdiv(self::MAX_TOTAL, $unit))) {
                $this->fail('phien_ban', 'Giá trị hạng mục vượt giới hạn lưu trữ.');
            }
            $amount = $unit * $quantity;
            $service = ($item['MaDV'] ?? null) !== null;
            $lineRate = $selected && $service && ($rate === 5 || ($rate === 30 && !empty($item['MaCTHDBaoHanh']))) ? $rate : 0;
            $lineDiscount = intdiv($amount * $lineRate + 50, 100);
            $item['TyLeGiam'] = $lineRate;
            $item['TienGiam'] = $lineDiscount;
            $item['ThanhTien'] = $selected ? $amount - $lineDiscount : 0;
            if ($selected) {
                if ($gross > self::MAX_TOTAL - $amount) $this->fail('phien_ban', 'Tổng chi phí vượt giới hạn lưu trữ.');
                $gross += $amount;
                $discount += $lineDiscount;
                if ($lineRate) $eligible += $amount;
            }
        }
        unset($item);
        $snapshot['LoaiUuDai'] = $rate === 30 ? 'BAO_HANH' : ($rate === 5 ? 'DOI_DIEM' : 'KHONG');
        $snapshot['TyLeGiam'] = $rate;
        $snapshot['DiemSuDung'] = $rate === 5 ? 50 : 0;
        $snapshot['TongTruocGiam'] = $gross;
        $snapshot['GiaTriDuocGiam'] = $eligible;
        $snapshot['TienGiam'] = $discount;
        $snapshot['TongSauGiam'] = $gross - $discount;
        return $snapshot;
    }

    private function totals(array $snapshot): array
    {
        return array_intersect_key($snapshot, array_flip([
            'LoaiUuDai', 'TyLeGiam', 'DiemSuDung', 'TongTruocGiam',
            'GiaTriDuocGiam', 'TienGiam', 'TongSauGiam',
        ]));
    }

    private function availablePoints(TaiKhoan $account, int $exclude): int
    {
        $reserved = DB::table('PHIEUKIEMTRA as kt')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'kt.MaLH')
            ->where('lh.MaTK', $account->MaTK)->where('lh.MaLH', '<>', $exclude)
            ->where('kt.TrangThaiXacNhan', PhieuKiemTra::DONG_Y)
            ->where('kt.LoaiUuDai', 'DOI_DIEM')
            ->whereIn('lh.TrangThai', [LichHen::CHO_SUA_CHUA, LichHen::DANG_SUA_CHUA, LichHen::CHO_THANH_TOAN])
            ->sum('kt.DiemSuDung');
        return max(0, (int) $account->DiemTichLuy - (int) $reserved);
    }

    private function pending(int $accountId, $id, string $token): array
    {
        $lichHen = LichHen::where('MaTK', $accountId)->where('MaLH', $id)->lockForUpdate()->firstOrFail();
        $phieu = PhieuKiemTra::where('MaLH', $id)->lockForUpdate()->first();
        if (!$phieu || $lichHen->TrangThai !== LichHen::CHO_XAC_NHAN || $phieu->TrangThaiXacNhan !== PhieuKiemTra::CHO_XAC_NHAN) {
            $this->fail('phien_ban', 'Phiếu đã được phản hồi hoặc lịch đã chuyển trạng thái. Vui lòng tải lại trang.');
        }
        if (!hash_equals($this->sentToken($phieu), $token)) {
            $this->fail('phien_ban', 'Nội dung phiếu đã thay đổi. Vui lòng tải lại để xem đúng chi phí trước khi xác nhận.');
        }
        return [$lichHen, $phieu];
    }

    private function sentSnapshot(PhieuKiemTra $phieu): array
    {
        $snapshot = $phieu->NoiDungDaGui;
        abort_unless(is_array($snapshot) && isset($snapshot['HangMuc'], $snapshot['LoaiUuDai']), 409, 'Phiếu chưa có nội dung xác nhận hợp lệ.');
        return $snapshot;
    }

    private function sentToken(PhieuKiemTra $phieu): string
    {
        return hash('sha256', (string) $phieu->getRawOriginal('NoiDungDaGui'));
    }

    private function draftToken(array $snapshot): string
    {
        return hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    private function itemProblem(
    LichHen $lichHen,
    bool $lockParts = false
): ?string {
    // Chỉ kiểm tra hạng mục KTV còn chọn.
    // Hạng mục đã bỏ chọn vẫn giữ trong lịch sử nhưng không chặn gửi.
    $chosen = $lichHen->chiTietLichHens
        ->filter(fn ($row) => (bool) $row->KTVDeXuat);

    $partIds = $chosen
        ->whereNotNull('MaPT')
        ->pluck('MaPT')
        ->unique()
        ->values();

    $query = PhuTung::query()
        ->whereIn('MaPT', $partIds)
        ->orderBy('MaPT');

    if ($lockParts) {
        $query->lockForUpdate();
    }

    $parts = $query->get()->keyBy('MaPT');
    $coHangMucNgungHoatDong = false;
    $coPhuTungVuotTonKho = false;

    foreach ($chosen as $row) {
        if ($row->MaDV !== null) {
            if (
                !$row->dichVu?->HoatDong
                || !$row->dichVu?->loaiDichVu?->HoatDong
            ) {
                $coHangMucNgungHoatDong = true;
            }

            continue;
        }

        $part = $parts->get($row->MaPT);

        if (!$part || !$part->HoatDong) {
            $coHangMucNgungHoatDong = true;
            continue;
        }

        if ((int) $row->SoLuong > (int) $part->SoLuongTon) {
            $coPhuTungVuotTonKho = true;
        }
    }

    if ($coHangMucNgungHoatDong && $coPhuTungVuotTonKho) {
        return 'Có hạng mục đã ngừng hoạt động và phụ tùng vượt tồn kho. '
            . 'Vui lòng quay lại bỏ chọn hạng mục ngừng hoạt động, '
            . 'điều chỉnh số lượng và lưu phiếu nháp.';
    }

    if ($coHangMucNgungHoatDong) {
        return 'Có dịch vụ, nhóm dịch vụ hoặc phụ tùng đã ngừng hoạt động. '
            . 'Vui lòng quay lại bỏ chọn hạng mục đó và lưu phiếu nháp.';
    }

    if ($coPhuTungVuotTonKho) {
        return 'Có phụ tùng vượt tồn kho hiện tại. '
            . 'Vui lòng quay lại điều chỉnh số lượng và lưu phiếu nháp.';
    }

    return null;
}

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
