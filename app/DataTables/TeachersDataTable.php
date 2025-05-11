<?php

namespace App\DataTables;

use App\Models\Teacher;
use Yajra\DataTables\Services\DataTable;

class TeachersDataTable extends DataTable
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
            ->editColumn('type', function ($teacher) {
                return trans('general.'.$teacher->type);
            })
            ->editColumn('active', function ($teacher) {
                return $teacher->active ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->editColumn('gender', function ($teacher) {
                return ($teacher->gender == "Male" ) ? trans('general.Male') : trans('general.Female');
            })
            ->addColumn('action', function ($teachers) {
                $html = '';
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if (auth()->user()->can('edit-teacher'))
                {
                    $html .= '<li><a href="'. route('teachers.edit', $teachers).'"  target="new" title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                }

                if(auth()->user()->hasRole('super-admin') ){
                    $html .= '<li><a href="'. route('teachers.editStatus', $teachers).'"  target="new" title = "'. trans('general.edit_status').' " > <i class="fa fa-pencil"></i> '. trans("general.edit_status") .' </a></li>';
                }
               
                if (auth()->user()->can('delete-teacher'))
                {
                    $html .= '<li><form action="'. route('teachers.destroy', $teachers).'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                }
                
             return $html;
            })
            ->rawColumns(['action','active']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Teacher $model)
    {
        $query = $model->select(
                'teachers.id',
                'teachers.name',
                'teachers.father_name',
                'teachers.email',
                'teachers.phone',
                'teachers.type',
                'teachers.gender',
                'teachers.degree',
                'teachers.province',
                'teachers.active',
                'universities.name as university',
                'departments.name as department',
                'teachers.last_name',
                'teachers.university_id',
                'teachers.academic_rank_id',
                'teacher_academic_ranks.title',
                'provinces.name as province_name',

            )
            ->leftJoin('provinces', 'provinces.id', '=', 'teachers.province')
            ->leftJoin('universities', 'universities.id', '=', 'university_id')
            ->leftJoin('departments', 'departments.id', '=', 'department_id')
            ->leftJoin('teacher_academic_ranks', 'teacher_academic_ranks.id', '=', 'academic_rank_id');

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
                    ->addAction(['title' => trans('general.action'), 'width' => '70px'])
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 13) { 
                                    if (this.index() == 0 || this.index() == 4  || this.index() == 6 || this.index() == 7 ) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else if(this.index() == 12)
                                    {
                                        let select = document.createElement('select');
                                        select.add(new Option(''));
                                        select.add(new Option('مرد','Male'));
                                        select.add(new Option('زن','Female'));
                                        column.footer().replaceChildren(select);
                                        select.addEventListener('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else if(this.index() == 13)
                                    {
                                        let select = document.createElement('select');
                                        select.add(new Option(''));
                                        select.add(new Option('فعال','1'));
                                        select.add(new Option('غیرفعال','0'));
                                        column.footer().replaceChildren(select);
                                        select.addEventListener('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else 
                                    {
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
        $columns =  [
            'id'               => ['name' => 'teachers.id','title' => trans('general.id')],    
            'name'             => ['name' => 'teachers.name','title' => trans('general.name')],
            'last_name'        => ['name' => 'teachers.last_name', 'title' => trans('general.last_name')],
            'father_name'      => ['name' => 'teachers.father_name', 'title' => trans('general.father_name')],
            'degree'           => ['name' => 'teachers.degree', 'title' => trans('general.degree')],
            'title'            => ['name' => 'teacher_academic_ranks.title', 'title' => trans('general.academic_rank')],
            'email'            => [ 'name' => 'teachers.email' ,'title' => trans('general.email')],
            'phone'            => [ 'name' => 'teachers.phone' ,'title' => trans('general.phone')],
            'type'             => [ 'name' => 'teachers.type' ,'title' => trans('general.type')],
            'university'       => ['name' => 'universities.name','title' => trans('general.university')],
            'department'       => ['name' => 'departments.name','title' => trans('general.department')],
            'province_name'    => ['name' => 'provinces.name','title' => trans('general.province')],
            'gender'           => ['name' => 'teachers.gender', 'title' => trans('general.gender')],
        ];
        if(auth()->user()->hasRole('super-admin'))
        {
            $columns['active'] = ['name' => 'teachers.active', 'title' => trans('general.active')];
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
        return 'Teachers_' . date('YmdHis');
    }
}