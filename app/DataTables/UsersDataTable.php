<?php

namespace App\DataTables;

use Illuminate\Support\Facades\Auth;
use App\User;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
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
            ->editColumn('active', function ($user) {
                return $user->active ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->editColumn('user_type', function ($user) {
                if($user->user_type == 1)
                {
                    return 'سطح وزارت - همه پوهنتون ها';
                }
                elseif($user->user_type == 2)
                {
                    return 'سطح وزارت - چند پوهنتون';
                }
                elseif($user->user_type == 3)
                {
                    return 'سطح پوهنتون';
                }
                else {
                    return '';
                }
                
            })
            ->setRowClass(function ($user) {
                return isset($user->deleted_at)  ? 'row_deleted' : '';
            })
            ->editColumn('username', function ($user) {
              return $user->user_name;
            })
            ->editColumn('universitiesName', function ($user) {
                
                $i=0;
                $university_array=array();
                foreach($user->universities as $university)
                {
                    $university_array[$i]=$university->university_name;
                    $i++;
                }
              return implode(',',$university_array);
            })
            ->editColumn('rolesNames', function ($user) {
                $i=0;
                $role_array=array();
                foreach($user->rolesNames as $role)
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
            ->addColumn('action', function ($user) {
                $html = '';
                $html = '<div class="btn-group">
                    <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                        <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';

                if (auth()->user()->can('edit-user')){
                    $html .= '<li><a href="'. route('users.edit', $user).'"  target="new" title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                }

                if(auth()->user()->hasRole('super-admin') ){
                    $html .= '<li><a href="'. route('users.editStatus', $user).'"  target="new" title = "'. trans('general.edit_status').' " > <i class="fa fa-pencil"></i> '. trans("general.edit_status") .' </a></li>';
                }

                if (auth()->user()->can('view-user-log')) {
                    $html .= '<li><a href="'. route('user.auth.log', $user).'"  target="new" title = "'. trans('general.auth_log').' " > <i class="fa fa-history"></i> '. trans("general.auth_log") .' </a></li>';
                    
                }

                if(auth()->user()->hasRole('super-admin') ){
                    if(isset($user->deleted_at))
                    {
                        $html .= '<li><a href="'. route('users.recover', $user) .'" target="new" onClick="doConfirm()"  title = "'. trans('general.restore').' " > <i class="fa fa-undo"></i> '. trans("general.restore") .' </a></li>';
                    }
                    
                }

                if (auth()->user()->can('delete-user')) {
                    $html .= '<li><form action="'. route('users.destroy', $user).'" method="post" style="display:inline">
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
    $user = Auth::user();
    $universityIds = $user->universities()->pluck('universities.id');

    $users = User::when(!auth()->user()->allUniversities(), function ($query) use ($universityIds, $user) {
            return $query->whereHas('universities', function ($q) use ($universityIds) {
                    $q->whereIn('universities.id', $universityIds);
                })
                ->where('id', '!=', $user->id);
        }, function ($query) {
            return $query->withTrashed();
        })
        ->where('type', 1)
        ->with([
            'rolesNames:id,title,admin,type_id,priority',
            'universities:id,name as university_name'
        ]);

    // For super-admins, exclude system-developers completely from results
    if (auth()->user()->hasRole('super-admin')) {
        $users->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'system-developer');
        });
    }

    return $users;
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
                    ->parameters(array_merge($this->getBuilderParameters(), [
                        'dom'          => '<"top"Bl>rt<"bottom"ip>',
                        'initComplete' => "function (settings, data) {   
                            emptyValue = '';                                     
                            table = this      
                            state = table.api().state.loaded()                        
                            
                            $('.dt-button.buttons-reset').click(function () {
                                $('.nav-tabs li').removeClass('active')
                                $('a[data-status-id=\"all\"]').parent().addClass('active');
                                $('tfoot input').val('');
                                $('tfoot select').val('');
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
            'name'     => ['name' => 'users.name' ,'title' => trans('general.name')],            
            'universitiesName' => ['name' => 'universities.name' ,'title' => trans('general.university')],
            'rolesNames' => ['name' => 'rolesNames.title' ,'title' => trans('general.role')],
            'position' => ['title' => trans('general.position')],
            'email'    => ['title' => trans('general.email')], 
            'phone'    => ['title' => trans('general.phone')],
            'updated_at'    => ['title' => trans('general.updated_at')],
        ];
        if(auth()->user()->hasRole('super-admin'))
        {
            $columns['active'] = ['title' => trans('general.active')];
            $columns['user_type'] = ['title' => trans('general.user_type')];
        }

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
