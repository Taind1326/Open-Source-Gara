<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// M03 - API gợi ý Hãng → Năm → Dòng cho form xe (dữ liệu trong config/xe_danh_muc.php)
class XeDanhMucController extends Controller
{
    private function danhMuc(): array
    {
        return config('xe_danh_muc', []);
    }

    private function namToiDa(): int
    {
        return now()->year + 1;
    }

    // GET xe-api/hang → ["Toyota", "Honda", ...]
    public function hang(): JsonResponse
    {
        return response()->json(array_keys($this->danhMuc()));
    }

    // GET xe-api/nam?hang=Toyota → [2027, 2026, ..., 1998] (năm mới → cũ, chỉ các năm hãng đó có dòng xe)
    public function nam(Request $request): JsonResponse
    {
        $dongXe = $this->danhMuc()[$request->query('hang')] ?? null;

        if ($dongXe === null) {
            return response()->json([]);
        }

        $tuNam = min(array_column($dongXe, 0));
        $denNam = max(array_map(fn ($k) => $k[1] ?? $this->namToiDa(), $dongXe));

        return response()->json(range($denNam, $tuNam));
    }

    // GET xe-api/dong?hang=Toyota&nam=2020 → ["Vios", "Camry", ...] (dòng xe đang bán trong năm đó)
    public function dong(Request $request): JsonResponse
    {
        $dongXe = $this->danhMuc()[$request->query('hang')] ?? null;
        $nam = (int) $request->query('nam');

        if ($dongXe === null || $nam <= 0) {
            return response()->json([]);
        }

        $ketQua = [];
        foreach ($dongXe as $ten => [$tuNam, $denNam]) {
            if ($nam >= $tuNam && $nam <= ($denNam ?? $this->namToiDa())) {
                $ketQua[] = $ten;
            }
        }

        return response()->json($ketQua);
    }
}
