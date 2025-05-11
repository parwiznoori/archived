@extends('layouts.app')

@section('content')   
    <div class="portlet light bordered">
        <div class="portlet-title" style="border: 0">    
           <a href="{{ route('universities.index') }}" class="btn btn-default"><i class="icon-arrow-right"></i> {{ trans('general.back') }}</a> 
           
        </div>
        <div class="portlet-body">
           
            <div class="row">
                <h3 style="text-align:center;">{{ trans('general.users_list') }}</h3>
            </div>
            
            <hr>
            <br>
            
            @php
                $usersListLength=count($usersListByUniversity);
                $j=0;
            @endphp    
                    
            <table class="table">
                <tr>
                    <th style="width: 60px">{{ trans('general.number') }}</th>
                    <th>{{ trans('general.id') }}</th>
                    <th>{{ trans('general.name') }}</th>
                    <th>{{ trans('general.position') }}</th>
                    <th>{{ trans('general.phone') }}</th>
                    <th>{{ trans('general.email') }}</th>
                    <th>{{ trans('general.active') }}</th>
                    <th>{{ trans('general.grade') }}</th>
                    <th>{{ trans('general.login_at') }}</th>
                    
                    
                </tr>
            @for($i=0;$i < $usersListLength;$i++)
                @php
                    $department=$usersListByUniversity[$i];
                @endphp
                <tr>
                    <td>{{ ++$j }}</td>
                    <td>{{ $department['id'] }}</td>
                    <td>{{ $department['name'] }}</td>
                    <td>{{ $department['position'] }}</td>
                    <td>{{ $department['phone'] }}</td>
                    <td>{{ $department['email'] }}</td>
                    <td>{!! $department['active'] ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>" !!}</td>
                    <td>{{ $department['user_grade'] }}</td>
                    <td>{{ $department['login_at'] }}</td>
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