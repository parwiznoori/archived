<?php

namespace App\Http\Controllers\Archive;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Imagick;

class PDFToJPGController extends Controller
{
    public static function convert(Request $request)
    {
        $request->validate([
            'path.*' => 'required|image|mimes:pdf,jpeg,png,jpg,gif,svg|max:1000000', 
        ]);
        doc_name
        $pdf = $request->file('path');
        $pdfPath = $pdf->getPathName();
        $outputDir = public_path() . '/archivefiles/' . $request->book_name . '/';

        // Create the output directory if it doesn't exist
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $imagick = new Imagick();

        // Set the resolution for the images (DPI)
        $imagick->setResolution(300, 300); // 300 DPI for high quality
        $imagick->readImage($pdfPath);

        $pageCount = 0;
        foreach ($imagick as $index => $page) {
            // Set image format to JPG
            $page->setImageFormat('jpg');

            // Set quality for the image (0 - 100)
            $page->setImageCompressionQuality(100); // 100 for no compression

            // Optional: You can also ensure to keep the colorspace
            $page->setColorspace(Imagick::COLORSPACE_RGB);

            $outputPath = $outputDir . '/' . ($index + 1) . '.jpg';
            $page->writeImage($outputPath);
            $pageCount = $index + 1;
        }

        // Clear Imagick object to free resources
        $imagick->clear();
        $imagick->destroy();

        return $pageCount;
    }

}
