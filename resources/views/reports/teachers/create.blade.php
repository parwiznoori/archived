<table>
    <thead>
    <tr>
        <th>{{trans('general.number')}}</th>
        <th>{{trans('general.id')}}</th>
        <th>{{trans('general.name')}}</th>
        <th>{{trans('general.last_name')}}</th>
        <th>{{trans('general.father_name')}}</th>
        <th>{{trans('general.grandfather_name')}}</th>
        <th>{{trans('general.email')}}</th>
        <th>{{trans('general.phone')}}</th>
        <th>{{trans('general.marital_status')}}</th>
        <th>{{trans('general.birthdate')}}</th>
        <th>{{trans('general.gender')}}</th>
        <th>{{trans('general.province')}}</th>
        <th>{{trans('general.academic_rank')}}</th>
        <th>{{trans('general.department')}}</th>
        <th>{{trans('general.university')}}</th>
        <th>{{trans('general.degree')}}</th> 
        <th>{{trans('general.type')}}</th> 
        <th>{{trans('general.active')}}</th> 
    </tr>
    </thead>
    <tbody>
    @foreach($teachers as $teacher)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $teacher->id }}</td>
            <td>{{ $teacher->name }}</td>
            <td>{{ $teacher->last_name }}</td>
            <td>{{ $teacher->father_name }}</td>
            <td>{{ $teacher->grandfather_name }}</td>
            <td>{{ $teacher->email }}</td>
            <td>{{ $teacher->phone }}</td>
            <td>{{ $teacher->marital_status == "married" ? trans('general.married') : trans('general.single') }}</td>
            <td>{{ $teacher->birthdate }}</td>
            <td>{{ $teacher->gender == "Male" ? trans('general.Male') : trans('general.Female') }}</td>
            <td>{{ $teacher->province }}</td>
            <td>{{ $teacher->teacher_acadaemic_rank }}</td>
            <td>{{ $teacher->department }}</td>
            <td>{{ $teacher->university }}</td>
            <td>{{ $teacher->degree == "bachelor" ? ( trans('general.bachelor'))  :  ($teacher->degree == "master" ? ( trans('general.master'))  :  trans('general.doctor') ) }}</td>
            <td>{{ $teacher->type == "permanent" ? ( trans('general.permanent')) : ( $teacher->type == "temporary" ? ( trans('general.temporary'))  :  trans('general.honorary') ) }}</td>
            <td>{{ $teacher->active == 1 ? trans('general.active') : trans('general.inactive') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>