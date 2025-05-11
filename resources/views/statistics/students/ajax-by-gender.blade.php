<div class="portlet">
    <table class="table">
        <tr>
            <th> {{ trans('general.gender') }} </th>
            <th> {{ trans('general.students') }} </th>
           
            
        </tr>
        @foreach ($totalStudents as $student)
            <tr>
                <td> {{ $student->gender }} </td>
                <td> {{ $student->count_students }} </td>
            </tr>
        @endforeach
        
    </table>
</div>