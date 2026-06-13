<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArchiveUserReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $reports;
    
    public function __construct($reports)
    {
        $this->reports = collect($reports);
    }
    
    public function collection()
    {
        return $this->reports;
    }
    
    public function headings(): array
    {
        return [
            'شماره',
            'نام یوزر',
            'ایمیل',
            'نقش',
            'تعداد کتاب‌ها',
            'تعداد محصلان'
        ];
    }
    
    public function map($report): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            $report['user_name'],
            $report['user_email'],
            $report['role'],
            $report['books_count'],
            $report['students_count']
        ];
    }
}