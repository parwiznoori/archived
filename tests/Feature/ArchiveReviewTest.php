<?php

namespace Tests\Feature;

use App\Models\Archivedata;
use App\Notifications\ArchivePageRejectedNotification;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArchiveReviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'archive_test',
            'database.connections.archive_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
            'activitylog.enabled' => false,
        ]);

        Schema::create('archives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('book_name')->nullable();
            $table->integer('qc_user_id');
            $table->integer('de_user_id');
            $table->integer('qc_status_id')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('archiveimages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('archive_id');
            $table->integer('status_id');
            $table->integer('qc_status_id')->default(1);
            $table->integer('total_students');
            $table->integer('book_pagenumber')->default(1);
            $table->timestamps();
        });
        Schema::create('archivedatas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('archive_id');
            $table->integer('archiveimage_id');
            $table->integer('column_number');
            $table->integer('qc_status_id')->default(1);
            $table->text('reject_comments')->nullable();
            $table->boolean('re_updated')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('model_id');
            $table->string('model_type');
        });

        DB::table('archives')->insert([
            ['id' => 1, 'qc_user_id' => 10, 'de_user_id' => 20],
            ['id' => 2, 'qc_user_id' => 11, 'de_user_id' => 21],
        ]);
        DB::table('archiveimages')->insert([
            ['id' => 1, 'archive_id' => 1, 'status_id' => 4, 'total_students' => 2],
            ['id' => 5, 'archive_id' => 1, 'status_id' => 2, 'total_students' => 0],
            ['id' => 3, 'archive_id' => 2, 'status_id' => 4, 'total_students' => 1],
        ]);
        DB::table('archivedatas')->insert([
            ['id' => 1, 'archive_id' => 1, 'archiveimage_id' => 1, 'column_number' => 1],
            ['id' => 2, 'archive_id' => 1, 'archiveimage_id' => 1, 'column_number' => 2],
            ['id' => 3, 'archive_id' => 2, 'archiveimage_id' => 3, 'column_number' => 1],
        ]);
        DB::table('users')->insert(['id' => 20, 'name' => 'Data entry user']);
    }

    public function testReviewerCannotReadOrChangeAnotherBooksReview()
    {
        $this->signInAs(11);

        $this->get('/archiveqcheck/1')->assertForbidden();
        $this->get('/archiveqcheckdata/1/1')->assertForbidden();
        $this->postJson('/archiveqcheckdataSetStatus', ['recordId' => 1, 'approvalStatus' => 3])->assertForbidden();
        $this->postJson('/archiveqcheckImageSetStatus', ['recordId' => 5, 'approvalStatus' => 3])->assertForbidden();
        $this->assertDatabaseHas('archivedatas', ['id' => 1, 'qc_status_id' => 1]);
        $this->assertDatabaseHas('archiveimages', ['id' => 5, 'qc_status_id' => 1]);
    }

    public function testReviewValidatesInputAndKeepsPageAndBookStatusInSync()
    {
        $this->signInAs(10);
        Notification::fake();

        $this->postJson('/archiveqcheckdataSetStatus', ['recordId' => 1, 'approvalStatus' => 2])->assertStatus(422);
        $this->postJson('/archiveqcheckdataSetStatus', ['recordId' => 1, 'approvalStatus' => 4])->assertStatus(422);
        $this->postJson('/archiveqcheckImageSetStatus', ['recordId' => 1, 'approvalStatus' => 3])->assertStatus(409);

        $this->post('/archiveqcheckdataSetStatus', ['recordId' => 1, 'approvalStatus' => 3])->assertRedirect();
        $this->assertDatabaseHas('archivedatas', [
            'id' => 1, 'qc_status_id' => 3, 'reject_comments' => Archivedata::QC_APPROVAL_COMMENT, 're_updated' => false,
        ]);
        $this->assertDatabaseHas('archiveimages', ['id' => 1, 'qc_status_id' => 1]);

        $this->post('/archiveqcheckdataSetStatus', ['recordId' => 2, 'approvalStatus' => 3])->assertRedirect();
        $this->assertDatabaseHas('archiveimages', ['id' => 1, 'qc_status_id' => 3]);
        $this->assertDatabaseHas('archives', ['id' => 1, 'qc_status_id' => 2]);
        $this->post('/archiveqcheckImageSetStatus', ['recordId' => 5, 'approvalStatus' => 3])->assertRedirect();
        $this->assertDatabaseHas('archives', ['id' => 1, 'qc_status_id' => 3]);

        $rejection = ['recordId' => 2, 'approvalStatus' => 4, 'reject_comments' => 'Check the date'];
        $this->post('/archiveqcheckdataSetStatus', $rejection)->assertRedirect();
        $this->post('/archiveqcheckdataSetStatus', $rejection)->assertRedirect();
        $this->assertDatabaseHas('archiveimages', ['id' => 1, 'qc_status_id' => 4]);
        $this->assertDatabaseHas('archives', ['id' => 1, 'qc_status_id' => 4]);
        Notification::assertSentToTimes(User::findOrFail(20), ArchivePageRejectedNotification::class, 1);
    }

    public function testPageEditsRequireTheAssignedUserAndAnEditablePage()
    {
        $input = ['approve_archive_id' => 1, 'approve_page_id' => 1, 'approve_total_student' => 1];

        $this->signInAs(21);
        $this->postJson('/archiveBookDataEntryApprove', $input)->assertForbidden();
        $this->postJson('/archiveBookDataEntryPageReject/1/1/1')->assertForbidden();
        $this->postJson('/import/undoBookUpload/1')->assertForbidden();

        $this->signInAs(20);
        $this->postJson('/archiveBookDataEntryPageReject/3/1/1')->assertNotFound();
        $this->get('/archiveBookDataEntryPageReject/1/1/1')->assertStatus(405);
        DB::table('archiveimages')->where('id', 1)->update(['qc_status_id' => 3]);
        $this->postJson('/archiveBookDataEntryApprove', $input)->assertStatus(409);
        $this->assertDatabaseHas('archivedatas', ['id' => 2]);

        DB::table('archiveimages')->where('id', 1)->update(['qc_status_id' => 1]);
        $this->post('/archiveBookDataEntryApprove', $input)->assertRedirect('/archiveBookDataEntryPage/1/1/0');
        $this->assertDatabaseHas('archivedatas', ['id' => 1]);
        $this->assertDatabaseMissing('archivedatas', ['id' => 2]);
        $this->assertDatabaseHas('archiveimages', ['id' => 1, 'total_students' => 1, 'status_id' => 4]);
    }

    public function testCsvCannotChangeOtherBooksOrMarkAPopulatedPageEmpty()
    {
        Storage::fake('local');
        DB::table('archiveimages')->where('id', 5)->update(['status_id' => 1, 'book_pagenumber' => 2]);
        $header = implode(',', (new \App\Exports\ArchiveExcelTemplateExport)->headings());
        $row = static function ($page) {
            return '1,' . $page . ',0,' . str_repeat('x,', 24) . 'x';
        };
        $upload = static function () use ($header, $row) {
            return UploadedFile::fake()->createWithContent('records.csv', implode("\n", [$header, $row(1), $row(2)]));
        };

        $this->signInAs(21);
        $this->post('/import', ['archive_id' => 1, 'csv_file' => $upload()])->assertForbidden();
        $this->assertDatabaseHas('archiveimages', ['id' => 5, 'status_id' => 1]);

        $this->signInAs(20);
        $this->post('/import', ['archive_id' => 1, 'csv_file' => $upload()])->assertRedirect('/archive/view/1');
        $this->assertDatabaseHas('archiveimages', ['id' => 1, 'status_id' => 4, 'total_students' => 2]);
        $this->assertDatabaseHas('archiveimages', ['id' => 5, 'status_id' => 2, 'total_students' => 0]);
        $this->assertDatabaseHas('archiveimages', ['id' => 3, 'status_id' => 4]);
    }

    private function signInAs(int $id): void
    {
        $user = new User(['name' => 'Test user']);
        $user->id = $id;
        $user->setRelation('roles', collect());
        $user->setRelation('departments', collect());
        $user->setRelation('grades', collect());
        $this->actingAs($user);
    }
}
