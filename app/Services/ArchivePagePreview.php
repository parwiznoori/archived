<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class ArchivePagePreview
{
    public function path(string $relativePath): string
    {
        $root = realpath(public_path('archivefiles'));
        $source = realpath(public_path(ltrim($relativePath, '/\\')));
        abort_unless($root && $source && is_file($source)
            && strncasecmp($source, $root . DIRECTORY_SEPARATOR, strlen($root) + 1) === 0
            && in_array(strtolower(pathinfo($source, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true), 404);

        $key = hash('sha256', $source . '|' . filemtime($source) . '|' . filesize($source) . '|preview-1600-v2');
        $directory = storage_path('app/archive-previews');
        File::ensureDirectoryExists($directory);
        $target = $directory . '/' . $key . '.jpg';
        if (is_file($target)) {
            return $target;
        }

        // Serialize concurrent requests for the same page and publish atomically.
        $lock = fopen($target . '.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX)) {
            throw new \RuntimeException('Unable to lock the scan preview.');
        }
        $temporary = null;
        try {
            if (!is_file($target)) {
                $image = new \Imagick();
                try {
                    $image->readImage($source);
                    $image->setIteratorIndex(0);
                    $image->autoOrient();
                    if ($image->getImageWidth() > 1600 || $image->getImageHeight() > 2200) {
                        $image->thumbnailImage(1600, 2200, true);
                    }
                    $image->setImageBackgroundColor('white');
                    $flattened = $image->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
                    try {
                        $flattened->setImageFormat('jpeg');
                        $flattened->setImageCompressionQuality(82);
                        $flattened->stripImage();
                        $temporary = tempnam($directory, 'preview-');
                        if (!$flattened->writeImage($temporary) || !rename($temporary, $target)) {
                            throw new \RuntimeException('Unable to write the scan preview.');
                        }
                    } finally {
                        $flattened->clear();
                    }
                } finally {
                    $image->clear();
                }
            }
        } finally {
            if ($temporary && is_file($temporary)) {
                unlink($temporary);
            }
            flock($lock, LOCK_UN);
            fclose($lock);
        }
        return $target;
    }
}
