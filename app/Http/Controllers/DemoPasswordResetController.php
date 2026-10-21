<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class DemoPasswordResetController extends Controller
{
    private function guardDemo(): void
    {
        abort_unless(app()->environment(['local', 'testing']) && config('password_demo.enabled'), 404);
    }

    public function show(Request $request)
    {
        $this->guardDemo();
        $flow = $this->flow($request);
        if (!$flow) {
            return view('auth.forgot-password-demo', ['step' => 1, 'flow' => null]);
        }
        $step = $flow->verified_until && now()->lt(Carbon::parse($flow->verified_until)) ? 3 : 2;
        return view('auth.forgot-password-demo', compact('step', 'flow'));
    }

    private function flow(Request $request)
    {
        return DB::table('demo_password_resets')
            ->where('id', (string) $request->session()->get('demo_reset_id', ''))
            ->whereNull('consumed_at')->first();
    }

    public function start(Request $request)
    {
        $this->guardDemo();
        $request->merge(['Email' => trim((string) $request->input('Email'))]);
        $data = $request->validate(['Email' => ['required', 'email', 'max:100']], [
            'Email.required' => 'Vui lòng nhập email.', 'Email.email' => 'Email chưa đúng định dạng.',
        ]);
        $account = TaiKhoan::where('Email', $data['Email'])->where('TrangThai', 'HOAT_DONG')->first();
        if (!$account) {
            throw ValidationException::withMessages(['Email' => 'Không tìm thấy tài khoản đang hoạt động với email này.']);
        }
        $oldId = $request->session()->get('demo_reset_id');
        $id = (string) Str::uuid();
        DB::transaction(function () use ($oldId, $id, $account) {
            if ($oldId) DB::table('demo_password_resets')->where('id', $oldId)->update(['consumed_at' => now()]);
            DB::table('demo_password_resets')->insert([
                'id' => $id, 'email' => $account->Email,
                'otp_hash' => Hash::make('123456'),
                'expires_at' => now()->addSeconds(config('password_demo.otp_ttl')),
                'attempts' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
        $request->session()->put('demo_reset_id', $id);
        return redirect()->route('password.demo');
    }

    public function verify(Request $request)
    {
        $this->guardDemo();
        $data = $request->validate(['otp' => ['required', 'digits:6']], ['otp.required' => 'Vui lòng nhập mã OTP.', 'otp.digits' => 'OTP gồm đúng 6 chữ số.']);
        // Trả lỗi ngoài transaction để số lần nhập sai vẫn được lưu.
        $error = DB::transaction(function () use ($request, $data) {
            $flow = DB::table('demo_password_resets')->where('id', (string) $request->session()->get('demo_reset_id', ''))->lockForUpdate()->first();
            if (!$flow || $flow->consumed_at) return 'Phiên đã kết thúc. Vui lòng nhập email lại.';
            if (now()->gte(Carbon::parse($flow->expires_at))) return 'Mã đã hết hạn. Vui lòng gửi lại mã.';
            if ($flow->attempts >= config('password_demo.max_attempts')) return 'Bạn đã nhập sai quá số lần cho phép. Vui lòng gửi lại mã.';
            if (!Hash::check($data['otp'], $flow->otp_hash)) {
                DB::table('demo_password_resets')->where('id', $flow->id)->increment('attempts');
                return 'Mã OTP không đúng.';
            }
            DB::table('demo_password_resets')->where('id', $flow->id)->update([
                'verified_until' => now()->addSeconds(config('password_demo.grant_ttl')), 'updated_at' => now(),
            ]);
            return null;
        });
        if ($error) throw ValidationException::withMessages(['otp' => $error]);
        return redirect()->route('password.demo');
    }

    public function resend(Request $request)
    {
        $this->guardDemo();
        $error = DB::transaction(function () use ($request) {
            $flow = DB::table('demo_password_resets')->where('id', (string) $request->session()->get('demo_reset_id', ''))->lockForUpdate()->first();
            if (!$flow || $flow->consumed_at) return 'Phiên đã kết thúc. Vui lòng nhập email lại.';
            if (now()->lt(Carbon::parse($flow->expires_at))) return 'Vui lòng chờ hết thời gian trước khi gửi lại mã.';
            DB::table('demo_password_resets')->where('id', $flow->id)->update([
                'otp_hash' => Hash::make('123456'), 'expires_at' => now()->addSeconds(config('password_demo.otp_ttl')),
                'attempts' => 0, 'verified_until' => null, 'updated_at' => now(),
            ]);
            return null;
        });
        if ($error) throw ValidationException::withMessages(['otp' => $error]);
        return redirect()->route('password.demo')->with('success', 'Đã tạo lại mã OTP demo.');
    }

    public function reset(Request $request)
    {
        $this->guardDemo();
        $data = $request->validate(['MatKhau' => ['required', 'string', 'max:255', Password::min(8)->letters()->numbers()], 'MatKhau_confirmation' => ['bail', 'required', 'string', 'same:MatKhau']], [
            'MatKhau.required' => 'Vui lòng nhập mật khẩu mới.', 'MatKhau_confirmation.required' => 'Vui lòng nhập xác nhận mật khẩu.', 'MatKhau_confirmation.same' => 'Mật khẩu xác nhận không khớp.',
            'MatKhau.min' => 'Mật khẩu cần ít nhất 8 ký tự.', 'MatKhau.letters' => 'Mật khẩu phải có chữ cái.', 'MatKhau.numbers' => 'Mật khẩu phải có chữ số.',
        ]);
        DB::transaction(function () use ($request, $data) {
            $flow = DB::table('demo_password_resets')->where('id', (string) $request->session()->get('demo_reset_id', ''))->lockForUpdate()->first();
            if (!$flow || $flow->consumed_at || !$flow->verified_until || now()->gte(Carbon::parse($flow->verified_until))) {
                throw ValidationException::withMessages(['MatKhau' => 'Phiên xác nhận không hợp lệ hoặc hết hạn. Vui lòng bắt đầu lại.']);
            }
            $account = TaiKhoan::where('Email', $flow->email)->where('TrangThai', 'HOAT_DONG')->lockForUpdate()->first();
            if (!$account) throw ValidationException::withMessages(['MatKhau' => 'Tài khoản không còn hoạt động.']);
            $account->MatKhau = Hash::make($data['MatKhau']);
            $account->save();
            DB::table('demo_password_resets')->where('id', $flow->id)->update(['consumed_at' => now(), 'updated_at' => now()]);
        });
        $request->session()->forget('demo_reset_id');
        $request->session()->regenerate();
        return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.');
    }

    public function restart(Request $request)
    {
        $this->guardDemo();
        DB::table('demo_password_resets')->where('id', (string) $request->session()->get('demo_reset_id', ''))->update(['consumed_at' => now()]);
        $request->session()->forget('demo_reset_id');
        return redirect()->route('password.demo');
    }
}
