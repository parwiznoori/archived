<div class="portlet">
    <table class="table">
        <tr>
            <th> {{ trans('general.university') }} </th>
            <th> {{ trans('general.students') }} </th>
           
            
        </tr>
        @foreach ($totalStudents as $student)
            <tr>
                <td> {{ $student->name }} </td>
                <td> {{ $student->count_students }} </td>
            </tr>
        @endforeach
        
    </table>
</div>