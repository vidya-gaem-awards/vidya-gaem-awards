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
     * This function does not adhere to security best practices (maybe?)
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $filename
     * @return string
     * @throws \Exception
     * @internal param $file_data
     */
    public static function handleUploadedFile(UploadedFile $file, string $directory, string $filename)
    {
        if ($file === null) {
            throw new \Exception('No file was uploaded');
        } elseif (!$file->isValid()) {
            throw new \Exception($file->getErrorMessage());
        } elseif (!in_array($file->getClientMimeType(), array_keys(self::EXTENSION_MAPPING), true)) {
            throw new \Exception('Invalid MIME type (' . $file->getClientMimeType() . ')');
        } elseif ($file->getSize() > self::FILESIZE_LIMIT) {
            throw new \Exception('Filesize of ' . self::humanFilesize($file->getSize()) . ' exceeds limit of ' . self::humanFilesize(self::FILESIZE_LIMIT));
        }

        if (!file_exists(__DIR__ . '/../../public/uploads/' . $directory)) {
            mkdir(__DIR__ . '/../../public/uploads/' . $directory, 0777, true);
        }

        $filename_to_use = $filename . self::EXTENSION_MAPPING[$file->getClientMimeType()];

        $file->move(__DIR__ . '/../../public/uploads/' . $directory . '/', $filename_to_use);
        return '/uploads/' . $directory . '/' . $filename_to_use;
    }

    public static function deleteFile(string $directory, string $filename)
    {
        unlink(__DIR__ . '/../../public/uploads/' . $directory . '/' . $filename);
    }

    /**
     * Converts a number of bytes into a human-readable filesize.
     * This implementation is efficient, but will sometimes return a value that's less than one due
     * to the differences between 1000 and 1024 (for example, 0.98 GB)
     * @param  int $bytes File size in bytes.
     * @return string     The human-readable string, to two decimal places.
     */
    public static function humanFilesize($bytes)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        // Determine the magnitude of the size from the length of the string.
        // Use the last element of the size array as the upper bound.
        $factor = min(floor((strlen($bytes) - 1) / 3), count($size) - 1);
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . $size[$factor];
    }
}
