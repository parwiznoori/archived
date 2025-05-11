@extends('layouts.app')

@section('content')   
    <div class="portlet light bordered">
        <div class="portlet-title" style="border: 0">    
           <a href="{{ route('universities.index') }}" class="btn btn-default"><i class="icon-arrow-right"></i> {{ trans('general.back') }}</a> 
           <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('kankor_year', trans('general.year'), ['class' => 'control-label
                    col-sm-3']) !!}
                    <div class="col-sm-8">
                        {!! Form::select('kankor_year', $kankor_years, $kankorYear, ['class' => 'form-control select2 ', 'placeholder' => trans('general.select'),'onchange' => "change_year();" ]) !!}
                    </div>
                </div>
            </div>
        </div>
           
        </div>
        <div class="portlet-body">
           
            <div class="row">
                <h3 style="text-align:center;">{{ trans('general.view_activities').' : '.$university->name }}</h3>
            </div>
            
            <hr>
            <br>
            
            @php
                $departmentListLength=count($departmentListByUniversity);
                $j=0;
            @endphp    
                    
            <table class="table">
                <tr>
                    <th style="width: 60px">{{ trans('general.number') }}</th>
                    <th>{{ trans('general.faculty') }}</th>
                    <th>{{ trans('general.id') }}</th>
                    
                    <th>{{ trans('general.department') }}</th>
                    <th>{{ trans('general.groups_count') }}</th>
                    <th>{{ trans('general.courses_count') }}</th>
                    <th>{{ trans('general.students_count') }}</th> 
                </tr>
            @for($i=0;$i < $departmentListLength;$i++)
                @php
                    $department=$departmentListByUniversity[$i];
                @endphp
                <tr>
                    <td>{{ ++$j }}</td>
                    <td>{{ $department['faculty_name'] }}</td>
                    <td>{{ $department['department_id'] }}</td>
                    
                    <td>{{ $department['department_name'] }}</td>
                    <td>{{ $department['groups_count'] }}</td>
                    <td>{{ $department['courses_count'] }}</td>
                    <td>{{ $department['students_count'] }}</td>
                </tr>
            
            @endfor
           
            </table>
           
        </div>
    </div>


@endsection

@push('scripts')
<script>
    function change_year()
    {
        var year = document.getElementById("kankor_year").value;
        var university = {{ $university->id }};
        console.log(year);
        window.location.replace('/universities/'+university+'/activities/'+year);
    }

</script>
@endpush