<?php

namespace Tests\Feature;

use App\Models\Archive;
use App\Services\ArchivePagePreview;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Blade, DB, File, Schema};
use Illuminate\Support\Str;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ArchiveGalleryTest extends TestCase
{
    private string $directory;
    private array $previews = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'gallery_test', 'database.connections.gallery_test'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>''], 'activitylog.enabled'=>false]);
        Schema::create('archives', function (Blueprint $t) { $t->increments('id'); $t->string('book_name'); $t->timestamps(); $t->softDeletes(); });
        Schema::create('archiveimages', function (Blueprint $t) { $t->increments('id'); $t->integer('archive_id'); $t->integer('book_pagenumber'); $t->string('path'); });
        DB::table('archives')->insert([['id'=>1,'book_name'=>'<script>test</script>'],['id'=>2,'book_name'=>'Other']]);
        $this->directory = public_path('archivefiles/gallery-test-' . Str::uuid());
        File::ensureDirectoryExists($this->directory);
        $image = new \Imagick;
        $image->newImage(2400, 1800, '#f0e5d2'); $image->setImageFormat('jpeg'); $image->writeImage($this->directory . '/scan.jpg'); $image->clear();
        DB::table('archiveimages')->insert(['id'=>10,'archive_id'=>1,'book_pagenumber'=>3,'path'=>'/archivefiles/' . basename($this->directory) . '/scan.jpg']);
        $user = new User; $user->id = 20;
        foreach (['departments','roles','grades'] as $relation) $user->setRelation($relation, collect());
        $this->actingAs($user)->withoutMiddleware(PermissionMiddleware::class);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);
        foreach ($this->previews as $preview) { File::delete([$preview, $preview . '.lock']); }
        parent::tearDown();
    }

    public function testPreviewIsResizedCachedAndLeavesOriginalUnchanged(): void
    {
        $source = $this->directory . '/scan.jpg'; $hash = hash_file('sha256', $source);
        $relative = DB::table('archiveimages')->value('path');
        $service = new ArchivePagePreview;
        $preview = $service->path($relative); $this->previews[] = $preview;
        $this->assertSame([1600,1200], array_slice(getimagesize($preview),0,2));
        $this->assertSame($preview, $service->path($relative));
        $this->assertSame($hash, hash_file('sha256',$source));
        $this->assertLessThan(filesize($source), filesize($preview));
    }

    public function testPreviewResponseSupportsBrowserCacheAndRejectsOtherBookPage(): void
    {
        $this->previews[] = (new ArchivePagePreview)->path(DB::table('archiveimages')->value('path'));
        $response = $this->get('/archive/1/pages/10/preview');
        $response->assertOk()->assertHeader('Content-Type','image/jpeg');
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->withHeader('If-None-Match',$response->headers->get('ETag'))->get('/archive/1/pages/10/preview')->assertStatus(304);
        $this->get('/archive/2/pages/10/preview')->assertNotFound();
    }

    public function testFilesOutsideScanDirectoryAreRejected(): void
    {
        $this->expectException(HttpException::class);
        (new ArchivePagePreview)->path('../.env');
    }

    public function testGalleryRendersOnePreviewAndOrdersPagesWithoutLoadingOriginals(): void
    {
        DB::table('archiveimages')->insert(['id'=>11,'archive_id'=>1,'book_pagenumber'=>1,'path'=>'/archivefiles/test/1.jpg']);
        $response = (new \App\Http\Controllers\Archive\ArchiveController)->show(1);
        $archive = $response->getData()['archive'];
        $this->assertSame([1,3], $archive->images->pluck('book_pagenumber')->all());
        $archive->setRelation('university', null);
        $template = str_replace(["@extends('layouts.app')","@section('content')",'@endsection'], '', file_get_contents(resource_path('views/archive/show.blade.php')));
        $html = Blade::render($template, compact('archive'));
        $this->assertSame(1, substr_count($html,'<img '));
        $this->assertStringContainsString('/archive/1/pages/11/preview', $html);
        $this->assertStringContainsString('fetchpriority="high"', $html);
        $this->assertStringNotContainsString('<script>test</script>', $html);
        $this->assertStringNotContainsString('<html', $html);
    }

    public function testBookWithoutImagesShowsEmptyState(): void
    {
        $archive = Archive::findOrFail(2); $archive->setRelation('university', null);
        $template = str_replace(["@extends('layouts.app')","@section('content')",'@endsection'], '', file_get_contents(resource_path('views/archive/show.blade.php')));
        $html = Blade::render($template, compact('archive'));
        $this->assertStringContainsString('هنوز عکسی', $html);
        $this->assertStringNotContainsString('id="agImage"', $html);
    }
}
