@extends('layouts.app')

@section('content')
    <style>
        #textarea {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #333;
            margin-top: 10px;
            min-height: 150px;
            overflow-y: auto;
        }

        .form-select {
            margin-bottom: 15px;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>

    @if(isset($success))
        <div class="alert alert-success">
            {{ $success }}
        </div>
    @endif


    <div class="portlet box">
        <div class="portlet-body">

            <!-- BEGIN FORM -->
            {!! Form::open(['route' => ['archive_form_print2', $archivedataid], 'method' => 'post', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">


                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('archive_form_temp_id') ? 'has-error' : '' }}">
                            {!! Form::label('archive_form_temp_id', trans('general.archive_doc_form_type'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8 required">
                                {!! Form::select(
                                    'archive_form_temp_id',
                                    $archiveFormTypes->pluck('form_name', 'id'),
                                    null,
                                    ['class' => 'form-control select2', 'placeholder' => trans('general.select'), 'onchange' => "loadContent(this.value)"]
                                ) !!}
                                @error('archive_form_temp_id')
                                <span class="help-block">
                                   <strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>






                <textarea style="display:none!important" class="d-none " name="text" id="text"></textarea>


                <div id="textarea"  class="mt-4 ">

                </div>


                <hr>
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


            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ادی</th>
                    <th>نوع مکتوب</th>
                    <th>پرنت</th>
                    <th>حذف</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($archiveFormPrint as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->form_name ?? 'No Form Name' }}</td> <!-- Display the form name -->
                        <td>
                            <!-- Print Content Button (links to the print view route) -->
                            <a href="{{route('print-archivedocform', $item->id)}}" class="btn btn-info">
                                چاپ مکتوب
                            </a>
                        </td>
                        <td>
                            <!-- Delete Button -->
                            <form action="{{ route('archive_form_print.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

        </div>
    </div>

@endsection

@push('scripts')

    <script>
        function loadContent(selectedValue) {
            let archiveFormTypes = @json($archiveFormTypes);

            // Find the selected record
            let record = archiveFormTypes.find(e =>e.id == selectedValue);

            const arr = record.variable.split(",");
            let archivedatas = @json($archivedataid);

            arr.forEach((item, index) => {
                record.content = record.content.replace("$", archivedatas[item]);
                console.log(record.content);
            });


            // Display the content inside the #textarea div
            if (record && record.content) {
                document.getElementById("textarea").innerHTML = record.content;
                document.getElementById("text").value = record.content;
            } else {
                document.getElementById("textarea").innerHTML = "<em>No content available for the selected template.</em>";
            }


            console.log(archivedatas);

        }

    </script>
@endpush