<?php

namespace Tests\Feature;

use App\Exports\ArchiveExcelTemplateExport;
use App\Models\Archivedata;
use App\Services\ArchiveCsvImporter;
use App\Services\ArchiveCsvReader;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ArchiveCsvImportTest extends TestCase
{
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'csv_test', 'database.connections.csv_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''], 'activitylog.enabled' => false]);
        Schema::create('archives', function (Blueprint $t) {
            $t->increments('id'); $t->integer('university_id'); $t->integer('de_user_id'); $t->integer('qc_status_id')->default(1); $t->integer('status_id')->default(1); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('archiveimages', function (Blueprint $t) {
            $t->increments('id'); $t->integer('archive_id'); $t->integer('book_pagenumber'); $t->integer('total_students')->default(0); $t->integer('status_id')->default(1); $t->integer('qc_status_id')->default(1); $t->timestamps();
        });
        Schema::create('archivedatas', function (Blueprint $t) {
            $t->increments('id');
            foreach ((new ArchiveExcelTemplateExport)->headings() as $field) { $t->string($field)->nullable(); }
            $t->integer('status_id'); $t->integer('qc_status_id'); $t->string('description'); $t->timestamps(); $t->softDeletes();
            $t->unique(['archive_id', 'name', 'father_name', 'grandfather_name', 'university_id', 'faculty_id', 'department_id'], 'unique_student');
        });
        Schema::create('archive_entry_users', function (Blueprint $t) { $t->increments('id'); $t->integer('archive_id'); $t->integer('status_id'); $t->timestamps(); });
        Schema::create('uploaded_csv_logs', function (Blueprint $t) { $t->increments('id'); $t->integer('archivedata_id'); $t->string('batch_id'); $t->timestamp('created_at'); });
        foreach (['universities', 'grades', 'semester_type'] as $table) { Schema::create($table, fn (Blueprint $t) => $t->increments('id')); DB::table($table)->insert(['id'=>1]); }
        Schema::create('faculties', function (Blueprint $t) { $t->increments('id'); $t->integer('university_id'); });
        Schema::create('departments', function (Blueprint $t) { $t->increments('id'); $t->integer('faculty_id'); });
        DB::table('faculties')->insert(['id'=>1,'university_id'=>1]);
        DB::table('departments')->insert(['id'=>1,'faculty_id'=>1]);
        DB::table('archives')->insert(['id'=>1,'university_id'=>1,'de_user_id'=>20]);
        DB::table('archiveimages')->insert([['id'=>10,'archive_id'=>1,'book_pagenumber'=>1],['id'=>11,'archive_id'=>1,'book_pagenumber'=>2]]);
        $user = new User; $user->id = 20;
        foreach (['grades', 'departments', 'roles'] as $relation) { $user->setRelation($relation, collect()); }
        $this->actingAs($user);
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) { unlink($file); }
        parent::tearDown();
    }

    private function csv(array $rows, string $delimiter = ',', bool $utf16 = false): string
    {
        $stream = fopen('php://temp', 'r+');
        foreach (array_merge([(new ArchiveExcelTemplateExport)->headings()], $rows) as $row) { fputcsv($stream, $row, $delimiter, '"', ''); }
        rewind($stream); $content = stream_get_contents($stream); fclose($stream);
        $file = tempnam(sys_get_temp_dir(), 'archive-csv-'); $this->files[] = $file;
        file_put_contents($file, $utf16 ? "\xFF\xFE" . mb_convert_encoding($content, 'UTF-16LE', 'UTF-8') : "\xEF\xBB\xBF" . $content);
        return $file;
    }

    private function student(array $changes = []): array
    {
        $row = array_fill_keys((new ArchiveExcelTemplateExport)->headings(), '');
        return array_values(array_replace($row, ['archive_id'=>'1','archiveimage_id'=>'1','column_number'=>'1','university_id'=>'1','faculty_id'=>'1','department_id'=>'1','name'=>'احمد','father_name'=>'محمود','grandfather_name'=>'علی','year_of_inclusion'=>'1390','graduated_year'=>'1394'], $changes));
    }

    private function runImport(string $file): array
    {
        return (new ArchiveCsvImporter)->import($file, 1, fn ($archive) => null);
    }

    public function testMixedRowsReportInsertedDuplicatePageAndExactInvalidColumn(): void
    {
        $report = $this->runImport($this->csv([
            array_slice($this->student(), 0, 21),
            $this->student(['archiveimage_id'=>'2']),
            $this->student(['name'=>'','column_number'=>'2']),
            $this->student(['archiveimage_id'=>'99','name'=>'کریم']),
        ], ';'));
        $this->assertSame(1, $report['inserted']); $this->assertSame(1, $report['duplicate']); $this->assertSame(2, $report['error']);
        $this->assertStringContainsString('صفحه 1', $report['details'][1]['message']);
        $this->assertStringContainsString('name', $report['details'][2]['message']);
        $this->assertSame(4, $report['details'][2]['row']);
        $this->assertStringContainsString('archiveimage_id', $report['details'][3]['message']);
        $this->assertDatabaseHas('archiveimages', ['id'=>10,'total_students'=>1]);
    }

    public function testDeleteBookThenUploadSameCsvAgain(): void
    {
        $file = $this->csv([$this->student()]);
        $this->assertSame(1, $this->runImport($file)['inserted']);
        $this->post('/import/undoBookUpload/1')->assertRedirect();
        $this->assertSame(0, DB::table('archivedatas')->count());
        $this->assertSame(0, DB::table('uploaded_csv_logs')->count());
        $this->assertDatabaseHas('archiveimages', ['id'=>10,'total_students'=>0,'status_id'=>1]);
        $this->assertSame(1, $this->runImport($file)['inserted']);
    }

    public function testSoftDeletedIdentityCanBeImportedAgainWithoutUniqueKeyFailure(): void
    {
        $file = $this->csv([$this->student()]); $this->runImport($file);
        Archivedata::first()->delete();
        $this->assertSame(1, $this->runImport($file)['inserted']);
        $this->assertSame(1, Archivedata::count());
    }

    public function testUtf16TabAndPersianNumbersAreAcceptedAndBlankRowsIgnored(): void
    {
        $report = $this->runImport($this->csv([[], $this->student(['archiveimage_id'=>'۱','column_number'=>'۱'])], "\t", true));
        $this->assertSame(1, $report['inserted']); $this->assertSame(3, $report['details'][0]['row']);
    }

    public function testPopulatedPageCannotBeMarkedEmptyAndReviewPagesCannotBeChanged(): void
    {
        $this->runImport($this->csv([$this->student()]));
        $report = $this->runImport($this->csv([$this->student(['column_number'=>'0']), $this->student(['archiveimage_id'=>'2','column_number'=>'0'])]));
        $this->assertSame(1, $report['error']); $this->assertSame(1, $report['empty']);
        DB::table('archiveimages')->where('id',11)->update(['qc_status_id'=>3]);
        $this->assertSame(1, $this->runImport($this->csv([$this->student(['archiveimage_id'=>'2'])]))['error']);
    }

    public function testWrongHeadersFailBeforeAnyInsert(): void
    {
        $file = $this->csv([]); file_put_contents($file, "wrong,header\n1,2\n");
        $this->expectException(ValidationException::class);
        iterator_to_array((new ArchiveCsvReader)->rows($file));
    }

    public function testLegacyEmptyGrandfatherIsStillADuplicate(): void
    {
        $file = $this->csv([$this->student(['grandfather_name'=>''])]);
        $this->runImport($file);
        DB::table('archivedatas')->update(['grandfather_name'=>'']);
        $this->assertSame(1, $this->runImport($file)['duplicate']);
        $this->assertSame(1, Archivedata::count());
    }

    public function testMissingYearAndOccupiedStudentNumberReportTheirColumns(): void
    {
        $this->runImport($this->csv([$this->student()]));
        $report = $this->runImport($this->csv([$this->student(['name'=>'کریم','graduated_year'=>'']), $this->student(['name'=>'کریم'])]));
        $this->assertSame(2, $report['error']);
        $this->assertStringContainsString('graduated_year', $report['details'][0]['message']);
        $this->assertStringContainsString('column_number', $report['details'][1]['message']);
    }

    public function testCsvPageRendersReportAndEscapesUploadedNames(): void
    {
        $report = $this->runImport($this->csv([$this->student(['name'=>'<script>test</script>'])]));
        session()->flash('csv_report', $report);
        $template = str_replace(["@extends('layouts.app')", "@section('content')", '@endsection'], '', file_get_contents(resource_path('views/archive/csvfile.blade.php')));
        $html = \Illuminate\Support\Facades\Blade::render($template, [
            'errors'=>new \Illuminate\Support\ViewErrorBag,
            'archive'=>\App\Models\Archive::first(), 'universities'=>[1=>'پوهنتون'], 'archiveDepartments'=>collect(),
            'archiveImages'=>\App\Models\Archiveimage::with('archivedatas')->get(), 'totalPages'=>2,
        ]);
        $this->assertStringContainsString('نتیجه بررسی فایل CSV', $html);
        $this->assertStringContainsString('&lt;script&gt;test&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>test</script>', $html);
    }
}
