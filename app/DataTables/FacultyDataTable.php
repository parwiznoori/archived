<?php

namespace App\DataTables;

use App\Models\Faculty;
use Yajra\DataTables\Services\DataTable;

class FacultyDataTable extends DataTable
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
            ->editColumn('departments_count', function ($faculty) {
                return $faculty->departments_count;
            })
            ->addColumn('action', function ($faculty) {
                $html = '';
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if (request()->is('universities*')) {

                    if (auth()->user()->can('view-faculty')) {
                        $html .= '<li><a href="'. route('faculties.departments_list', [request()->segment(2), $faculty]) .'" target="new"  title = "'. trans('general.departments_list').' " > <i class="fa fa-table"></i> '. trans("general.departments_list") .' </a></li>';
                    }

                    if (auth()->user()->can('edit-faculty')) {
                        $html .= '<li><a href="'. route('faculties.edit', [request()->segment(2), $faculty]) .'"  title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                    }

                    if (auth()->user()->can('delete-faculty') and ! $faculty->departments_count ) {
                        $html .= '<li><form action="'.route('faculties.destroy', [request()->segment(2), $faculty])  .'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                    } 
                } 
                $html .= '</ul>
                </div>';
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\University $faculty
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Faculty $faculty)
    {
        // $model = $model->where('faculties.university_id', request()->segment(2))->select('name', 'faculty', 'id','chairman','department_student_affairs');


        $faculty = $faculty->where('faculties.university_id', request()->segment(2))
            ->leftJoin('universities', 'universities.id', '=', 'university_id')
            ->select('faculties.name','faculties.name_en', 'faculties.id','faculties.chairman' ,'faculties.university_id','universities.name as university_name' );
        
        $faculty->withCount('departments');



        return $faculty;
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

                            if(!state || state.columns[0].search.search == '')        
                                $('a[data-status-id=\"all\"]').parent().addClass('active')
                            else
                                $('a[data-status-id=\"'+state.columns[0].search.search+'\"]').parent().addClass('active')

                            table.api().columns().every(function () {
                                var column = this;
                                var onEvent = 'change';
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 2) { 
                                    if (this.index() == 0 || this.index() == 2 ) {
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
            'id'     => ['title' => trans('general.id')],
            'name'    => ['title' => trans('models/faculty.fields.name')],                   
            'name_en'    => ['title' => trans('models/faculty.fields.name_en')],                   
            'university_name'    => ['name' => 'universities.name' ,'title' => trans('models/faculty.fields.university_id')],                   
            'departments_count' => ['name' => 'departments_count', 'title' => trans('general.departments_count'), 'searchable' => false, 'orderable' => false]              
                                                                                
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Faculty_' . date('YmdHis');
    }
}
