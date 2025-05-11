<?php

namespace App\DataTables;

use App\Models\Archive;
use Yajra\DataTables\Services\DataTable;

class ArchiveqcDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        $datatables = datatables($query)

                ->setRowClass(function ($archiveqc) {
                    $className='';
                    if(isset($archiveqc->deleted_at))
                    {
                        $className.='row_deleted';
                    }

                    // Check if qc_status_id is set and equals 1
                    if( $archiveqc->qc_status_id ==3 && $archiveqc->status_id ==4 ) {
                        $className .= ' qc_status';
                    }

                    if( $archiveqc->qc_status_id ==4 && $archiveqc->status_id ==4 ) {
                        $className .= ' qc_status2';
                    }

                    return $className;

            })
            ->addColumn('action', function ($archiveqc) {

                $html = '';
                $html = '<div class="btn-group"> 
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';
                        $html .= trans('general.action').'
                            <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">';



                            $html .= '<li><a href="' . route("archiveqcheck", ['id' => $archiveqc->id]) . '" > <i class="fa fa-pencil"></i> '. trans("general.check") .' </a></li>';






                        $html .= '</ul>
                        </div>';



                return $html;
            })
            ->rawColumns( ['action']);

            return $datatables;



    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
{

    if(auth()->user()->hasRole('super-admin')){
        $query = Archive::query()->where('status_id', 4);
    }else{
        $query = Archive::query()->where('status_id', 4)
            ->where('qc_user_id',auth()->user()->id);
    }

    return $query->select(
        'archives.id',
        'archives.status_id',
        'archives.qc_status_id',
        'universities.name as university',
//        'faculties.name as faculty',
//        'departments.name as department',
        'archiveyears.year as archiveyears',
        'archives.book_pagenumber',
        'book_description',
        'book_name',
        'archivedatastatus.status as archivedatastatus',
        'archiveqcstatus.qc_status as archiveqcstatus',
    )
        ->leftJoin('universities', 'universities.id', '=', 'archives.university_id')
//        ->leftJoin('faculties', 'faculties.id', '=', 'archivedatas.faculty_id')
//        ->leftJoin('departments', 'departments.id', '=', 'archivedatas.department_id')
        ->leftJoin('archivedatastatus', 'archivedatastatus.id', '=', 'archives.status_id')
        ->leftJoin('archiveyears', 'archiveyears.id', '=', 'archives.archive_year_id')
        ->leftJoin('archiveqcstatus', 'archiveqcstatus.id', '=', 'archives.qc_status_id');

}


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['title' => trans('general.action'), 'width' => '100px'])
                    ->parameters(array_merge($this->getBuilderParameters([]), [
                        'dom'          => 'Brtip',
                        'initComplete' => "function (settings, data) {   
                            emptyValue = '';                                     
                            table = this      
                            state = table.api().state.loaded()                        
                            
                            $('.dt-button.buttons-reset').click(function () {
                                $('.nav-tabs li').removeClass('active')
                                $('a[data-status-id=\"all\"]').parent().addClass('active')
                            })

                            if(!state || state.columns[0].search.search == '')        
                                $('a[data-status-id=\"all\"]').parent().addClass('active')
                            else
                                $('a[data-status-id=\"'+state.columns[0].search.search+'\"]').parent().addClass('active')

                            table.api().columns().every(function () {
                                var column = this;
                                var onEvent = 'change';
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 7) { 
                                    if (this.index() == 0 || this.index() == 7) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    } else {
                                        $('<input class=\"datatable-footer-input \"  placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />') .attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                }
                            });

                            $('#dataTableBuilder').wrap('<div class=\"table-responsive\"></div>');
                        }"

                    ]));
    }
    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        return [
            'book_name'    => ['name' => 'book_name', 'title' => trans('general.book_name')],
            'university' => ['name' => 'universities.name', 'title' => trans('general.university')],
//            'faculty'    => ['name' => 'faculties.name', 'title' => trans('general.faculty')],
//            'department'    => ['name' => 'departments.name', 'title' => trans('general.department')],
            'archiveyears' => ['name' => 'archiveyears.year','title' => trans('general.book_year')],
//            'book_pagenumber'    => ['name' => 'book_pagenumber', 'title' => trans('general.book_pagenumber')],
            'archivedatastatus'    => ['name' => 'archivedatastatus.status', 'title' => trans('general.status')],
            'archiveqcstatus'    => ['name' => 'archiveqcstatus.qc_status', 'title' => trans('general.accept_or_refuse')],
            'book_description'    => ['name' => 'book_description', 'title' => trans('general.book_description')],


        ];
       
    }
    
    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Archiveqc_' . date('YmdHis');
    }
}