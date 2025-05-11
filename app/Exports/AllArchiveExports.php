<?php
namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class AllArchiveExports implements FromCollection, WithHeadings
{

    protected $filters;
    protected $select;

    public function __construct($filters,$select)
    {
        $this->filters = $filters;
        $this->select = $select;
    }

    public function collection()
    {
        // Perform a join with archivedatas and select the required fields
        return $this->filters;

    }

    public function headings(): array
    {
        return $this->select;
    }
}



