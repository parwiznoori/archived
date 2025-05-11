<?php

namespace App\DataTables;

use App\Models\Transfer;
use Yajra\DataTables\Services\DataTable;

class TransfersDataTable extends DataTable
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

            ->setRowClass(function ($transfer) {
                return isset($transfer->deleted_at)  ? 'row_deleted' : '';
            })
            ->editColumn('form_no', function ($transfer) {
                return $transfer->student->form_no ?? '' ;
            })
            ->editColumn('name', function ($transfer) {
                return $transfer->student->name ?? '' ;
            })
            ->editColumn('father_name', function ($transfer) {
                return $transfer->student->father_name ?? '' ;
            })
            ->editColumn('from_university', function ($transfer) {
                return $transfer->from_department_id ? ($transfer->fromDepartment->university->name ?? '' ) : '';
            })
            ->editColumn('from_department', function ($transfer) {

                return $transfer->from_department_id ? ($transfer->fromDepartment->name ?? '' ) : '';
            })
            ->editColumn('to_university', function ($transfer) {
                return $transfer->to_department_id ? ($transfer->toDepartment->university->name ?? '' ) : '';
            })
            ->editColumn('to_department', function ($transfer) {

                return $transfer->to_department_id ? ($transfer->toDepartment->name ?? '' ) : '';
            })
            ->editColumn('approved', function ($transfer) {
                return $transfer->approved ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->editColumn('exception', function ($transfer) {
                return $transfer->exception ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
           
            ->addColumn('action', function ($transfer) {
                    
                $html = '';

                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if ((auth()->user()->can('view-transfer')) or (auth()->user()->hasRole('super-admin')) ) {
                    $html .= '<li><a href="'. route('transfers.download-pdf', $transfer) .'"  title = "'. trans('general.pdf').' " > <i class="fa fa-file-pdf-o"></i> '. trans("general.pdf") .' </a></li>';
                }

                if(auth()->user()->hasRole('super-admin') ){
                    if(isset($transfer->deleted_at))
                    {
                        $html .= '<li><a href="'. route('transfers.recover', $transfer) .'" onClick="doConfirm()"  title = "'. trans('general.restore').' " > <i class="fa fa-exchange"></i> '. trans("general.restore") .' </a></li>';
                    }
                } 

                if ((auth()->user()->can('delete-transfer') and $transfer->approved == false) or (auth()->user()->hasRole('super-admin'))) {
                    $html .= '<li><form action="'. route('transfers.destroy', $transfer) .'" method="post" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE" />
                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                        <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                    </form></li>';
                } 

                if ( auth()->user()->can('view-transfer') ) {
                    $html .= '<li><a href="'. route('transfers.show', $transfer) .'"   title = "'. trans('general.show').' " > <i class="fa fa-eye"></i> '. trans("general.show") .' </a></li>';
                } 

                if ((auth()->user()->can('edit-transfer') and $transfer->approved == false ) or (auth()->user()->hasRole('super-admin')) ) {
                    $html .= '<li><a href="'. route('transfers.edit', $transfer) .'"   title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                } 

                if ((auth()->user()->can('approve-transfer') and $transfer->approved == false ) or (auth()->user()->hasRole('super-admin')) ) { 
                    $html .= '<li><a href="'. route('transfers.approved', $transfer) .'"   title = "'. trans('general.approved').' " > <i class="fa fa-spinner"></i> '. trans("general.approved") .' </a></li>';
                }
                $html .= '</ul>
                    </div>';  
                return $html;
            })
            ->rawColumns(['action','approved','exception']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transfer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transfer $model)
    {
        $query = Transfer::select(
            'transfers.id',
            'transfers.student_id',
            'transfers.from_department_id',
            'transfers.to_department_id',
            'transfers.education_year',
            'transfers.approved',
            'transfers.semester',
            'transfers.exception',
            'transfers.deleted_at',
            'transfers.note',
        )
        ->with(['student:id,name,form_no,father_name,gender,province,kankor_year'])
        ->with(['fromDepartment:id,name,university_id', 'fromDepartment.university:id,name'])
        ->with(['toDepartment:id,name,university_id', 'toDepartment.university:id,name'])
        ;

        // $query = $model->select(
        //     'transfers.id',
        //     'transfers.approved',
        //     'transfers.education_year',
        //     'transfers.semester',
        //     'transfers.exception',
        //     'students.form_no',
        //     'students.name',
        //     'transfers.deleted_at',
        //     'students.father_name as father_name',
        //     \DB::raw('CONCAT(from_department.name, "-", from_university.name) as from_department'),
        //     \DB::raw('CONCAT(to_department.name, "-", to_university.name) as to_department'),
        //     'note'
        //     )
        // ->join('students', 'students.id', '=', 'student_id')

        // ->join('departments as from_department', 'from_department.id', '=', 'from_department_id')
        // ->leftJoin('universities as from_university', 'from_university.id', '=', 'from_department.university_id')

        // ->join('departments as to_department', 'to_department.id', '=', 'to_department_id')        
        // ->leftJoin('universities as to_university', 'to_university.id', '=', 'to_department.university_id');

        if(auth()->user()->hasRole('super-admin'))  
        {
            $query =$query->withTrashed();
        }  
        else
        {
            $query =$query->whereNull('transfers.deleted_at');

        }

        if (! auth()->user()->allUniversities()) {                
            // $query->leftJoin('departments as from', 'from.id', '=' , 'from_department_id')
            //     ->leftJoin('departments as to', 'from.id', '=' , 'to_department_id')
            $query->leftjoin('departments as from_department', 'from_department.id', '=', 'from_department_id')
            ->leftjoin('departments as to_department', 'to_department.id', '=', 'to_department_id')
            ->where('from_department.university_id', auth()->user()->university_id)
            ->orWhere('to_department.university_id', auth()->user()->university_id);                                   
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

                    if(!state || state.columns[0].search.search == '')        
                        $('a[data-status-id=\"all\"]').parent().addClass('active')
                    else
                        $('a[data-status-id=\"'+state.columns[0].search.search+'\"]').parent().addClass('active')

                    table.api().columns().every(function () {
                        var column = this;
                        var onEvent = 'change';
                                                                                                            
                        if(this.index() >= 0 && this.index() <= 8) { 
                            if (this.index() == 1 || this.index() == 7) {
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
        if(auth()->user()->hasRole('super-admin'))  
        {
            return [            
                'form_no'           => ['name' => 'student.form_no', 'title' => trans('general.form_no')],
                'name'              => ['name' => 'student.name','title' => trans('general.name')],
                'father_name'       => ['name' => 'student.father_name','title' => trans('general.father_name')],
                'education_year'    => ['name' => 'transfers.education_year','title' => trans('models/transfer.fields.education_year')],
                'semester'          => ['name' => 'transfers.semester','title' => trans('general.semester')],
                'from_university'   => ['name' => 'fromDepartment.university.name', 'title' => trans('general.from_university')],
                'from_department'   => ['name' => 'fromDepartment.name', 'title' => trans('general.from_department')],
                'to_university'     => ['name' => 'toDepartment.university.name', 'title' => trans('general.to_university')],
                'to_department'     => ['name' => 'toDepartment.name', 'title' => trans('general.to_department')],
                'approved'          => ['name' => 'transfers.approved', 'title' => trans('general.approved')],
                // 'note'              => ['name' => 'transfers.note', 'title' => trans('general.note'), 'sortable' => false, 'searchable' => false],
                'exception'         => ['name' => 'transfers.exception', 'title' => trans('general.exception')],
            ];
        } 
        else
        {
            return [            
                'form_no'           => ['name' => 'student.form_no', 'title' => trans('general.form_no')],
                'name'              => ['name' => 'student.name','title' => trans('general.name')],
                'father_name'       => ['name' => 'student.father_name','title' => trans('general.father_name')],
                'education_year'    => ['name' => 'transfers.education_year','title' => trans('models/transfer.fields.education_year')],
                'semester'          => ['name' => 'transfers.semester','title' => trans('general.semester')],
                'from_university'   => ['name' => 'fromDepartment.university.name', 'title' => trans('general.from_university')],
                'from_department'   => ['name' => 'fromDepartment.name', 'title' => trans('general.from_department')],
                'to_university'     => ['name' => 'toDepartment.university.name', 'title' => trans('general.to_university')],
                'to_department'     => ['name' => 'toDepartment.name', 'title' => trans('general.to_department')],
                'approved'          => ['name' => 'transfers.approved', 'title' => trans('general.approved')],
                // 'note'              => ['name' => 'transfers.note', 'title' => trans('general.note'), 'sortable' => false, 'searchable' => false],
            ];
        }
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Transfers_' . date('YmdHis');
    }
}