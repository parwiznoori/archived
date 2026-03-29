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

        // ✅ ساخت مسیر خروجی (بدون تکرار)
        $outputDir = public_path('/archivefiles/' . $archive->id . '-' . $request->book_name);

        // ✅ اگر فولدر وجود داشت فقط داخلش پاک شود (نه حذف کامل)
        if (is_dir($outputDir)) {
            File::cleanDirectory($outputDir);
        } else {
            mkdir($outputDir, 0775, true);
        }

        $imagick = new Imagick();

        // ✅ تنظیم DPI مناسب
        $imagick->setResolution(150, 150);

        try {
            // ✅ تست خواندن PDF (صفحه اول)
            $imagick->readImage($pdfPath . '[0]');
        } catch (\Exception $e) {
            throw new \Exception('Cannot read PDF: ' . $e->getMessage());
        }

        // ✅ اگر صفحه‌ای لود نشد
        if ($imagick->getNumberImages() == 0) {
            throw new \Exception('PDF has no readable pages');
        }

        // ✅ پاک و خواندن کل PDF
        $imagick->clear();
        $imagick->readImage($pdfPath);

        $pageCount = 0;

        foreach ($imagick as $index => $page) {

            $page->setImageFormat('jpg');
            $page->setImageCompressionQuality(90);
            $page->setColorspace(Imagick::COLORSPACE_RGB);

            $outputPath = $outputDir . '/' . ($index + 1) . '.jpg';

            $page->writeImage($outputPath);

            // ✅ آزادسازی حافظه
            $page->clear();

            $pageCount++;
        }

        $imagick->clear();
        $imagick->destroy();

        return $pageCount;
    }
}