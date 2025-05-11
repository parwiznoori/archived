@extends('layouts.app') {{-- Assuming you have a master layout --}}

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $title }}</div>
                    <div class="panel-body">
                        {!! Form::model($archiveimage, ['route' => ['archiveimage.update', $archiveimage->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
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
                            </div>

                            <br>
                            <div class="form-actions fluid">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-8">
                                        <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                                        <a href="{{ route('archiveimage.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
