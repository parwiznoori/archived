<?php

namespace App\DataTables;

use App\Models\GraduateBooksPdf;
use Yajra\DataTables\Services\DataTable;

class GraduateBookDataTable extends DataTable
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
            ->editColumn('university_name', function ($graduateBooksPdf) {
                return $graduateBooksPdf->university_id ? ($graduateBooksPdf->university->name ?? '' ) : '';
            })
            ->editColumn('department_name', function ($graduateBooksPdf) {
                return $graduateBooksPdf->department_id ? ($graduateBooksPdf->department->name ?? '' ) : '';
            })
            ->editColumn('faculty_name', function ($graduateBooksPdf) {
                return $graduateBooksPdf->department_id ? ($graduateBooksPdf->department->facultyName->name ?? '') : '';
            })
            ->editColumn('grade_name', function ($graduateBooksPdf) {
                return $graduateBooksPdf->grade_id ? ($graduateBooksPdf->grade->name ?? '' ) : '';
            })
            ->editColumn('user_name', function ($graduateBooksPdf) {
                return $graduateBooksPdf->user_id ? ($graduateBooksPdf->user->name ?? '' ) : '';
            })
            ->addColumn('action', function ($graduateBooksPdf) {
                $html = '';
                $html = '<div class="btn-group">';
                if (auth()->user()->can('print-graduate-book')) {
                    $html .= '<a class="btn btn-primary" href="'. route('graduate-book.download', $graduateBooksPdf->id) .'"   title = "' .trans('general.download').' " > <i class="fa fa-download"> </i> '.trans("general.download") .' </a>';    
                } 
                $html .= '</div>';
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\University $graduateBooksPdf
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(GraduateBooksPdf $graduateBooksPdf)
    {
        $query = GraduateBooksPdf::select(
            'graduate_books_pdf.*'
        )
        ->with(['university:id,name'])
        ->with(['department:id,name,faculty_id', 'department.facultyName:id,name'])
        ->with(['grade:id,name'])
        ->with(['user:id,name,email']);
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
                    ->addAction(['title' => trans('general.action'), 'width' => '120px'])
                    ->parameters(array_merge($this->getBuilderParameters([]), [
                        'dom'          => '<"top"Bl>rt<"bottom"ip>',
                        'initComplete' => "function (settings, data) {   
                            emptyValue = ''; 
                            reset_button=false;                                    
                            table = this      
                            state = table.api().state.loaded()                        
                            
                            $('.dt-button.buttons-reset').click(function () {
                                reset_button=true;  
                                console.log('reset buton clicked '+reset_button);
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
                                    if (this.index() == 0 || this.index() == 5) {
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
        return [
            'id'     => ['title' => trans('general.id')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')], 
            'faculty_name'    => ['name' => 'department.facultyName.name', 'title' => trans('general.faculty')], 
            'department_name' => ['name' => 'department.name', 'title' => trans('general.department')], 
            'grade_name' => ['name' => 'grade.name', 'title' => trans('general.grade')], 
            'graduated_year' => ['title' => trans('general.graduated_year')],
            'user_name' => ['name' => 'user.name', 'title' => trans('general.created_by')], 
            'status' => ['title' => trans('general.status')],
            'generated_count' => ['title' => trans('general.generated_count')],    
                                                                                
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'GraduateBook_' . date('YmdHis');
    }
}
