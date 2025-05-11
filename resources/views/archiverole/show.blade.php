@extends('layouts.app')

@section('content')

    <style>
        /* Custom Styling */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 2.5rem;
        }

        .text-center h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #333;
        }

        .table {
            width: 100%;
            margin-top: 1.5rem;
            border-collapse: separate;
            border-spacing: 0;
        }

      
        .btn-secondary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 50px;
            border: none;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #0056b3, #003d80);
            transform: translateY(-2px);
        }

        /* Responsive Design for smaller screens */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }

            .text-center h1 {
                font-size: 2rem;
            }

            .table th, .table td {
                font-size: 0.875rem;
                padding: 12px;
            }

            .btn-secondary {
                width: 100%;
                padding: 12px 0;
            }

            /* Make the table horizontally scrollable on small screens */
            .table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>

    <div class="portlet box">
        <div class="portlet-body">
            
                <!-- Role Details Box -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Page Title -->
                        <h1 class="text-center">{{ trans('general.roles') }}</h1>

                        <!-- Role Details Table -->
                        <div class="table-wrapper">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ trans('general.user_name') }}</th>
                                        <th>{{ trans('general.role') }}</th>
                                        <th>{{ trans('general.book_name') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $role->user->name ?? 'N/A' }}</td>
                                        <td>{{ $role->role->name ?? 'N/A' }}</td>
                                        <td>{{ $role->archive->book_name ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Back Button -->
                        <div class="text-center mt-4">
                            <a href="{{ route('archivedata.index') }}" class="btn btn-secondary">
                                {{ trans('general.cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection