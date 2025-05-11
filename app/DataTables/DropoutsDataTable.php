<?php

namespace App\DataTables;

use App\Models\Dropout;
use Yajra\DataTables\Services\DataTable;

class DropoutsDataTable extends DataTable
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
            ->editColumn('form_no', function ($dropouts) {
                return $dropouts->student->form_no;
            })
            ->editColumn('name', function ($dropouts) {
                return $dropouts->student->name;
            })
            ->editColumn('father_name', function ($dropouts) {
                return $dropouts->student->father_name;
            })
            ->editColumn('university_name', function ($dropouts) {
                return $dropouts->university->name;
            })
            ->editColumn('removal_dropout', function ($dropouts) {
                return $dropouts->removal_dropout ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->editColumn('approved', function ($dropouts) {
                return $dropouts->approved ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->editColumn('permanent', function ($dropouts) {
                return $dropouts->permanent ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->addColumn('action', function ($dropouts) {
                $html = '';
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if ((auth()->user()->can('view-dropout')) or (auth()->user()->hasRole('super-admin')) ) {
                    $html .= '<li><a href="'. route('dropouts.show', $dropouts) .'"  title = "'. trans('general.pdf').' " > <i class="fa fa-file-pdf-o"></i> '. trans("general.pdf") .' </a></li>';
                }

                if ((auth()->user()->can('delete-dropout') and $dropouts->approved == false) or (auth()->user()->hasRole('super-admin'))) {
                    $html .= '<li><form action="'. route('dropouts.destroy', $dropouts) .'" method="post" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE" />
                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                        <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                    </form></li>';
                } 
                
                if ((auth()->user()->can('edit-dropout') and $dropouts->approved == false ) or (auth()->user()->hasRole('super-admin')) ) {
                    $html .= '<li><a href="'. route('dropouts.edit', $dropouts) .'"   title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                } 

                if ((auth()->user()->can('approve-dropout') and $dropouts->approved == false ) or (auth()->user()->hasRole('super-admin')) ) { 
                    $html .= '<li><a href="'. route('dropouts.approved', $dropouts) .'"   title = "'. trans('general.approved').' " > <i class="fa fa-spinner"></i> '. trans("general.approved") .' </a></li>';
                }

                if ((auth()->user()->can('removal-dropout') and $dropouts->approved == true and $dropouts->removal_dropout == false )  ) { 
                    $html .= '<li><a href="'. route('dropouts.removal', $dropouts) .'" onClick="doConfirm()"   title = "'. trans('general.removal_dropout').' " > <i class="fa fa-check"></i> '. trans("general.removal_dropout") .' </a></li>';
                }

                $html .= '</ul>
                    </div>';
                            
                return $html;
            })
            ->rawColumns(['action','approved','permanent','removal_dropout']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transfer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Dropout $model)
    {
        $query = Dropout::select('dropouts.*')
        ->with(['university'=>function($q){
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
                    ->addAction(['title' => trans('general.action'), 'width' => '۷0px'])
                    // ->parameters($this->getBuilderParameters());
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
                                                                                                                    
                                if(this.index() >= 1 && this.index() <= 7) { 
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
            'id'              => ['name' => 'dropouts.id', 'title' => trans('general.id'),'searchable' => false],          
            'form_no'         => ['name' => 'student.form_no', 'title' => trans('general.form_no')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')],
            'year'            => ['name' => 'dropouts.year', 'title' => trans('general.year')],
            'semester'        =>  ['name' => 'dropouts.semester', 'title' => trans('general.semester')],
            'name'            => ['name' => 'student.name', 'title' => trans('general.name')],
            'father_name'     => ['name' => 'student.father_name', 'title' => trans('general.father_name')],
            'note'            => ['name' => 'dropouts.note', 'title' => trans('general.note'), 'sortable' => false, 'searchable' => false],
            'approved'        => ['name' => 'dropouts.approved', 'title' => trans('general.approved')],
            'permanent'        => ['name' => 'dropouts.permanent', 'title' => trans('models/dropout.fields.permanent')],
            'removal_dropout'        => ['name' => 'dropouts.removal_dropout', 'title' => trans('models/dropout.fields.removal_dropout')],
            'removal_dropout_description'        => ['name' => 'dropouts.removal_dropout_description', 'title' => trans('models/dropout.fields.removal_dropout_description')]

        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Dropouts_' . date('YmdHis');
    }
}