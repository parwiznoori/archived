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

        // 🔹 ذخیره PDF در مسیر دائمی temp
        $pdfName = time() . '-' . $pdf->getClientOriginalName();
        $pdfSavePath = storage_path('app/temp_pdfs/' . $pdfName);

        if (!is_dir(dirname($pdfSavePath))) {
            mkdir(dirname($pdfSavePath), 0775, true);
        }

        $pdf->move(dirname($pdfSavePath), $pdfName);

        // ✅ بررسی فایل
        if (!file_exists($pdfSavePath) || filesize($pdfSavePath) == 0) {
            throw new \Exception('PDF file not found or empty');
        }

        // 🔹 مسیر خروجی JPG
        $outputDir = public_path('/archivefiles/' . $archive->id . '-' . $request->book_name);

        // ✅ اگر فولدر وجود داشت فقط داخلش پاک شود
        if (is_dir($outputDir)) {
            File::cleanDirectory($outputDir);
        } else {
            mkdir($outputDir, 0775, true);
        }

        $imagick = new Imagick();
        $imagick->setResolution(150, 150);

        try {
            // ✅ تست خواندن صفحه اول PDF
            $imagick->readImage($pdfSavePath . '[0]');
        } catch (\Exception $e) {
            throw new \Exception('Cannot read PDF: ' . $e->getMessage());
        }

        if ($imagick->getNumberImages() == 0) {
            throw new \Exception('PDF has no readable pages');
        }

        // ✅ پاک و خواندن کل PDF
        $imagick->clear();
        $imagick->readImage($pdfSavePath);

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

        // 🔹 حذف فایل PDF temp بعد از پردازش
        unlink($pdfSavePath);

        return $pageCount;
    }
}