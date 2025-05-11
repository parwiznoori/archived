@extends('layouts.app')

@section('content')



    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session('row_errors'))
        <div class="alert alert-danger">
            <strong>{{ trans('general.validation_errors') }}</strong>
            <ul>
                @foreach (session('row_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <style>
        /* Custom table styling for better design */
        table {
            font-size: 14px;
            line-height: 1.6;
            width: 100%;
            margin-bottom: 20px;
        }

        .table th, .table td {
            padding: 12px;
            text-align: center;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .thead-dark {
            background-color: #0056b3;
            color: white;
        }

        .table-primary {
            background-color: #cce5ff;
            font-weight: bold;
        }

        /* Hide border for specific cells */
        .border-none {
            border: none !important;
        }

        /* Hover effect for the table rows */
        .table-hover tbody tr:hover {
            background-color: #f1f1f1 !important;
        }

        /* Optional: Subtle border effect for table rows */
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }

        /* Alignment of table content */
        .table th, .table td {
            vertical-align: middle;
        }

        /* Buttons */
        .btn-primary {
            background-color: #0056b3;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
        }

        .btn-primary:hover {
            background-color: #003d7a;
            color: white;
        }

        /* Print and template section */
        #printButton {
            background-color: #0056b3;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
        }

        #printButton:hover {
            background-color: #007bff;
        }

        #template {
            margin-top: 20px;
        }

        @media print {
            #printButton {
                display: none;
            }

            #template {
                display: none;
            }

            form {
                display: none;
            }
        }
    </style>

    <div class="portlet box">
        <div class="portlet-body">
            <!-- Table Section -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th>نام آرشیف</th>
                        <th>ID آرشیف</th>
                        <th>پوهنتون</th>
                        <th>ایدی پوهنتون</th>
                        <th>نام پوهنځی</th>
                        <th>ایدی پوهنځی</th>
                        <th>نام دیپارتمنت</th>
                        <th>ایدی دیپارتمنت</th>
                        <th>
                            <button type="submit" id="printButton" class="btn btn-primary">چاپ اطلاعات</button>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="table-primary">
                        <td>{{ $archive->book_name }}</td>
                        <td>{{ $archive->id }}</td>
                        <td>{{ $universities[$archive->university_id] ?? 'N/A' }}</td>
                        <td>{{ $archive->university_id }}</td>
                        <td colspan="4" class="text-center font-weight-bold">جزئیات پوهنځی و دیپارتمنت</td>
                    </tr>
                    @foreach ($archiveDepartments as $archiveDepartment)
                        <tr>
                            <td class="border-none"></td> <!-- Empty cell to align with the main archive row, no border -->
                            <td class="border-none"></td> <!-- Empty cell with no border -->
                            <td class="border-none"></td> <!-- Empty cell with no border -->
                            <td class="border-none"></td> <!-- Empty cell with no border -->
                            <td>{{ $archiveDepartment->faculty->name ?? 'N/A' }}</td>
                            <td>{{ $archiveDepartment->faculty_id }}</td>
                            <td>{{ $archiveDepartment->department->name ?? 'N/A' }}</td>
                            <td>{{ $archiveDepartment->department_id }}</td>
                        </tr>
                    @endforeach
                    @if($archiveImage)
                        <tr>
                            <td colspan="2" class="text-right  ">
                                <strong>تعداد صفحات کتاب:</strong> {{ $totalPages }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="2" class="text-right">
                                <strong>اطلاعات صفحه در دسترس نیست</strong>
                            </td>
                        </tr>
                    @endif


                    </tbody>
                </table>
            </div>







            <div class="p-6 bg-gray-50 rounded-lg shadow-md">
    <span class="text-gray-700">
        <b class="text-lg font-bold">
            <p>نوت:</p>
        </b>
        <p class="mt-2">برای فایل csv اکسل معلومات کالم های  <span class="font-semibold"> <code class="bg-gray-100 px-1 py-0.5 rounded bold">archive_id,university_id,faculty_id,department_id</code></span>از قسمت بالا کاپی کرده و درج فایل csv نمایید.</p>
        <p class="mt-2">در فایل csv اکسل کالم <span class="font-semibold"> <code class="bg-gray-100 px-1 py-0.5 rounded bold">archiveimage_id</code></span> نام صفحه را درج نمایید.</p>
        <p>در فایل csv اکسل کالم <span class="font-semibold"><code class="bg-gray-100 px-1 py-0.5 rounded bold">column_number</code></span> تعداد محصل در صفحه را به ترتیب درج نمایید. در صورتیکه صفحه محصل ندارد، صفر بگذارید.</p>
        <p>در کالم <span class="font-semibold"><code class="bg-gray-100 px-1 py-0.5 rounded bold">semester_type_id</code></span> برای سمستر بهاری <code class="bg-gray-100 px-1 py-0.5 rounded">1</code> و برای سمستر خزانی <code class="bg-gray-100 px-1 py-0.5 rounded">2</code> بنویسید.</p>
        <p>در کالم <span class="fontfont-semibold"><code class="bg-gray-100 px-1 py-0.5 rounded bold">grade_id</code></span> برای چهارده پاس <code class="bg-gray-100 px-1 py-0.5 rounded">1</code>، برای لیسانس <code class="bg-gray-100 px-1 py-0.5 rounded">2</code> و برای ماستر <code class="bg-gray-100 px-1 py-0.5 rounded">3</code> بنویسید.</p>
        <p>در اخیر هم قالب فایل csv را دانلود نموده خانه پری نماید و بعد از خانه پری دوباره اپلود کنید.</p>
    </span>
            </div>

            <!-- Download Button Section -->
            <div class="flex justify-center">
                <a href="{{ route('downloadTemplate') }}" class="btn btn-primary px-6 py-3 text-white bg-blue-500 hover:bg-blue-600 rounded-lg shadow">
                    🗂️ دانلود قالب فایل CSV
                </a>
            </div>


            <!-- Form Section -->
            {!! Form::open(['route' => 'archivedata.import', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">
                <hr>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('csv_file') ? 'has-error' : '' }}">
                            {!! Form::label('csv_file', trans('general.upload_csv'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::file('csv_file', ['class' => 'form-control']) !!}
                                @if ($errors->has('csv_file'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('csv_file') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden Fields -->
                {!! Form::hidden('university_id', $archive->university_id) !!}
                {!! Form::hidden('department_id', $archive->department_id) !!}
            </div>

            <!-- Submit and Cancel Buttons -->
            <br>
            <div class="form-actions">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <!-- Save Button -->
                        <button type="submit" class="btn btn-primary btn-lg mx-2">
                            {{ trans('general.save') }}
                        </button>

                        <!-- Cancel Button -->
                        <a href="{{ route('archive.index') }}" class="btn btn-secondary btn-lg mx-2">
                            {{ trans('general.cancel') }}
                        </a>

                        <!-- Undo Button -->
                        <a href="{{ route('import.undoLastUpload') }}" class="btn btn-danger btn-lg mx-2">
                            {{ trans('general.undo') }}
                        </a>
                    </div>
                </div>
            </div>

            <hr>
            {!! Form::close() !!}

        </div>
    </div>

    <script>
        // Function to handle the click event on the print button
        function handlePrintButtonClick() {
            window.print(); // This triggers the browser's print functionality
        }

        // Add event listener to the print button
        document.getElementById('printButton').addEventListener('click', handlePrintButtonClick);
    </script>

@endsection
