<?php

namespace App\Http\Controllers\Archive;

use App\Models\Archive;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Imagick;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class PDFToJPGController
{
    private const SCAN_TIMEOUT = 300;

    public static function convert(UploadedFile $upload, Archive $archive): array
    {
        $directory = 'archivefiles/' . $archive->id . '-scan-' . Str::uuid();
        $output = public_path($directory);

        try {
            if (!File::makeDirectory($output, 0755, true)) {
                throw new RuntimeException('Unable to create the scan directory.');
            }

            self::renderScan($upload->getPathname(), $output);

            $pageCount = count(File::files($output));
            if ($pageCount === 0) {
                throw new RuntimeException('The scan contains no pages.');
            }

            $paths = [];
            for ($number = 1; $number <= $pageCount; $number++) {
                if (!is_file($output . '/' . $number . '.jpg')) {
                    throw new RuntimeException('A rendered scan page is missing.');
                }
                $paths[] = '/' . $directory . '/' . $number . '.jpg';
            }

            $archive->getConnection()->afterRollBack(fn () => File::deleteDirectory($output));

            return $paths;
        } catch (\Throwable $error) {
            File::deleteDirectory($output);
            report($error);

            throw ValidationException::withMessages([
                'path' => $error instanceof ProcessTimedOutException
                    ? __('تبدیل فایل اسکن در مهلت ۵ دقیقه تکمیل نشد.')
                    : __('تبدیل فایل اسکن ممکن نشد. فایل را بررسی کرده و دوباره بارگذاری کنید.'),
            ]);
        }
    }

    private static function renderScan(string $input, string $output): void
    {
        $php = PHP_BINDIR . '/php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        if (PHP_OS_FAMILY === 'Windows') {
            $php = dirname(PHP_BINARY) . '/php.exe';
        }
        if (!is_executable($php)) {
            $php = (new PhpExecutableFinder())->find(false);
        }
        if ($php === false) {
            throw new RuntimeException('Unable to locate PHP for PDF conversion.');
        }

        $command = [$php];
        if (PHP_OS_FAMILY === 'Windows' && ($ini = php_ini_loaded_file())) {
            array_push($command, '-c', $ini);
        }
        array_push($command, '-d', 'max_execution_time=0', base_path('artisan'), 'archive:render-scan', $input, $output);

        // Allow the worker to time out and clean up before the web request ends.
        $previousLimit = (int) ini_get('max_execution_time');
        $requestLimit = self::SCAN_TIMEOUT + 30;
        $extendLimit = $previousLimit > 0 && $previousLimit < $requestLimit;
        if ($extendLimit) {
            set_time_limit($requestLimit);
        }

        try {
            (new Process($command, base_path(), timeout: self::SCAN_TIMEOUT))->mustRun();
        } finally {
            if ($extendLimit) {
                set_time_limit($previousLimit);
            }
        }
    }

    public static function renderPages(string $input, string $output): void
    {
        $image = new Imagick();
        try {
            $image->setResolution(300, 300);
            $image->readImage($input);

            foreach ($image as $index => $page) {
                $page->setImageFormat('jpg');
                $page->setImageCompressionQuality(100);
                $page->setColorspace(Imagick::COLORSPACE_RGB);

                if (!$page->writeImage($output . '/' . ($index + 1) . '.jpg')) {
                    throw new RuntimeException('Unable to write a scan page.');
                }
            }
        } finally {
            $image->clear();
        }
    }
}
