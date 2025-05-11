@extends('layouts.app')

@section('content')
<div class="portlet box">
    <div class="portlet-body">
        
        <div class="form-body">
            
            <div class="row">
                <div class="col-md-6">
                    <h3>{{ trans('general.department') }}</h3>
                    <table class="table  table-condensed flip-content">
                        <tr>
                            <th>{{ trans('models/department.fields.university_id') }}</th>
                            <td>{{ $department->university_id ? $department->university->name : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.faculty_id') }}</th>
                            <td>{{ $department->faculty_id ? $department->facultyName->name : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.name') }}</th>
                            <td>{{ $department->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.department_eng') }}</th>
                            <td>{{ $department->department_eng }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.abbreviation_eng') }}</th>
                            <td>{{ $department->abbreviation_eng }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.number_of_semesters') }}</th>
                            <td>{{ $department->number_of_semesters }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.chairman') }}</th>
                            <td>{{ $department->chairman }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.department_student_affairs') }}</th>
                            <td>{{ $department->department_student_affairs }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.grade_id') }}</th>
                            <td>{{ $department->grade_id ? $department->grade->name : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.department_type_id') }}</th>
                            <td>{{ $department->department_type_id  ? $department->departmentType->name : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.created_at') }}</th>
                            <td>{{ $department->created_at }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('models/department.fields.updated_at') }}</th>
                            <td>{{ $department->updated_at }}</td>
                        </tr>  
                    </table>
                </div>
                <div class="col-md-6">
                    <h3>{{ trans('general.department_detail') }}</h3>
                    <table class="table  table-condensed flip-content">
                        <tr>
                            <th>{{ trans('general.students_number') }}</th>
                            <td>{{ $studentsNumbersInDepartment }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('general.groups_number') }}</th>
                            <td>{{ $groupsNumbersInDepartment }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('general.courses_number') }}</th>
                            <td>{{ $coursesNumbersInDepartment }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('general.subjects_number') }}</th>
                            <td>{{ $subjectsNumbersInDepartment }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <hr>

        </div> 
        <div class="form-actions fluid">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                   
                    <a href="{{ route('departments.index', $university) }}"
                        class="btn default">{{ trans('general.back') }}</a>
                </div>
            </div>
        </div>
      
    </div>
</div>
@endsection('content')