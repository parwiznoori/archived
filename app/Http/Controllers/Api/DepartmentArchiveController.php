<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentArchiveController extends Controller
{
    public function __invoke(Request $request, $university = null)
    {
        $departments = Department::leftJoin('department_types', 'department_types.id', '=', 'departments.department_type_id')
            ->leftJoin('faculties', 'faculties.id', '=', 'departments.faculty_id')
            ->select('departments.id', \DB::raw('CONCAT(departments.name, " [ پوهنځی : ", faculties.name , "] (", department_types.name,")") as text'))
            ->orderBy('departments.name');

        if ($university) {
            $departments->allUniversities()
                ->where('departments.university_id', $university->id);
        }

        if ($request->q != '') {
            $departments->where('departments.name', 'like', '%' . $request->q . '%');
        }

        return $departments->get();
    }
}
