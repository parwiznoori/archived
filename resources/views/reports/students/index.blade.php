@extends('layouts.app')

@section('content')
<div class="portlet box col-lg-12">        
    <div class="portlet-body">
        <!-- BEGIN FORM-->            
        {!! Form::open(['route' => 'report.student.create', 'method' => 'post', 'class' => 'form-horizontal' , 'target' => 'new']) !!}
            <div class="form-body" id="app">
                <br>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('kankor_year') ? ' has-error' : '' }}">
                            {!! Form::label('kankor_year', trans('general.year'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::select('kankor_year', $kankor_years, $kankorYear, ['class' => 'form-control select2 ', 'placeholder' => trans('general.select')]) !!}
                                @if ($errors->has('kankor_year'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('kankor_year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('province') ? ' has-error' : '' }}">
                            {!! Form::label('province', trans('general.province'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::select('province', $provinces, null, ['class' => 'form-control select2 ' ,'placeholder' => trans('general.select')]) !!}
                                @if ($errors->has('province'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('province') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @if(auth()->user()->allUniversities())
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('university') ? ' has-error' : '' }}">
                                {!! Form::label('university', trans('general.university'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('university', $universities, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                    @if ($errors->has('university'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('university') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('university') ? ' has-error' : '' }}">
                                {!! Form::label('university', trans('general.university'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('university', $universities, null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('university'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('university') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    
                    @endif
                    
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('grade') ? ' has-error' : '' }}">
                            {!! Form::label('grade', trans('general.grade'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::select('grade', $grades, null, ['class' => 'form-control select2 ', 'placeholder' => trans('general.select')]) !!}
                                @if ($errors->has('grade'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('grade') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    @if(auth()->user()->allUniversities())
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('department') ? ' has-error' : '' }}">
                                {!! Form::label('department', trans('general.department'), ['class' => 'control-label col-md-3']) !!}                                
                                <div class="col-md-8">
                                    {!! Form::select('department', $department, null, ['class' => 'form-control select2-ajax', 'remote-url' => route('api.departments'), 'remote-param' => '[name="university"]']) !!}
                                    @if ($errors->has('department'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('department') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('department') ? ' has-error' : '' }}">
                                {!! Form::label('department', trans('general.department'), ['class' => 'control-label col-md-3']) !!}                                
                                <div class="col-md-8">
                                    {!! Form::select('department', $department, null, ['class' => 'form-control select2']) !!}
                                    @if ($errors->has('department'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('department') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>
                        </div>
                    @endif     
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                            {!! Form::label('gender', trans('general.gender'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::select('gender', ['male' => trans('general.Male'),  'female' => trans('general.Female')], null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                @if ($errors->has('gender'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                            {!! Form::label('status', trans('general.status'), ['class' => 'control-label col-md-3']) !!}                                
                            <div class="col-md-8">
                                {!! Form::select('status', $statuses, null, ['class' => 'form-control ']) !!}
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                @endif                                                                                                   
                            </div>
                        </div>
                    </div>
                   
                    <div class="col-md-6">
                       
                    </div>
                </div>

            </div>
            <hr>
            <div class="form-actions fluid">
                <div class="row">
                    <div class="col-md-offset-1 col-md-12">
                        <button type="submit" class="btn green">{{ trans('general.generate_report') }}</button>
                        <a href="{{ route('noticeboard') }}" class="btn default">{{ trans('general.cancel') }}</a>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
        <!-- END FORM-->
    </div>
</div>

<div class="row " >
    <div class="col-md-12">
        <div class="ajax-loader">
            <img src="{{ url('img/Spinner.gif') }}" class="img-responsive" />
            <br>
            <p class="loading">
                {{ trans("general.please_wait") }}
            </p>
            
        </div>
    </div>
   
</div>

<div class="row" >
    <div class="col-md-12" id="statistics_data">
       
    </div>
</div>

@endsection('content')

@push('styles')
<style type="text/css">
    .ajax-loader {
        visibility: hidden;
        background-color: rgba(255,255,255,0.7);
        position: fixed;
        text-align: center;
        vertical-align: middle;
        top: 0;
        left: 0;
        z-index: +1000 !important;
        height:100%;
        width: 100%;
        /* width: 80%; */
        /* background-color: rgb(10, 9, 9); */
    }

    .ajax-loader img {
        position: fixed;
        top:50%;
        left:50%;
        width: 100px;
        height: 100px;
        text-align: center;
        
    }
    .loading
    {
        position: fixed;
        top:55%;
        left:45%;
        padding-top:30px; 
        text-align: center;
        font-size: 18px;
        font-weight: bolder;

    }
</style>

@endpush

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function dowload_report_by_all()
    {
        $.ajax({
            type:'POST',
            beforeSend: function(){
                $('.ajax-loader').css("visibility", "visible");
            },
            url:"{{ route('report.student.all') }}",
            data:{ 
                university: document.getElementById('university').value,
                department: document.getElementById('department').value,
                kankor_year: document.getElementById('kankor_year').value,
                province: document.getElementById('province').value,
                grade: document.getElementById('grade').value,
                gender: document.getElementById('gender').value,
            },
            success:function(data){
                document.getElementById('statistics_data').innerHTML=data;
            },
            complete: function(){
                $('.ajax-loader').css("visibility", "hidden");
            }
        });   
    }
</script>
@endpush