@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet">                   
            <div class="portlet-body">
                @if (session('message'))
                    <script src="{{ asset('sweetalert2/sweetalert2.all.min.js') }}"></script>
                    <script>
                        Swal.fire('{{ session("message") }}')
                    </script>
                @endif
                
                {!! Form::open(['route' => 'kankor_results.store_department', 'method' => 'post', 'class' => 'form-horizontal']) !!}
                <div class="form-body" id="app">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('kankor_year') ? ' has-error' : '' }}">
                                {!! Form::label('kankor_year', trans('general.year'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-9">
                                    {!! Form::hidden('kankor_year', $kankorYear) !!}
                                    {{ $kankorYear }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">   
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('kankorResults') ? ' has-error' : '' }}">
                                {!! Form::label('kankorResults', trans('general.kankorResults'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('kankorResults', $kankorResults, null, ['class' => 'form-control select2', 'required']) !!}
                                    @if ($errors->has('kankorResults'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('kankorResults') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('archive_id') ? ' has-error' : '' }}">
                                {!! Form::label('archive_id', trans('general.archive'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    @if(auth()->user()->allUniversities())
                                        {!! Form::select('archive_id', $archives, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select'), 'required']) !!}
                                        @if ($errors->has('archive_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('archive_id') }}</strong>
                                            </span>
                                        @endif
                                    @else
                                        {!! Form::hidden('archive_id', auth()->user()->archive_id) !!}
                                        {{ auth()->user()->archive->book_name ?? '' }}
                                    @endif
                                </div>
                            </div>
                        </div>  
                    </div>
                    
                      <!-- University Selection -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('university_id') ? ' has-error' : '' }}">
                            {!! Form::label('university_id', trans('general.university'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('university_id', $universities, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => trans('general.select')
                                ]) !!}
                                @if ($errors->has('university_id'))
                                    <span class="help-block">
                            <strong>{{ $errors->first('university_id') }}</strong>
                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                        <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('department_id') ? ' has-error' : '' }}">
                            {!! Form::label('department_id', trans('general.department'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('department_id[]', $department, null, [
                                    'class' => 'form-control select2-ajax',
                                    'remote-url' => route('api.departmentArchive'),
                                    'remote-param' => '[name="university_id"]'
                                ]) !!}
                                @if ($errors->has('department_id'))
                                    <span class="help-block">
                            <strong>{{ $errors->first('department_id') }}</strong>
                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                   
                    <hr>
                    <div class="form-actions fluid">
                        <div class="row">
                            <div class="col-md-offset-1 col-md-12">
                                <button type="submit" class="btn green">{{ trans('general.update_department_id') }}</button>
                                <a href="{{ route('noticeboard') }}" class="btn default">{{ trans('general.cancel') }}</a>
                            </div>
                        </div>
                    </div>
                    <br>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection