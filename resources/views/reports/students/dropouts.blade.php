<table>
    <thead>
    <tr>
        <th colspan="22">{{trans('general.dropouts_list')}} </th>

    </tr>
    <tr>
        <th>{{trans('general.number')}}</th>
        <th>{{trans('general.form_no')}}</th>
        <th>{{trans('general.name')}}</th>
        <th>{{trans('general.last_name')}}</th>
        <th>{{trans('general.father_name')}}</th>
        <th>{{trans('general.grandfather_name')}}</th>
        <th>{{trans('general.phone')}}</th>
        <th>{{trans('general.email')}}</th>
        <th>{{trans('general.gender')}}</th>
        <th>{{trans('general.kankor_result')}}</th>
        <th>{{trans('general.province')}}</th>
        <th>{{trans('general.kankor_year')}}</th>
        <th>{{trans('general.school_name')}}</th>
        <th>{{trans('general.school_graduation_year')}}</th>
        <th>{{trans('general.department')}}</th>
        <th>{{trans('general.university')}}</th>
        <th>{{trans('general.grade')}}</th> 
        <th>{{trans('general.status')}}</th> 

        <th>{{trans('general.dropout_year')}}</th>
        <th>{{trans('general.semester')}}</th>
        <th>{{trans('general.approved_dropout')}}</th>
        <th>{{trans('general.note')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($students as $student)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $student->form_no }}</td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->last_name }}</td>
            <td>{{ $student->father_name }}</td>
            <td>{{ $student->grandfather_name }}</td>
            <td>{{ $student->phone }}</td>
            <td>{{ $student->email }}</td>
            <td>{{ $student->gender == "Male" ? trans('general.Male') : trans('general.Female') }}</td>
            <td>{{ $student->kankor_result }}</td>
            <td>{{ $student->province }}</td>
            <td>{{ $student->kankor_year }}</td>
            <td>{{ $student->school_name }}</td>
            <td>{{ $student->school_graduation_year }}</td>
            <td>{{ $student->department }}</td>
            <td>{{ $student->university }}</td>
            <td>{{ $student->grade }}</td>
            <td>{{ $student->status }}</td>

            <td>{{ $student->year }}</td>
            <td>{{ $student->semester }}</td>
            <td>{{ $student->approved == 1 ?  trans('general.yes') : trans('general.no') }}</td>
            <td>{{ $student->note }}</td>
        </tr>
    @endforeach
    </tbody>
</table>