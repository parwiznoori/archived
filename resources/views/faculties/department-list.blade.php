@extends('layouts.app')

@section('content')   
    <div class="portlet light bordered">
        <div class="portlet-title" style="border: 0">    
           <a href="{{ route('faculties.index', $university) }}" class="btn btn-default"><i class="icon-arrow-right"></i> {{ trans('general.back') }}</a> 
           
        </div>
        <div class="portlet-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('general.university') }}</th>
                        <th>{{ trans('general.faculty') }}</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $university->name }}</td>
                        <td>{{ $faculty->name }}</td>
                    </tr>
                </tbody>
                
            </table>
            <hr>
            <div class="row">
                <h3 style="text-align:center;">{{ trans('general.departments_list') }}</h3>
            </div>
            
            <hr>
            <br>
            
                
            @php
                $departmentListlength=count($departmensListByFaculty);
                $j=0;
            @endphp    
                    
            <table class="table">
                <tr>
                    <th style="width: 60px">{{ trans('general.number') }}</th>
                    <th>{{ trans('general.id') }}</th>
                    <th>{{ trans('general.name') }}</th>
                    <th>{{ trans('models/department.fields.department_eng') }}</th>
                    <th>{{ trans('models/department.fields.abbreviation_eng') }}</th>
                    <th>{{ trans('models/department.fields.department_type_id') }}</th>
                    <th>{{ trans('models/department.fields.grade_id') }}</th>
                    <th>{{ trans('models/department.fields.number_of_semesters') }}</th>
                    <th>{{ trans('models/department.fields.min_credits_for_graduated') }}</th>
                    
                    
                </tr>
            @for($i=0;$i < $departmentListlength;$i++)
                @php
                    $department=$departmensListByFaculty[$i];
                @endphp
                <tr>
                    <td>{{ ++$j }}</td>
                    <td>{{ $department['id'] }}</td>
                    <td>{{ $department['name'] }}</td>
                    <td>{{ $department['name_eng'] }}</td>
                    <td>{{ $department['abbreviation_eng'] }}</td>
                    <td>{{ $department['department_type_name'] }}</td>
                    <td>{{ $department['grade_name'] }}</td>
                    <td>{{ $department['number_of_semesters'] }}</td>
                    <td>{{ $department['min_credits_for_graduated'] }}</td>
                </tr>
            
            @endfor
           
            </table>
           
        </div>
    </div>


@endsection

@push('scripts')
<script>


</script>
@endpush