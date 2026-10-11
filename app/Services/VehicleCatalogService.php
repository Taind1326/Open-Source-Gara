<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class VehicleCatalogService
{
    private const BASE_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles/';

    public function makes(): array
    {
        return Cache::remember(
            'vehicle_catalog.makes.v1',
            now()->addDay(),
            function () {
                $rows = $this->fetch('GetAllMakes');

                $makes = collect($rows)
                    ->filter(function ($row) {
                        return ! empty($row['Make_ID'])
                            && ! empty($row['Make_Name']);
                    })
                    ->map(function ($row) {
                        return [
                            'id' => (int) $row['Make_ID'],
                            'name' => trim($row['Make_Name']),
                        ];
                    })
                    ->unique('id')
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();

                if (empty($makes)) {
                    throw new RuntimeException(
                        'Chưa tải được danh sách hãng xe. Vui lòng thử lại.'
                    );
                }

                return $makes;
            }
        );
    }

    public function models(int $makeId, int $year): array
    {
        $currentYear = now('Asia/Ho_Chi_Minh')->year;

        if ($year < 1990 || $year > $currentYear) {
            throw new RuntimeException('Năm xe không hợp lệ.');
        }

        $make = collect($this->makes())->firstWhere('id', $makeId);

        if (! $make) {
            throw new RuntimeException('Hãng xe không hợp lệ.');
        }

        $filteredByYear = $year >= 1996;

        $models = Cache::remember(
            "vehicle_catalog.models.v1.{$makeId}.{$year}",
            now()->addDay(),
            function () use ($makeId, $year, $filteredByYear) {
                $endpoint = $filteredByYear
                    ? "GetModelsForMakeIdYear/makeId/{$makeId}/modelyear/{$year}"
                    : "GetModelsForMakeId/{$makeId}";

                return collect($this->fetch($endpoint))
                    ->filter(function ($row) use ($makeId) {
                        return (int) ($row['Make_ID'] ?? 0) === $makeId
                            && ! empty($row['Model_ID'])
                            && ! empty($row['Model_Name']);
                    })
                    ->map(function ($row) {
                        return [
                            'id' => (int) $row['Model_ID'],
                            'name' => trim($row['Model_Name']),
                        ];
                    })
                    ->unique('id')
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();
            }
        );

        return [
            'make' => $make,
            'models' => $models,
            'filtered_by_year' => $filteredByYear,
            'notice' => $filteredByYear
                ? 'Danh sách theo năm mẫu xe trong dữ liệu NHTSA.'
                : 'Danh sách theo hãng, chưa lọc theo năm. '
                    . 'API không hỗ trợ bộ lọc năm 1990–1995.',
        ];
    }

    private function fetch(string $endpoint): array
    {
        try {
            $response = Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(20)
                ->get(self::BASE_URL . $endpoint, [
                    'format' => 'json',
                ]);

            $response->throw();

            $rows = $response->json('Results');

            if (! is_array($rows)) {
                throw new RuntimeException('Dữ liệu API không hợp lệ.');
            }

            return $rows;
        } catch (\Throwable $exception) {
            report($exception);

            throw new RuntimeException(
                'Không thể kết nối danh mục xe. Vui lòng thử lại sau.',
                0,
                $exception
            );
        }
    }
}