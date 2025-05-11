@extends("layouts.app")
@section('content')

<div class="portlet">
    <!-- Form for displaying login report -->
    <form action="{{ route('login_report.showReport') }}" method="POST">
        <!-- Include CSRF token for security when using POST method -->
        @csrf

        <table class="table">
            <tr>
                <th>{{ trans('models/loginReport.fields.no') }}</th>
                <th>{{ trans('models/loginReport.fields.viewers') }}</th>
                <th>{{ trans('models/loginReport.fields.loginDate') }}</th>
                <th>{{ trans('models/loginReport.fields.name_of_days') }}</th>
            </tr>
            
            <!-- Iterate over authenticationLogs data -->
            @foreach ($authenticationLogs as $a)
                <tr>
                    <td>{{ $a[''] }}</td>
                    <td>{{ $a['login_at'] }}</td>
                    <td>{{ $a['number_of_users'] }}</td>
                    <td>{{ $a['totalStudentsWithDroupout'] }}</td>
                </tr>
            @endforeach
        </table>
    </form>
</div>

@endsection
