@extends('layouts.app')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="portlet box">
        <div class="portlet-body">



            <!-- BEGIN FORM -->
            {!! Form::open(['route' => ['archive_doc_type3', $docType->id], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">


                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('doc_type') ? ' has-error' : '' }}">
                            {!! Form::label('doc_type', trans('general.archive_doc_type'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::select('doc_type',['1' => 'دیپلوم', '2' => 'ترانسکریپت', '3' => 'حوض جاب'],$docType->doc_type,
                                    ['class' => 'form-control select2', 'placeholder' => trans('general.select')]
                                ) !!}
                                @if ($errors->has('doc_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doc_type') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('doc_number') ? ' has-error' : '' }}">
                            {!! Form::label('doc_number', trans('general.number'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('doc_number',  $docType->doc_number, ['class' => 'form-control ']) !!}
                                @if ($errors->has('doc_number'))
                                    <span class="help-block">
                                                 <strong>{{ $errors->first('doc_number') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('doc_file') ? 'has-error' : '' }}">
                            {!! Form::label('doc_file', trans('general.pdf'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::file('doc_file', ['class' => 'form-control']) !!}
                                @if ($errors->has('doc_file'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('doc_file') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('doc_description') ? ' has-error' : '' }}">
                            {!! Form::label('doc_description', trans('general.description'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('doc_description',  $docType->doc_description, ['class' => 'form-control ']) !!}
                                @if ($errors->has('doc_description'))
                                    <span class="help-block">
                                                 <strong>{{ $errors->first('doc_description') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Form Actions -->
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-12">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('archivedata.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>


            </div>
            <br>
            {!! Form::close() !!}
        <!-- END FORM -->



        </div>
    </div>

@endsection
