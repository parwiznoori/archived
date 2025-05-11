@extends('layouts.app')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="portlet box">
        <div class="portlet-body">


            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                <tr>
{{--                    <th>آدی محصل</th>--}}
                    <th>نام محصل</th>
                </tr>
                </thead>
                <tbody>
                <tr>
{{--                    <td>{{ $archiveId }}</td>--}}
                    <td>{{ $archivedataName }}</td>
                </tr>
                </tbody>
            </table>
<hr>


            <!-- BEGIN FORM -->
            {!! Form::open(['route' => 'archive_doc_type2', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
                        <div class="form-body" id="app">

                            <input type="hidden" value="{{$archiveId}}" name="archiveDataId"/>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group {{ $errors->has('doc_type') ? ' has-error' : '' }}">
                                            {!! Form::label('doc_type', trans('general.archive_doc_type'), ['class' => 'control-label col-md-3']) !!}
                                            <div class="col-md-8">
                                                {!! Form::select('doc_type', ['1' => 'دیپلوم', '2' => 'ترانسکریپت', '3' => 'حوض جاب '], null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                                @if ($errors->has('doc_type'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('doc_type') }}</strong>
                                                </span>
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
                                            {!! Form::text('doc_number', null, ['class' => 'form-control ']) !!}
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
                                            {!! Form::text('doc_description', null, ['class' => 'form-control ']) !!}
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

<hr>

            <!-- Display Archived Documents -->
            <h1>لیست اسناد محصلین</h1>

            @php
                $docTypeNames = [
                    '1' => 'دیپلوم',
                    '2' => 'ترانسکریپت',
                    '3' => 'حوض جاب',
                ];
            @endphp
            <table class="table table-bordered">
                <thead>
                <tr>

                    <th>نوع سند </th>
                    <th>شماره سند</th>
                    <th>توضیحات</th>
                    <th>فایل پی دی اف</th>
                    <th>تصحیح</th>
                    <th>حذف</th>
                </tr>
                </thead>
                <tbody>

                @if ($archiveDocType)
                @foreach ($archiveDocType as $doc)
                    <tr>
                        <td>{{ $docTypeNames[$doc->doc_type] ?? $doc->doc_type }}</td>
                        <td>{{ $doc->doc_number }}</td>
                        <td>{{ $doc->doc_description }}</td>
                        <td><a href="{{ asset($doc->doc_file) }}" target="_blank">مشاهده/دانلود</a></td>
                        <td>
                            <a href="{{ route('archive_doc_type3', $doc->id) }}" class="btn btn-primary" target="_blank">تصحیح</a>
                        </td>
                        <td>
                            <form action="{{ route('archive_doc_type3.destroy', $doc->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('آیا میخواهید این ریکار را حذف نمایید؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @endif
                </tbody>
            </table>

        </div>
    </div>

@endsection
