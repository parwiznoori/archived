@extends('layouts.app')

@section('content')

    <h3>{{ trans('لیست وظایف یوزر آرشیف') }}</h3>

    <a href="{{ route('archiverole.create') }}" class="btn btn-primary">{{ trans('وظایف جدید') }}</a>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <br><br>

    <div class="portlet box">
        <div class="portlet-body">

            <!-- Search Bar -->
            <style>
                .search-container {
                    display: flex;
                    align-items: center;
                    margin-bottom: 1rem;
                }

                .search-container input[type="text"] {
                    width: 33.33%; /* 4 columns = 1/3 of 12-column grid */
                    padding: 10px;
                    border: 1px solid #ccc;
                    border-radius: 4px 0 0 4px;
                    outline: none;
                }

                .search-container button {
                    padding: 10px 20px;
                    border: none;
                    background-color: #007bff;
                    color: white;
                    border-radius: 0 4px 4px 0;
                    cursor: pointer;
                }

                .search-container button:hover {
                    background-color: #0056b3;
                }
            </style>

            <form method="GET" action="{{ route('archiverole.index') }}" class="search-container">
                <input
                        type="text"
                        name="search"
                        placeholder="{{ trans('جستجو در وظایف') }}"
                        value="{{ request('search') }}">
                <button type="submit">{{ trans('جستجو') }}</button>
            </form>

            <hr>
            <!-- Table -->
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>{{ trans('general.id') }}</th>
                    <th>{{ trans('general.user_name') }}</th>
                    <th>{{ trans('general.book_name') }}</th>
                    <th>{{ trans('general.university') }}</th>
                    <th>{{ trans('general.role') }}</th>
                    <th>{{ trans('general.status') }}</th>
                    <th>{{ trans('general.accept_or_refuse') }}</th>
                    <th>{{ trans('general.date') }}</th>
                    {{-- <th>{{ trans('general.actions') }}</th> --}}
                </tr>
                </thead>
                <tbody>
                @foreach ($archiveRoles as $archiveRole)
                    <tr>
                        <td>{{ $archiveRole->id ?? 'N/A' }}</td>
                        <td>{{ $archiveRole->user->name ?? 'N/A' }}</td>
                        <td>{{ $archiveRole->archive->book_name ?? 'N/A' }}</td>
                        <td>{{ $archiveRole->archive->university->name ?? 'N/A' }}</td>
                        <td>{{ $archiveRole->role->title ?? 'N/A' }}</td>
                        <td>
                            @if($archiveRole->archive->status_id ?? false)
                                @switch($archiveRole->archive->status_id)
                                    @case(1)
                                        <span>حالت معمولی</span>
                                        @break
                                    @case(2)
                                        <span class="badge-primary m-1 p-1">صرف نظر</span>
                                        @break
                                    @case(3)
                                        <span class="badge-warning m-1 p-1">ناتکمیل</span>
                                        @break
                                    @case(4)
                                        <span class="badge-success m-1 p-1">تکمیل</span>
                                        @break
                                    @default
                                        <span>N/A</span>
                                @endswitch
                            @endif
                        </td>
                        
                        <td>
                            @if($archiveRole->archive->qc_status_id ?? false)
                                @switch($archiveRole->archive->qc_status_id)
                                    @case(1)
                                        <span>حالت معمولی</span>
                                        @break
                                    @case(2)
                                        <span class="badge-primary m-1 p-1">پروسس</span>
                                        @break
                                    @case(3)
                                        <span class="badge-success m-1 p-1">تايید</span>
                                        @break
                                    @case(4)
                                        <span class="badge-danger m-1 p-1">رد</span>
                                        @break
                                    @default
                                        <span>N/A</span>
                                @endswitch
                            @endif
                        </td>
                        
                        <td>{{ $archiveRole->created_at ? $archiveRole->created_at->diffForHumans() : 'N/A' }}</td>

                        <td>
                            <a href="{{ route('archiverole.edit', $archiveRole->id) }}" class="btn btn-warning btn-sm">{{ trans('general.edit') }}</a>


                           {{-- <form action="{{ route('archiverole.destroy', $archiveRole->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این وظیفه را حذف کنید؟');">
                               @csrf
                               @method('DELETE')
                               <button type="submit" class="btn btn-danger btn-sm">{{ trans('general.delete') }}</button>
                           </form> --}}

                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            {{ $archiveRoles->links() }}
        </div>
    </div>
@endsection
