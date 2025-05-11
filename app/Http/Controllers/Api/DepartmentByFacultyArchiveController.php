<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentByFacultyArchiveController extends Controller
{
    public function __invoke(Request $request)
    {
        \Log::info('Request Data:', $request->all());

        $query = Department::leftJoin('department_types', 'department_types.id', '=', 'departments.department_type_id')
            ->leftJoin('faculties', 'faculties.id', '=', 'departments.faculty_id')
            ->select(
                'departments.id',
                \DB::raw('CONCAT(departments.name, " [ پوهنځی : ", faculties.name , "] (", department_types.name,")") as text')
            )
            ->orderBy('departments.name');

        if ($request->university_id) {
            $query->where('departments.university_id', $request->university_id);
        }

        if ($request->faculty_id) {
            $query->where('departments.faculty_id', $request->faculty_id);
        }

        if ($request->q) {
            $query->where('departments.name', 'like', '%' . $request->q . '%');
        }

        return $query->get();
    }

}
