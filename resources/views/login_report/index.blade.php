@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ trans('models/loginReport.fields.Select_Start_and_End_Dates')}}</h2>

    <!-- Form with start date and end date -->
    <form action="{{ route('login_report.showReport') }}" method="GET" class="form-inline mb-4">
        <!-- Start date input -->
        <div class="form-group me-3">
            <label for="from_login_at" class="me-2">{{ trans('models/loginReport.fields.from_login_at') }}</label>
            <input type="date" name="from_login_at" id="from_login_at" value="{{ request('from_login_at') }}"
                   class="form-control" />
        </div>

        <!-- End date input -->
        <div class="form-group me-3">
            <label for="to_login_at" class="me-2">{{ trans('models/loginReport.fields.to_login_at') }}</label>
            <input type="date" name="to_login_at" id="to_login_at" value="{{ request('to_login_at') }}"
                   class="form-control" />
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary">{{ trans('models/loginReport.fields.Show_Report') }}</button>
    </form>

    <!-- Display date difference and logs by day -->
    @if (isset($logsByDay))

    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>{{ trans('models/loginReport.fields.no') }}</th>
                <th>{{ trans('models/loginReport.fields.viewers') }}</th>
                <th>{{ trans('models/loginReport.fields.loginDate') }}</th>
                <th>{{ trans('models/loginReport.fields.name_of_days') }}</th>
            </tr>
        </thead>
        <tbody>
            @for ($m=0;$m<count($logsByDayData['date']);$m++)
                <tr>
                    <!-- Display auto-incremented index (counter) -->
                    <td>{{ $m + 1 }}</td>
                    <td>{{ $logsByDayData['count'][$m] }}</td>
                    <td>{{ $logsByDayData['date'] [$m]}}</td>
                    <td>{{ $logsByDayData['day_of_week'][$m] }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
    @endif
</div>
@endsection
