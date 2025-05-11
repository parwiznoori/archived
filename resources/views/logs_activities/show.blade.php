@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="todo-container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="todo-projects-container">
                        <li class="todo-padding-b-0">
                            <div class="todo-head">
                                <h3 style="list-style-type:none;padding-right: 20px"> {{__('general.user_name')}} :
                                    {{$log->causer ? $log->causer->name : ''}} </h3>
                                <br>
                                <p style="list-style-type:none;padding-right: 20px"> {{__('general.creation_date')}}
                                    :{{ $log ?  $log->created_at : ''}}</p>
                                <br>
                                <h3 style="list-style-type:none;padding-right: 20px"> {{__('general.description')}} :
                                    {{$log ? $log->description : ''}} </h3>
                                <br>
                                <h3 style="list-style-type:none;padding-right: 20px"> {{__('general.log_name')}} :
                                    {{$log ? $log->subject_type : ''}} </h3>
                            </div>
                        </li>
                        <li style="color: black; font-size: 15px;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{__('general.key')}}</th>
                                        <th>{{__('general.updated_data')}}</th>
                                        <th>{{__('general.old_data')}}</th>
                                    </tr>

                                    @if($log)
                                    @foreach($log->changes['attributes'] as $key=> $value)
                                    <tr>
                                        <td> {{ __('general.' . $key )  }}</td>
                                        <td> {{ $value }}</td>
                                        <td> {{ isset($log->changes['old']) ? $log->changes['old'][$key] : ''}}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </thead>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- card -->
    <!-- download files table --
    </div>
      <!-- user seen table -->
    @endsection('content')