<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\Archiveimage;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\University;
use DB;
use Illuminate\Http\Request;
use PDF;

class ArchivedocController extends Controller
{
    public function __construct()
    {
        // Apply the permission middleware to ensure the user has 'print-archivedoc' permission
        $this->middleware('permission:print-archivedoc', ['only' => ['index']]);
    }

    /**
     * Print PDF for a student's diploma based on their archived data
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        // Retrieve the Archive and Archivedata models by the provided ID
        $archive = Archive::findOrFail($id);
        $archivedata = Archivedata::findOrFail($id);

        // Load the view and generate a PDF in A4 portrait format
        $pdf = PDF::loadView('archivedata.archivedoc-pa', compact('archive', 'archivedata'), [], [
            'mode'                   => 'utf-8',
            'format'          => 'A4-P', // Portrait orientation
            'default_font'    => 'times-new-roman', // Set the default font
            'custom_font_dir' => base_path('resources/fonts/'), // Custom font directory
            'custom_font_data' => [
                'times-new-roman' => [ // Define custom font
                    'R'  => 'times.ttf', // Regular font file
                    'B'  => 'timesbd.ttf',    // Bold font file (optional)
                    'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
			        'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
			        'unAGlyphs' => true
                ],
            ],
        ]);




        // Stream the PDF to the browser with a dynamic filename
        return $pdf->stream('Archivedata-' . $archivedata->id . '.pdf');
    }

    public function fdoc($id)
    {
        // Retrieve the Archive and Archivedata models by the provided ID
        $archive = Archive::findOrFail($id);
        $archivedata = Archivedata::findOrFail($id);

        // Load the view and generate a PDF in A4 portrait format
        $pdf = PDF::loadView('archivedata.archivedoc-f', compact('archive', 'archivedata'), [], [
            'mode'                   => 'utf-8',
            'format'          => 'A4-P', // Portrait orientation
            'default_font'    => 'times-new-roman', // Set the default font
            'custom_font_dir' => base_path('resources/fonts/'), // Custom font directory
            'custom_font_data' => [
                'times-new-roman' => [ // Define custom font
                    'R'  => 'times.ttf', // Regular font file
                    'B'  => 'timesbd.ttf',    // Bold font file (optional)
                    'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                    'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
                    'unAGlyphs' => true
                ],
            ],
        ]);

        // Stream the PDF to the browser with a dynamic filename
        return $pdf->stream('Archivedata-' . $archivedata->id . '.pdf');
    }

    public function archivedestalam($id)
    {
        // Retrieve the Archive and Archivedata models by the provided ID
        $archive = Archive::findOrFail($id);
        $archivedata = Archivedata::findOrFail($id);

        // Load the view and generate a PDF in A4 portrait format
        $pdf = PDF::loadView('archivedata.archivedestalam', compact('archive', 'archivedata'), [], [

            'mode'                   => 'utf-8',
            'format' => 'A5-p', // Portrait orientation
            'default_font'    => 'times-new-roman', // Set the default font
            'custom_font_dir' => base_path('resources/fonts/'), // Custom font directory
            'custom_font_data' => [
                'times-new-roman' => [ // Define custom font
                    'R'  => 'times.ttf', // Regular font file
                    'B'  => 'timesbd.ttf',    // Bold font file (optional)
                    'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                    'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
                    'unAGlyphs' => true
                ],
            ],
        ]);

        // Stream the PDF to the browser with a dynamic filename
        return $pdf->stream('Archivedata-' . $archivedata->id . '.pdf');
    }

    public function archivedestalam2($id)
    {
        // Retrieve the Archive and Archivedata models by the provided ID
        $archive = Archive::findOrFail($id);
        $archivedata = Archivedata::findOrFail($id);


        // Load the view and generate a PDF in A4 portrait format
        $pdf = PDF::loadView('archivedata.archivedestalam2', compact('archive', 'archivedata'), [], [

            'mode'                   => 'utf-8',
            'format' => 'A5-L', // Portrait orientation
            'default_font'    => 'times-new-roman', // Set the default font
            'custom_font_dir' => base_path('resources/fonts/'), // Custom font directory
            'custom_font_data' => [
                'times-new-roman' => [ // Define custom font
                    'R'  => 'times.ttf', // Regular font file
                    'B'  => 'timesbd.ttf',    // Bold font file (optional)
                    'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                    'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
                    'unAGlyphs' => true
                ],
            ],
        ]);

        // Stream the PDF to the browser with a dynamic filename
        return $pdf->stream('Archivedata-' . $archivedata->id . '.pdf');
    }


}