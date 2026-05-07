<?php

declare(strict_types=1);

namespace App\Imports;

use App\Services\Mci\TargetManagementService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TargetImport implements ToCollection, WithHeadingRow
{
    private TargetManagementService $targetService;
    private int $targetYear;

    public function __construct(TargetManagementService $targetService, int $targetYear)
    {
        $this->targetService = $targetService;
        $this->targetYear = $targetYear;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Determine year from row if provided, otherwise use constructor year
                $year = !empty($row['tahun']) ? (int)$row['tahun'] : $this->targetYear;
                
                $this->targetService->processTargetImport($row->toArray(), $year);
            }
        });
    }
}
