<?php
namespace VGA;

class FileSystem
{
    const FILESIZE_LIMIT = 1024 * 1024 * 2;

    public static $file_upload_errors = [
        0 => 'The file was uploaded successfully.',
        1 => 'The uploaded file is too large.',
        2 => 'The uploaded file is too large.',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    ];

    /**
     * This function does not adhere to security best practices
     *
     * @param $file_data
     * @param string $directory
     * @param string $filename
     * @return string
     * @throws \Exception
     */
    public static function handleUploadedFile($file_data, string $directory, string $filename)
    {
        if ($file_data === null) {
            throw new \Exception(self::$file_upload_errors[4]);
        } elseif ($file_data['error'] > 0) {
            throw new \Exception(self::$file_upload_errors[$file_data['error']]);
        } elseif (!in_array($file_data['type'], ['image/png', 'image/jpeg', 'image/gif'], true)) {
            throw new \Exception('Invalid MIME type.');
        } elseif ($file_data['size'] > self::FILESIZE_LIMIT) {
            throw new \Exception('Filesize of ' . self::humanFilesize($file_data['size']) . ' exceeds limit of ' . self::humanFilesize(self::FILESIZE_LIMIT));
        }

        if (!file_exists(__DIR__ . '/../../public/uploads/' . $directory)) {
            mkdir(__DIR__ . '/../../public/uploads/' . $directory, 0777, true);
        }

        $extensions = [
            'image/png' => '.png',
            'image/jpeg' => '.jpg',
            'image/gif' => '.gif'
        ];

        $filename_to_use = $filename . $extensions[$file_data['type']];

        $contents = file_get_contents($file_data['tmp_name']);
        file_put_contents(__DIR__ . '/../../public/uploads/' . $directory . '/' . $filename_to_use, $contents);

        return '/uploads/' . $directory . '/' . $filename_to_use;
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
