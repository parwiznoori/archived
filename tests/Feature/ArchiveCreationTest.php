<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Tests\TestCase;

class ArchiveCreationTest extends TestCase
{
    private $publicDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'archive_creation_test',
            'database.connections.archive_creation_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
            'activitylog.enabled' => false,
        ]);

        Schema::create('archives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('university_id');
            $table->integer('book_pagenumber')->nullable();
            $table->string('book_name');
            $table->string('book_description');
            $table->timestamps();
            $table->softDeletes();
        });
        $migration = base_path('database/migrations/2026_11_11_000001_add_creation_request_key_to_archives.php');
        require_once $migration;
        (new \AddCreationRequestKeyToArchives())->up();

        Schema::create('archiveimages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('archive_id');
            $table->integer('book_pagenumber');
            $table->integer('status_id');
            $table->string('path');
        });
        Schema::create('archive_archive_year', function (Blueprint $table) {
            $table->integer('archive_id');
            $table->integer('archive_year_id');
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('archive_type');
        });

        $user = new User();
        $user->id = 12;
        $this->actingAs($user)->withoutMiddleware(PermissionMiddleware::class);
        $this->publicDirectory = sys_get_temp_dir() . '/archive-creation-' . Str::uuid();
        $this->app->instance('path.public', $this->publicDirectory);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->publicDirectory)) {
            foreach (glob($this->publicDirectory . '/archivefiles/*', GLOB_ONLYDIR) as $directory) {
                foreach (glob($directory . '/*.jpg') as $file) {
                    unlink($file);
                }
                rmdir($directory);
            }
            rmdir($this->publicDirectory . '/archivefiles');
            rmdir($this->publicDirectory);
        }

        parent::tearDown();
    }

    public function testRetryAfterSuccessfulUploadReusesTheOriginalBook()
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('Book scan conversion requires Imagick.');
        }

        $create = $this->get('/archive/create');
        $create->assertRedirect();
        $formUrl = $create->headers->get('Location');
        parse_str(parse_url($formUrl, PHP_URL_QUERY), $query);
        $token = $query['submission_token'];
        $this->assertTrue(Str::isUuid($token));

        $this->post('/archive', $this->bookForm($token, $this->imageUpload()))->assertRedirect('/archive');

        // A resend after losing the response may arrive without the original upload.
        $this->post('/archive', ['submission_token' => $token])
            ->assertRedirect('/archive')->assertSessionHas('message');
        $this->get($formUrl)->assertRedirect('/archive');
        $this->assertSame(1, DB::table('archives')->count());
        $this->assertSame(1, DB::table('archiveimages')->count());
        $this->assertSame(1, DB::table('archive_archive_year')->count());
        $this->assertFileExists($this->publicDirectory . DB::table('archiveimages')->value('path'));

        // A new form can create a different book with the same name.
        $this->post('/archive', $this->bookForm((string) Str::uuid(), $this->imageUpload()))
            ->assertRedirect('/archive');
        $this->assertSame(2, DB::table('archives')->count());
    }

    public function testFailedUploadCanBeRetriedWithTheSameToken()
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('Book scan conversion requires Imagick.');
        }

        $token = (string) Str::uuid();
        $invalid = UploadedFile::fake()->createWithContent('scan.png', 'invalid scan');
        $this->post('/archive', $this->bookForm($token, $invalid))
            ->assertRedirect(route('archive.create', ['submission_token' => $token]))
            ->assertSessionHasErrors('path');
        $this->assertSame(0, DB::table('archives')->count());

        $this->post('/archive', $this->bookForm($token, $this->imageUpload()))->assertRedirect('/archive');
        $this->assertSame(1, DB::table('archives')->count());
    }

    private function bookForm(string $token, UploadedFile $upload): array
    {
        return [
            'submission_token' => $token,
            'university_id' => 1,
            'archive_year_id' => [1],
            'book_name' => 'Test book',
            'book_description' => 'Test scan',
            'path' => $upload,
        ];
    }

    private function imageUpload(): UploadedFile
    {
        $image = new \Imagick();
        $image->newImage(10, 10, 'white', 'png');

        return UploadedFile::fake()->createWithContent('scan.png', $image->getImageBlob());
    }
}
