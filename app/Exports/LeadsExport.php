<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $fields;

    public function __construct($data, $fields)
    {
        $this->data = $data;
        $this->fields = $fields;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return collect($this->fields)->map(function ($field) use ($item) {
                return $item[$field];
            });
        });
    }

    public function headings(): array
    {
        return $this->fields;
    }
}
