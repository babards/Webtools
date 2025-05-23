<?php

namespace App\Exports;

use App\Models\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogsExport implements FromCollection, WithHeadings
{
    protected $logs;
    public function __construct($logs)
    {
        $this->logs = $logs;
    }
    public function collection()
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
            'ID', 'User ID', 'Action', 'Description', 'IP Address', 'Created At', 'Updated At'
        ];
    }
}
