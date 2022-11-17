<?php
namespace App\VGA;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSystem
{
    const FILESIZE_LIMIT = 1024 * 1024 * 10;

    const EXTENSION_MAPPING = [
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
        'image/gif' => '.gif',
        'audio/ogg' => '.ogg',
        'video/ogg' => '.ogg',
        'application/ogg' => '.ogg',
    ];
    /**
     * Converts a number of bytes into a human-readable filesize.
     * This implementation is efficient, but will sometimes return a value that's less than one due
     * to the differences between 1000 and 1024 (for example, 0.98 GB)
     * @param int $bytes File size in bytes.
     * @return string     The human-readable string, to two decimal places.
     */
    public static function humanFilesize(int $bytes): string
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        // Determine the magnitude of the size from the length of the string.
        // Use the last element of the size array as the upper bound.
        $factor = min(floor((strlen($bytes) - 1) / 3), count($size) - 1);
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . $size[$factor];
    }
}
