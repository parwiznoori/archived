@extends('layouts.app')

@section('content')
 <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> 
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<div class="portlet box">
    <div class="portlet-body">
        <div class="panel-body">
            <div class="container-fluid">
                <!-- Success Message -->
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <!-- Edit Form Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h3 class="m-0 font-weight-bold">{{ trans('general.edit_monograph') }}</h3>
                    </div>
                    
                    <div class="card-body">
                        {!! Form::model($archivedata, [
                        'route' => ['archive_monograph_update', $archivedata->id],
                        'method' => 'POST',
                        'enctype' => 'multipart/form-data',
                        'class' => 'form-horizontal'
                        ]) !!}
                        @csrf
                        
                        <div class="row">
                            <!-- Left Column - Basic Information -->
                            <div class="col-lg-6">
                                <div class="card mb-4 border-left-primary">
                                    
                                    <div class="card-body">
                                        @foreach ([
                                            'monograph_date' => 'date',
                                            'monograph_number' => 'text',
                                            'monograph_title' => 'text',
                                            'monograph_doc_date' => 'date',
                                            'monograph_doc_number' => 'text'
                                        ] as $field => $type)
                                            <div class="form-group row mb-4 {{ $errors->has($field) ? 'has-error' : '' }}">
                                                {!! Form::label($field, trans("general.$field"), ['class' => 'col-md-4 col-form-label font-weight-bold']) !!}
                                                <div class="col-md-8">
                                                    @if($type === 'date')
                                                    <div class="input-group">
                                                        {!! Form::text($field, null, [
                                                            'class' => 'form-control persian-date-input',
                                                            'autocomplete' => 'off'
                                                        ]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text bg-white">
                                                                <i class="far fa-calendar-alt text-primary"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @else
                                                        {!! Form::text($field, null, ['class' => 'form-control']) !!}
                                                    @endif
                                                    @if ($errors->has($field))
                                                        <span class="text-danger small">{{ $errors->first($field) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column - Additional Details -->
                            <div class="col-lg-6">
                                <div class="card mb-4 border-left-success">
                                    <div class="card-header bg-light">
                                    </div>
                                    <div class="card-body">
                                        <!-- Description Field -->
                                        <div class="form-group row mb-4 {{ $errors->has('monograph_desc') ? 'has-error' : '' }}">
                                            {{-- {!! Form::label('monograph_desc', trans('general.monograph_desc'), ['class' => 'col-md-4 col-form-label font-weight-bold']) !!}
                                            <div class="col-md-8">
                                                {!! Form::textarea('monograph_desc', null, [
                                                    'class' => 'form-control',
                                                    'rows' => 4,
                                                   
                                                ]) !!}
                                                @if ($errors->has('monograph_desc'))
                                                    <span class="text-danger small">{{ $errors->first('monograph_desc') }}</span>
                                                @endif
                                            </div> --}}
                                        </div>
                                        
                                        <!-- Image Upload -->
                                        <div class="form-group row {{ $errors->has('monograph_img') ? 'has-error' : '' }}">
                                            {!! Form::label('monograph_img', trans('general.monograph_img'), ['class' => 'col-md-4 col-form-label font-weight-bold']) !!}
                                            <div class="col-md-8">
                                                <div class="custom-file">
                                                    {!! Form::file('monograph_img', [
                                                        'class' => 'custom-file-input',
                                                        'id' => 'monographImageUpload'
                                                    ]) !!}
                                                    <label class="custom-file-label" for="monographImageUpload">
                                                        
                                                    </label>
                                                </div>
                                            
                                                @if ($errors->has('monograph_img'))
                                                    <span class="text-danger small">{{ $errors->first('monograph_img') }}</span>
                                                @endif
                                                
                                                <!-- Image Preview -->
                                                @if ($archivedata->monograph_img)
                                                
                                                    <img src="{{ asset($archivedata->monograph_img) }}" 
                                                        
                                                        style="max-height: 70px;"
                                                        alt="Monograph Image">
                                                    
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="form-group row mt-4">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary px-4">
                                   {{ trans('general.save') }}
                                </button>
                                <a href="{{ route('archivedata.index') }}" class="btn btn-outline-secondary px-4 ml-2">
                                    {{ trans('general.cancel') }}
                                </a>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                
                <!-- Preview Section -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h5 class="m-0 font-weight-bold">
                            
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>{{ trans('general.monograph_date') }}</th>
                                        <th>{{ trans('general.monograph_number') }}</th>
                                        <th>{{ trans('general.monograph_title') }}</th>
                                        <th>{{ trans('general.monograph_doc_date') }}</th>
                                        <th>{{ trans('general.monograph_doc_number') }}</th>
                                        {{-- <th>{{ trans('general.monograph_desc') }}</th> --}}
                                        <th class="text-center">{{ trans('general.monograph_img') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $archivedata->monograph_date }}</td>
                                        <td>{{ $archivedata->monograph_number }}</td>
                                        <td>{{ $archivedata->monograph_title }}</td>
                                        <td>{{ $archivedata->monograph_doc_date }}</td>
                                        <td>{{ $archivedata->monograph_doc_number }}</td>
                                        {{-- <td>{{ $archivedata->monograph_desc ?: 'N/A' }}</td> --}}
                                        <td class="text-center">
                                            @if ($archivedata->monograph_img)
                                                <a href="{{ asset($archivedata->monograph_img) }}" 
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary"
                                                data-toggle="tooltip" 
                                                title="{{ trans('general.view_full_size') }}">
                                                     {{ trans('general.view') }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .form-control, .custom-select, .custom-file-input {
        border-radius: 0.35rem;
    }
    .input-group-text {
        background-color: #f8f9fa;
    }
    .table th {
        white-space: nowrap;
    }
    .img-fluid {
        max-width: 100%;
        height: auto;
    }
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Update file input label
    document.getElementById('monographImageUpload').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || '{{ trans("general.choose_file") }}';
      
        
    });

    // Initialize Persian datepicker
    $(document).ready(function() {
        $('.persian-date-input').persianDatepicker({
            months: ["حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت"],
            dowTitle: ["شنبه", "یکشنبه", "دوشنبه", "سه شنبه", "چهارشنبه", "پنج شنبه", "جمعه"],
            showGregorianDate: false,
            persianNumbers: true,
            formatDate: "YYYY/MM/DD",
            isRTL: true,
            observer: true,
        });

        // Enable tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush