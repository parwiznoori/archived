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
        // ✅ اعتبارسنجی فایل PDF
        $request->validate([
            'path' => 'required|file|mimes:pdf|max:1024000', // حداکثر 1GB
        ]);

        $pdf = $request->file('path'); 

        // ✅ مسیر موقت امن داخل storage پروژه
        $tempDir = storage_path('app/temp_pdfs');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $tempPath = $tempDir . '/temp_' . time() . '.pdf';
        $pdf->move($tempDir, basename($tempPath));

        // بررسی فایل
        if (!file_exists($tempPath) || filesize($tempPath) === 0) {
            throw new \Exception('PDF file not found or empty');
        }

        // ✅ مسیر خروجی JPG
        $outputDir = public_path('archivefiles/' . $archive->id . '-' . $request->book_name);
        if (is_dir($outputDir)) {
            File::cleanDirectory($outputDir); // فقط محتوا پاک شود
        } else {
            mkdir($outputDir, 0775, true);
        }

        $imagick = new Imagick();
        $imagick->setResolution(150, 150); // DPI مناسب، 300 ممکن است سرور را کرش دهد

        // ✅ تست خواندن صفحه اول PDF
        try {
            $imagick->readImage($tempPath . '[0]');
        } catch (\Exception $e) {
            throw new \Exception('Cannot read PDF: ' . $e->getMessage());
        }

        if ($imagick->getNumberImages() === 0) {
            throw new \Exception('PDF has no readable pages');
        }

        // پاک و خواندن کل PDF
        $imagick->clear();
        $imagick->readImage($tempPath);

        $pageCount = 0;

        foreach ($imagick as $index => $page) {
            $page->setImageFormat('jpg');
            $page->setImageCompressionQuality(90);
            $page->setColorspace(Imagick::COLORSPACE_RGB);

            $outputPath = $outputDir . '/' . ($index + 1) . '.jpg';
            $page->writeImage($outputPath);

            $page->clear();
            $pageCount++;
        }

        $imagick->clear();
        $imagick->destroy();

        // ✅ حذف فایل موقت PDF
        unlink($tempPath);

        return $pageCount;
    }
}