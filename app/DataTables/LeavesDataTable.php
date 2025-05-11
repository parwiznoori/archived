<?php

namespace App\DataTables;

use App\Models\Leave;
use Yajra\DataTables\Services\DataTable;

class LeavesDataTable extends DataTable
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
            ->addIndexColumn()
            ->editColumn('end_leave', function ($leave) {
                return $leave->end_leave === 1 ? trans('general.yes') : trans('general.no');
            })
            ->editColumn('approved', function ($leave) {
                return $leave->approved === 1 ? trans('general.yes') : trans('general.no');
            })
            ->editColumn('form_no', function ($leave) {
                return $leave->student->form_no;
            })
            ->editColumn('name', function ($leave) {
                return $leave->student->name;
            })
            ->editColumn('father_name', function ($leave) {
                return $leave->student->father_name;
            })
            ->editColumn('university_name', function ($leave) {
                return $leave->university->name;
            })
            ->editColumn('department_name', function ($leave) {

                return $leave->department_id ? $leave->department->name : '';
            })
            ->editColumn('half_year', function ($leave) {
                return $leave->semester_type_id ? $leave->semesterType->name : '';
            })
            ->addColumn('action', function ($leave) {
                $html = '';
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if ((auth()->user()->can('view-leave')) or (auth()->user()->hasRole('super-admin')) ) {
                    $html .= '<li><a href="'. route('leaves.show', $leave) .'"  title = "'. trans('general.pdf').' " > <i class="fa fa-file-pdf-o"></i> '. trans("general.pdf") .' </a></li>';
                }

                if ((auth()->user()->can('delete-leave') and $leave->approved == false) or (auth()->user()->hasRole('super-admin'))) {
                    $html .= '<li><form action="'. route('leaves.destroy', $leave) .'" method="post" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE" />
                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                        <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                    </form></li>';
                } 
                
                if ((auth()->user()->can('edit-leave') and $leave->approved == false ) or (auth()->user()->hasRole('super-admin')) ) {
                    $html .= '<li><a href="'. route('leaves.edit', $leave) .'" onClick="doConfirm()"    title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                } 

                if ((auth()->user()->can('approve-leave') and $leave->approved == false ) or (auth()->user()->hasRole('super-admin')) ) { 
                    $html .= '<li><a href="'. route('leaves.approved', $leave) .'" onClick="doConfirm()"   title = "'. trans('general.approved').' " > <i class="fa fa-spinner"></i> '. trans("general.approved_leave") .' </a></li>';
                }

                if ( auth()->user()->can('end-leave') ) 
                { 
                    $html .= '<li><a href="'. route('leaves.end_leave', $leave) .'" onClick="doConfirm()"   title = "'. trans('general.approved').' " > <i class="fa fa-check"></i> '. trans("general.end_leave") .' </a></li>';
                }
                $html .= '</ul>
                </div>';
                                                
                return $html;
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transfer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Leave $model)
    {
        $query = Leave::select('leaves.*')
        ->with(['university'=>function($q){
            $q->select('id','name');
        }])
        ->with(['department'=>function($q){
            $q->select('id','name');
        }])
        ->with(['semesterType'=>function($q){
            $q->select('id','name');
        }])
        ->with(['student'=>function($q){
            $q->select('id','name','form_no','father_name');
        }]);
        
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
                    // ->parameters(array_merge($this->getBuilderParameters([]), [
                    //     'dom'          => 'Bflrtip',
                    // ]));
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
                                                                                                                    
                                if(this.index() >= 1 && this.index() <= 6) { 
                                    if (this.index() == 1 ) {
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
            'id'      => ['name' => 'id', 'title' => trans('general.id'),'searchable' => false],
            'form_no'         => ['name' => 'student.form_no', 'title' => trans('general.form_no')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')],
            'department_name' => ['name' => 'department.name', 'title' => trans('general.department')],
            'name'            => ['name' => 'student.name', 'title' => trans('general.name')],
            'father_name'     => ['name' => 'student.father_name', 'title' => trans('general.father_name')],
            'leave_year'      =>  ['name' => 'leave_year', 'title' => trans('general.leave_year')],
            'half_year'       => [ 'name' => 'semesterType.name','title' => trans('general.half_year')],
            'semester'      =>  ['name' => 'semester', 'title' => trans('general.semester')],
            'approved'     =>  ['name' => 'approved', 'title' => trans('general.approved')],
            'end_leave'     =>  ['name' => 'end_leave', 'title' => trans('general.end_leave')],
            // 'note'            => ['name' => 'note', 'title' => trans('general.note'), 'sortable' => false, 'searchable' => false]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Leaves_' . date('YmdHis');
    }
}