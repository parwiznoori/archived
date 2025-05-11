<?php

namespace App\DataTables;

use App\Models\Archive;
use App\Models\Archiveimage;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;
class ArchiveQCImageDataTable extends DataTable
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

            ->setRowClass(function ($archiveqcheck) {
                $className='';
                if(isset($archiveqcheck->deleted_at))
                {
                    $className.='row_deleted';
                }
//                // Check if qc_status_id is set and equals 1
//                if( $archiveqcheck->qc_status_id ==3 ) {
//                    $className .= ' qc_status';
//                }
//
//                if( $archiveqcheck->qc_status_id ==4  ) {
//                    $className .= ' qc_status2';
//                }
                return $className;

            })
            ->addColumn('action', function ($archiveqcheck)  {

                $html = '<div class="btn-group"> 
            <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';
                $html .= trans('general.action') . '
            <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">';


                    $html .= '<li><a href="' . route('archiveqcheckdata', ['archive_id' => $archiveqcheck->archiveId, 'image_archive_id' => $archiveqcheck->id]) . '" target="_blank"> <i class="fa fa-pencil"></i> ' . trans("general.check") . ' </a></li>';

                $html .= '</ul>
        </div>';

                return $html;
            })

            ->rawColumns(['action']);

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
        // Fetch archive ID from the URL segment
        $archiveId = request()->segment(2);
    
        // Fetch archive images related to the specified archive ID
        $archiveImagesQuery = Archiveimage::where('archive_id', $archiveId);
    
        // Build the query for the datatable
        $query = $archiveImagesQuery->select(
            'archives.id as archiveId',
            'archives.qc_status_id',
            'users.name as de_user_name',
            'archives.de_user_id',
            'archiveimages.id',
            'archives.book_name as archive',
            
            DB::raw("
            CONCAT(
                IF(TIMESTAMPDIFF(DAY, archiveimages.updated_at, NOW()) > 0, 
                    CONCAT(TIMESTAMPDIFF(DAY, archiveimages.updated_at, NOW()), ' روز '), 
                    ''
                ),
                IF(TIMESTAMPDIFF(HOUR, archiveimages.updated_at, NOW()) % 24 > 0, 
                    CONCAT(TIMESTAMPDIFF(HOUR, archiveimages.updated_at, NOW()) % 24, ' ساعت '), 
                    ''
                ),
                IF(TIMESTAMPDIFF(SECOND, archiveimages.updated_at, NOW()) % 60 > 0, 
                    CONCAT(TIMESTAMPDIFF(SECOND, archiveimages.updated_at, NOW()) % 60, ' ثانیه قبل'), 
                    'دقیقا الان'
                )
            ) as updated_days_diff
        "),
        
            'archiveimages.book_pagenumber',
            'archiveimages.total_students',
            'archiveimages.updated_at',
            'archivedatastatus.status as archivedatastatus',
            'archiveqcstatus.qc_status as archiveqcstatus'
        )
        ->leftJoin('archives', 'archives.id', '=', 'archiveimages.archive_id')
        ->leftJoin('users', 'users.id', '=', 'archives.de_user_id')
        ->leftJoin('archivedatastatus', 'archivedatastatus.id', '=', 'archiveimages.status_id')
        ->leftJoin('archiveqcstatus', 'archiveqcstatus.id', '=', 'archiveimages.qc_status_id');
    
        return $query;
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 8) { 
                                    if (this.index() == 0 || this.index() == 8) {
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
            'archive' =>        ['name' => 'archives.book_name', 'title' => trans('general.book_name')],
             'book_pagenumber' => ['name' => 'book_pagenumber', 'title' => trans('general.page')],
            'total_students'    => ['name' => 'total_students', 'title' => trans('general.total_students')],
            'archivedatastatus'    => ['name' => 'archivedatastatus.status', 'title' => trans('general.status')],
            'archiveqcstatus'    => ['name' => 'archiveqcstatus.qc_status', 'title' => trans('general.accept_or_refuse')],
             'updated_days_diff'  => [ 'name' => 'updated_days_diff' ,'title' => trans('general.update_date')],
            'de_user_name' =>        ['name' => 'de_user_name', 'title' => trans('general.archiveuser')],

        ];

    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Archiveqcheck_' . date('YmdHis');
    }
}