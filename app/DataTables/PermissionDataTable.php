<?php
namespace App\DataTables;

use App\Models\Permission;
use Yajra\DataTables\Services\DataTable;

class PermissionDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowClass(function ($Permission) {
                $className='';
                if(isset($Permission->deleted_at))
                {
                    $className='row_deleted';
                }
                return $className;
            })
            ->addColumn('action', function ($Permission) {
                $html = '';
                $html = '<div class="btn-group">
                    <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                        <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';

                if(auth()->user()->hasRole('system-developer')  ){
                    if(isset($Permission->deleted_at))
                    {
                        $html .= '<li><a href="'. route('permissions.recover', [$Permission->id]) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.restore").' </a></li>';
                    }
                } 

                if (auth()->user()->hasRole('system-developer')  && !isset($Permission->deleted_at) ) 
                {
                    $html .= '<li><a href="'. route('permissions.edit', [$Permission->id]).'"  target="new" title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                }

                if ( auth()->user()->hasRole('system-developer')  && (!isset($Permission->deleted_at)) ) 
                {
                    $html .= '<li><form action="'. route('permissions.destroy', [$Permission->id]) .'" method="post" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE" />
                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                        <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                    </form></li>';
                }
            
                $html .= '</ul>
                    </div>';  
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Permission $model)
    {
        $query = Permission::select(
            'permissions.id',
            'permissions.name',
            'permissions.title',
            'permissions.guard_name',
            'permissions.is_restricted',
            'permissions.deleted_at'
        );
        if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('system-developer'))  
        {
            $query =$query->withTrashed();
        }  
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
                    ->addAction(['title' => trans('general.action'), 'width' => '60px'])
                    ->parameters(array_merge($this->getBuilderParameters([]), [
                        'dom'          => '<"top"Bl>rt<"bottom"ip>',
                        'initComplete' => "function (settings, data) {   
                            emptyValue = '';                                     
                            table = this      
                            state = table.api().state.loaded()                        
                            
                            $('.dt-button.buttons-reset').click(function () {
                                $('.nav-tabs li').removeClass('active')
                                $('a[data-status-id=\"all\"]').parent().addClass('active')
                            })

                            table.api().columns().every(function () {
                                var column = this;
                                var onEvent = 'change';
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 2) { 
                                    if (this.index() == 1 ) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    } else {
                                        $('<input class=\"datatable-footer-input \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                }
                            });

                            $('a.student-status').click(function () {
                                if ($(this).attr('data-status-id') == 'all')
                                    table.api().columns(0).search('', false, false, true).draw();
                                else
                                    table.api().columns(0).search($(this).attr('data-status-id'), false, false, true).draw();
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
        $columns = [            
            'title'     => ['title' => trans('models/permissions.fields.title')],            
            'name' => ['title' => trans('models/permissions.fields.name')],
            'guard_name' => ['title' => trans('models/permissions.fields.guard_name')],
            // 'action'    => ['title' => trans('general.status')],
        ];

        if(auth()->user()->hasRole('system-developer'))
        {
            $columns['is_restricted'] = ['title' => trans('general.is_restricted')];
        }

        return $columns;
    }

   
}
