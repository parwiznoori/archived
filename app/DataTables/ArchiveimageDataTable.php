<?php

namespace App\DataTables;

use App\Models\Archiveimage;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\URL;
class ArchiveimageDataTable extends DataTable
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

        ->setRowClass(function ($archiveimage) {
            $className='';
            if(isset($archiveimage->deleted_at))
            {
                $className.='row_deleted';
            }
            return $className;
        })




             ->editColumn('path', function ($archiveimage) {
                return "<img src='" . asset($archiveimage->path) . "' height='200' width='200'>";
               })
  


     
        ->addColumn('action', function ($archiveimage) {

                $html = '';
                $html = '<div class="btn-group">
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                        $html .= trans('general.action').'
                            <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">';
                      



                        if (auth()->user()->can('delete-archiveimage')) {
//                            $html .= '<li><form action="'. route('archiveimage.destroy', $archiveimage) .'" method="post" style="display:inline">
//                                <input type="hidden" name="_method" value="DELETE" />
//                                <input type="hidden" name="_token" value="'.csrf_token().'" />
//                                <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
//                            </form></li>';
                        }

                        $html .= '</ul>
                        </div>';

                

                return $html;
            })
           
            ->rawColumns(['path', 'action']);
            return $datatables;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Archiveimage $model)
    {
       
 
        $query = $model->select(
            'archiveimages.id', 
            'archives.book_name as archive',
            'path',
            // 'archiveimages.book_pagenumber',
            'total_students',
            'archivedatastatus.status as archivedatastatus',
            'archiveqcstatus.qc_status as archiveqcstatus',
        
        ) 
        ->leftJoin('archives', 'archives.id', '=', 'archiveimages.archive_id')
        ->leftJoin('archivedatastatus', 'archivedatastatus.id', '=', 'archiveimages.status_id')
        ->leftJoin('archiveqcstatus', 'archiveqcstatus.id', '=', 'archiveimages.qc_status_id');
        // ->orderBy('archives.book_name');

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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 6) { 
                                    if (this.index() == 0 || this.index() == 6) {
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
            // 'path' =>            ['name' => 'path','title' => trans('general.photo')],
            // 'book_pagenumber' => ['name' => 'book_pagenumber', 'title' => trans('general.book_pagenumber')],
            // 'type'            => [ 'name' => 'type' ,'title' => trans('general.type')],
            'path' =>            ['name' => 'path','title' => trans('general.photo')],
            'total_students'    => ['name' => 'total_students', 'title' => trans('general.total_students')],
            'archivedatastatus'    => ['name' => 'archivedatastatus.status', 'title' => trans('general.status')],
            'archiveqcstatus'    => ['name' => 'archiveqcstatus.qc_status', 'title' => trans('general.accept_or_refuse')],

        ];


    }
    
    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Archiveimage_' . date('YmdHis');
    }
}

