<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LandlordBoardersExport implements FromCollection, WithHeadings
{
    protected $boarders;
    public function __construct($boarders)
    {
        $this->boarders = $boarders;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Map boarders to export only relevant fields
        return $this->boarders->map(function($b) {
            $joined = $b->created_at ? $b->created_at->format('Y-m-d') : '';
            $duration = '';
            if ($b->created_at) {
                $start = $b->created_at;
                $now = now();
                $diff = $start->diff($now);
                $duration = $diff->m . ' months and ' . $diff->d . ' days';
            }
            return [
                'ID' => $b->id,
                'Pad Name' => $b->pad->padName ?? '',
                'Tenant' => ($b->tenant->first_name ?? '') . ' ' . ($b->tenant->last_name ?? ''),
                'Status' => ucfirst($b->status),
                'Joined' => $joined,
                'Duration' => $duration,
            ];
        });
    }
    public function headings(): array
    {
        return ['ID', 'Pad Name', 'Tenant', 'Status', 'Joined', 'Duration'];
    }
}
