<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMigrationData;
use Illuminate\Http\Request;
use SplFileObject;

class MigrationController extends Controller
{
    public function index()
    {
        return view('migration');
    }

    public function process(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:10240']);

        $csv = $request->file('file')->openFile('r');
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
        $header = null;
        $rows = [];

        foreach ($csv as $row) {
            if ($header === null) {
                $header = $row;
                continue;
            }

            $rows[] = $row;
            if (count($rows) === 1000) {
                ProcessMigrationData::dispatch($rows, $header);
                $rows = [];
            }
        }

        if ($rows !== []) {
            ProcessMigrationData::dispatch($rows, $header);
        }

        return back();
    }
}
