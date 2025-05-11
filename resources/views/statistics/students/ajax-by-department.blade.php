<div class="portlet">
    <table class="table">
        <tr>
            <th> {{ trans('general.number') }} </th>
            <th> {{ trans('general.university') }} </th>
            <th> {{ trans('general.department') }} </th>
            <th> {{ trans('general.students_kankor') }} </th>
            <th> {{ trans('general.leaves') }} </th>
            <th> {{ trans('general.dropouts') }} </th>
            <th> {{ trans('general.transfer_from') }} </th>
            <th> {{ trans('general.transfer_to') }} </th>
        </tr>
        @php
            $i=0;
        @endphp
        @foreach ($students as $s)
           
            <tr>
                <td> {{ ++$i  }} </td>
                <td> {{ $s['name']  }} </td>
                <td> {{ $s['department_name']  }} </td>
                <td> {{ $s['totalStudents'] }} </td>
                <td> {{ $s['totalStudentsWithLeave']  }} </td>
                <td> {{ $s['totalStudentsWithDroupout'] }} </td>
                <td> {{ $s['totalStudentsWithTransferFrom']  }} </td>
                <td> {{ $s['totalStudentsWithTransferTo']  }} </td>
            </tr>
            
        @endforeach
        
    </table>
</div>
