<?php

namespace App\Http\Controllers\Archive;

use App\Exports\AllArchiveExports;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ArchivereportDocController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function report3(Request $request)
    {
        $reportarchivedoc = DB::table('archivedatas')
            ->pluck('name', 'id');

        return view('archivereport.archive_report3', [
            'title' => trans('general.archive'),
            'description' => trans('general.create_archive'),
            'reportarchivedoc' => $reportarchivedoc,
        ]);
    }

    public function reportresult3(Request $request)
    {
        return $this->reportTypeDetail($request);
    }

   public function reportTypeDetail(Request $request)
{
    $wheres = [];
    $select = [];
    $headerTable = [];

    // کوئری اصلی با جوین
    $query = DB::table('archive_doc_types')
        ->leftJoin('archivedatas', 'archive_doc_types.archivedata_id', '=', 'archivedatas.id');

    // فیلتر تاریخ
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('archive_doc_types.doc_date', [$request->start_date, $request->end_date]);
        $select[] = 'archive_doc_types.doc_date';
        $headerTable[] = 'تاریخ اسناد';
    }

    // فیلتر نوع سند از archive_doc_types
    if ($request->doc_type) {
        $query->where('archive_doc_types.doc_type', $request->doc_type);
        $select[] = 'archive_doc_types.doc_type';
        $headerTable[] = 'نوع اسناد';
    }

    // ستون‌های مورد نیاز
    $select = array_merge($select, [
        'archive_doc_types.doc_number',
        'archivedatas.name as student_name',
        'archive_doc_types.doc_type',
    ]);

    $headerTable = array_merge($headerTable, [
         'شماره اسناد', 'نام محصل',
    ]);

    // اجرای نهایی کوئری
    $results = $query->select($select)->get();

    // تبدیل نوع سند به عنوان خوانا
    $results->transform(function ($item) {
        $item->doc_type = $this->getDocTypeName($item->doc_type);
        return $item;
    });

    return Excel::download(new AllArchiveExports($results, $headerTable), 'filtered-archives.xlsx');
}


 private function getDocTypeName($docType)
{
    $docTypes = [
        '1' => 'دیپلوم',
        '2' => 'ترانسکریپت',
        '3' => 'حوض جاب',
    ];

    return $docTypes[$docType] ?? 'نامشخص';
}

}
