<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ArchiveExcelTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Returning empty collection for template structure
        return collect([
            // Empty row structure for template
            [],
        ]);
    }

    public function headings(): array
    {
        return [

            'archive_id',
            'archiveimage_id',
            'column_number',
            'university_id',
            'faculty_id',
            'department_id',
            'grade_id',
            'semester_type_id' ,
            'name',
            'last_name',
            'father_name',
            'grandfather_name',
            'school' ,
            'school_graduation_year' ,
            'tazkira_number' ,
            'birth_date' ,
            'birth_place' ,
            'time' ,
            'kankor_id' ,
            'year_of_inclusion' ,
            'graduated_year' ,
            'transfer_year' ,
            'leave_year' ,
            'failled_year' ,
            'monograph_date',
            'monograph_number' ,
            'monograph_title' ,
            'averageOfScores'

        ];
    }
}
