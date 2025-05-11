@extends('layouts.app')

@section('content')
    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="panel-body">        
               




                {!! Form::open(['route' => 'archiveimage.store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
                <div class="form-body" id="app">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('archive_id') ? ' has-error' : '' }}">
                                {!! Form::label('archive_id', trans('general.book_name'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::select('archive_id', $archives, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                    @if ($errors->has('archive_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('archive_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('path[]') ? ' has-error' : '' }}">
                                {!! Form::label('path', trans('general.photo'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::file('path[]', ['class' => 'form-control', 'multiple' => 'multiple']) !!}
                                    @if ($errors->has('path'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('path') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
            
                
            
                  
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('book_pagenumber') ? ' has-error' : '' }}">
                                    {!! Form::label('book_pagenumber', trans('general.book_pagenumber'), ['class' => 'control-label col-sm-3']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('book_pagenumber', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('book_pagenumber'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('book_pagenumber') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                                {!! Form::label('type', trans('general.type'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::select('type', ['information' => trans('general.archive'),'temporary' => trans('general.temporary'),], null, ['class' => 'form-control',
                                     'placeholder' => trans('general.select')]) !!}
                                    @if ($errors->has('type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div> 
                       </div> --}}

                   

                       {{-- <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                        {!! Form::label('status', trans('general.status'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">                 
                            <div >
                                <label class="checkbox-inline">
                                <input type="checkbox" name="status" value="1" checked> {{ trans('general.status') }}                               
                                </label>                                                       
                            </div>                                               
                        </div>
                    </div> --}}

                </div>
                <br>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-2 col-md-8">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('archiveimage.index') }}"
                               class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            
        </div>
    </div>










@endsection('content')

