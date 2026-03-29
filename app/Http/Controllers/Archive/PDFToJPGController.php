<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Imagick;

class PDFToJPGController extends Controller
{
    public static function convert(Request $request, $archive)
    {
        $request->validate([
            'path' => 'required|file|mimes:pdf|max:1024000',
        ]);

        $pdf = $request->file('path'); 
        $pdfPath = $pdf->getPathName();

        // ✅ بررسی فایل
        if (!file_exists($pdfPath) || filesize($pdfPath) == 0) {
            throw new \Exception('PDF file not found or empty');
        }

        $outputDir = public_path('/archivefiles/' . $archive->id . '-' . $request->book_name);

        // ✅ حذف فولدر قبلی
        if (File::exists($outputDir)) {
            File::deleteDirectory($outputDir);
        }

        // ✅ ساخت امن فولدر (بدون خطا)
        File::ensureDirectoryExists($outputDir);

        $imagick = new Imagick();

        // ✅ تنظیم کیفیت مناسب (کمتر از 300 برای جلوگیری از کرش)
        $imagick->setResolution(150, 150);

        try {
            // ✅ تست خواندن PDF (صفحه اول)
            $imagick->readImage($pdfPath . '[0]');
        } catch (\Exception $e) {
            throw new \Exception('Cannot read PDF: ' . $e->getMessage());
        }

        // اگر صفحه‌ای لود نشد
        if ($imagick->getNumberImages() == 0) {
            throw new \Exception('PDF has no readable pages');
        }

        // ✅ حالا کل PDF را بخوان
        $imagick->clear();
        $imagick->readImage($pdfPath);

        $pageCount = 0;

        foreach ($imagick as $index => $page) {

            $page->setImageFormat('jpg');
            $page->setImageCompressionQuality(90);
            $page->setColorspace(Imagick::COLORSPACE_RGB);

            $outputPath = $outputDir . '/' . ($index + 1) . '.jpg';

            $page->writeImage($outputPath);

            // ✅ آزادسازی حافظه (خیلی مهم)
            $page->clear();

            $pageCount++;
        }

        $imagick->clear();
        $imagick->destroy();

        return $pageCount;
    }
}