<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LandlordApplicationsExport implements FromCollection, WithHeadings
{
    protected $applications;
    public function __construct($applications)
    {
        $this->applications = $applications;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Map applications to export only relevant fields
        return $this->applications->map(function($app) {
            return [
                'ID' => $app->id,
                'Pad Name' => $app->pad->padName ?? '',
                'Tenant' => ($app->tenant->first_name ?? '') . ' ' . ($app->tenant->last_name ?? ''),
                'Application Date' => $app->application_date ? $app->application_date->format('Y-m-d') : '',
                'Status' => $app->status,
                'Message' => $app->message,
            ];
        });
    }
    public function headings(): array
    {
        return ['ID', 'Pad Name', 'Tenant', 'Application Date', 'Status', 'Message'];
    }
}
