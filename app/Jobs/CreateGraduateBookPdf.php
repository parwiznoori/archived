<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Services\GraduatesBookService;
use PDF;
use ZipArchive;
use Illuminate\Support\Facades\Auth;
use App\Models\University;
use App\Models\Department;
use App\Models\GraduateBooksPdf;
use App\Events\GraduateBookNotificationEvent;
use App\Notifications\GraduateBookCreatedNotification;
use App\User;

class CreateGraduateBookPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileName;
    public $university;
    public $department;
    public $year;
    public $grade_id;
    public $user_id;
    public $studentsArray;
    public $failOnTimeout = false;
    public $timeout = 600;
    protected $graduatesBookService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($university,$department,$graduate_year,$grade_id,GraduatesBookService $graduatesBookService,$user_id)
    {
        ini_set('memory_limit', '1500000M');
        ini_set("pcre.backtrack_limit", "3000000");
        ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
        $this->university = $university;
        $this->department = $department;
        $this->year = $graduate_year;
        $this->grade_id = $grade_id;
        $this->user_id = $user_id;
        $this->graduatesBookService = $graduatesBookService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ob_start();
        
        $university = $this->university;
        $department = $this->department;
        $year = $this->year;
        $grade_id = $this->grade_id;
        $graduatesStusentsArray = array();
        $studentsArray = $this->graduatesBookService->getGraduateStudentsData($university->id,$department->id,$year,$grade_id);

        $graduatesStusentsArray = $this->graduatesBookService->get_registration_of_graduate_documents($university->id,$department->id,$year,$grade_id);

        $pdf = PDF::chunkLoadView('<html-separator/>', 'graduate-book.pdf.graduate-results-table', compact('university', 'department', 'year', 'studentsArray'),[], [            
            'format' => 'A4-L'
        ]);
        $pdfFilePath =  "graduate-books/".$department->id."/graduate-students-results-$year.pdf";
        Storage::disk('public')->put($pdfFilePath, $pdf->output());
      
        $pdf = PDF::chunkLoadView('<html-separator/>', 'graduate-book.pdf.graduate-students-index', compact('university', 'department', 'year', 'graduatesStusentsArray'),[], [            
            'format' => 'A4-P'
        ]);
        $pdfFilePath =  "graduate-books/".$department->id."/graduate-students-index-$year.pdf";
        Storage::disk('public')->put($pdfFilePath, $pdf->output());
      
        ob_end_clean();
        $pdfFilePath = "graduate-books/". $department->id.'/';
        $zipFilePath = $this->updateZipArchive($pdfFilePath,$year,$department->id);

        $graduateBookPdf = GraduateBooksPdf::where('university_id',$university->id)
        ->where('department_id',$department->id)
        ->where('grade_id',$grade_id)
        ->where('graduated_year',$year)
        ->first();

        if(!$graduateBookPdf && $zipFilePath != '')
        {
            $graduateBookPdf = GraduateBooksPdf::create([
                'university_id' => $university->id,
                'department_id' => $department->id,
                'grade_id' => $grade_id,
                'graduated_year' => $year,
                'status' => 'completed',
                'fileName' => $zipFilePath,
                'user_id' => $this->user_id,
                'generated_count' => 1
            ]);
            //notificaton will send to authenticated user
            $user = User::where('id', $this->user_id )->first();
            $user->notify(new GraduateBookCreatedNotification($graduateBookPdf,$user));
            
            // graduatebook will fire through an event

            $message = "سوال جدید را اضافه...";
            event(new GraduateBookNotificationEvent($graduateBookPdf, $user,$message));
        }
        else 
        {
            $graduateBookPdf->increment('generated_count', 1);
            //notificaton will send to authenticated user
            $user = User::where('id', $this->user_id )->first();
            $user->notify(new GraduateBookCreatedNotification($graduateBookPdf,$user));
            
            // graduatebook will fire through an event

            $message = "سوال جدید را اضافه...";
            event(new GraduateBookNotificationEvent($graduateBookPdf, $user,$message));
        }
       
        // return $pdf->download("graduate-students-results-$year.pdf");
    }

    private function updateZipArchive($pdfFilePath, $year,$department_id)
    {
        $zip = new ZipArchive();
        $zipFilePath = Storage::disk('public')->path('/graduate-books/' . $department_id. '/graduate-book-'.$year.'.zip');
        $pdfLocalFilePath = Storage::disk('public')->path($pdfFilePath);
        $indexFileName = "graduate-students-index-$year.pdf";
        $indexFileNamePath = 'graduate-books/' . $department_id.'/'.$indexFileName;
        $studentsResultsName = "graduate-students-results-$year.pdf";
        $studentsResultsNamePath = 'graduate-books/' . $department_id.'/'.$studentsResultsName;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $pathIndex = Storage::disk('public')->get($pdfFilePath.$indexFileName);
            $pathResults = Storage::disk('public')->get($pdfFilePath.$studentsResultsName);
            $zip->addFromString($indexFileName, $pathIndex);
            $zip->addFromString($studentsResultsName, $pathResults);
            $zip->close();
            logger('Graduate Book zip file for demartment id of '.$department_id.' and year of '.$year.' created successfully.');
           
            Storage::disk('public')->delete($indexFileNamePath);
            Storage::disk('public')->delete($studentsResultsNamePath);
            return "storage/graduate-books/".$department_id.'/graduate-book-'.$year.'.zip';
           
            
        } else {
            logger('Failed opening zip file:' . $zipFilePath);
            return '';
        }
    }
}
