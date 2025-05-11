<?php

namespace App\DataTables;

use App\User;
use Yajra\DataTables\Services\DataTable;

class ArchiveRoleDataTable extends DataTable
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
            ->editColumn('active', function ($archiveuser) {
                return $archiveuser->active ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->setRowClass(function ($archiveuser) {
                return isset($archiveuser->deleted_at)  ? 'row_deleted' : '';
            })
//            ->editColumn('university', function ($user) {
//                return ($user->university_id >= 1 ) ? $user->university->name : '';
//            })
            ->editColumn('rolesNames', function ($archiveuser) {
                $i=0;
                $role_array=array();
                foreach($archiveuser->rolesNames as $role)
                {
                    if(auth()->user()->hasRole('system-developer'))
                    {
                        $role_array[$i]=$role->title;
                        $i++;
                    }
                    else
                    {
                        if($role->priority <= 900)
                        {
                            $role_array[$i]=$role->title;
                            $i++;
                        }
                    }


                }
              return implode(',',$role_array);
            })


            ->addColumn('action', function ($archiveuser) {
                $html = '';
                $html = '<div class="btn-group">
                    <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                        <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';

                if (auth()->user()->can('edit-user')){
                        $html .= '<li><a href="'. route('archiveuser.edit', $archiveuser).'"  title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                }

                if(auth()->user()->hasRole('super-admin') ){
                    $html .= '<li><a href="'. route('users.editStatus', $archiveuser).'"   title = "'. trans('general.edit_status').' " > <i class="fa fa-pencil"></i> '. trans("general.edit_status") .' </a></li>';
                }

                if (auth()->user()->can('view-user-log')) {
                    $html .= '<li><a href="'. route('user.auth.log', $archiveuser).'"  title = "'. trans('general.auth_log').' " > <i class="fa fa-history"></i> '. trans("general.auth_log") .' </a></li>';
                    
                }

                if(auth()->user()->hasRole('super-admin') ){
                    if(isset($archiveuser->deleted_at))
                    {
                        $html .= '<li><a href="'. route('users.recover', $archiveuser) .'"  onClick="doConfirm()"  title = "'. trans('general.restore').' " > <i class="fa fa-undo"></i> '. trans("general.restore") .' </a></li>';
                    }
                    
                }

                if (auth()->user()->can('delete-user')) {
                    $html .= '<li><form action="'. route('archiveuser.destroy', $archiveuser).'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                }
                
                $html .= '</ul>
                    </div>';  

                return $html;
            })
            ->rawColumns([ 'action', 'active']);


        return $datatables;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        // $users = $model->select('users.id', 'users.name', 'position', 'email', 'phone', 'universities.name as university', 'active')
        //     ->leftJoin('universities', 'universities.id', '=', 'university_id')
        //     ;

        $archiveuser = User::select('id', 'name', 'position', 'email', 'phone','type', 'active')
            ->where('type', 2)
            ->with(['rolesNames:id,title,admin,type_id,priority']);

        if(auth()->user()->hasRole('super-admin'))
        {
            $archiveuser = $archiveuser->withTrashed();
        }
//
//        $archiveuser = $archiveuser->get();

        return $archiveuser;

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
                    ->addAction(['title' => trans('general.action'), 'width' => '80px'])
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 7) { 
                                    if (this.index() == 0 || this.index() == 5 || this.index() == 6) {
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
            'id'     => ['title' => trans('general.id')],            
            'name'     => ['title' => trans('general.name')],            
//            'university' => ['name' => 'university.name' ,'title' => trans('general.university')],
            'rolesNames' => ['name' => 'rolesNames.title' ,'title' => trans('general.role')],
            'position' => ['title' => trans('general.position')],
            'email'    => ['title' => trans('general.email')], 
            'phone'    => ['title' => trans('general.phone')],
            'updated_at'    => ['title' => trans('general.updated_at')],
        ];
        if(auth()->user()->hasRole('super-admin'))
        {
            $columns['active'] = ['title' => trans('general.active')];
        }

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Archiveusers_' . date('YmdHis');
    }
}
