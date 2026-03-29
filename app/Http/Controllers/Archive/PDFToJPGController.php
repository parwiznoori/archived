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

        $outputDir = public_path('/archivefiles/' . $archive->id . '-' . $request->book_name);

        // delete old
        if (File::exists($outputDir)) {
            File::deleteDirectory($outputDir);
        }

        // create
        File::makeDirectory($outputDir, 0775, true);

        $imagick = new Imagick();
        $imagick->setResolution(150, 150);

        // 🔥 مهم
        $imagick->readImage($pdfPath);

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

        return $pageCount;
    }
}