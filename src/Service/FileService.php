<?php
namespace App\Service;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    const FILESIZE_LIMIT = 1024 * 1024 * 10;

    const EXTENSION_MAPPING = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'audio/ogg' => 'ogg',
        'video/ogg' => 'ogg',
        'application/ogg' => 'ogg',
    ];

    /** @var string */
    private $uploadDirectory;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(string $projectDir, EntityManagerInterface $em)
    {
        $this->uploadDirectory = $projectDir . '/public/uploads/';
        $this->em = $em;
    }

    /**
     * This function does not adhere to security best practices (maybe?)
     *
     * @param UploadedFile $file
     * @param string $entityType
     * @param string $directory
     * @param string $filename
     * @return string
     * @throws \Exception
     * @internal param $file_data
     */
    public function handleUploadedFile(UploadedFile $file, string $entityType, string $directory, string $filename): File
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

        if (!file_exists($this->uploadDirectory . $directory)) {
            mkdir($this->uploadDirectory . $directory, 0777, true);
        }

        $filename = $filename . '-' . time();

        $fileEntity = new File();
        $fileEntity->setSubdirectory($directory);
        $fileEntity->setFilename($filename);
        $fileEntity->setExtension(self::EXTENSION_MAPPING[$file->getClientMimeType()]);
        $fileEntity->setEntity($entityType);

        $this->em->persist($fileEntity);
        $this->em->flush();

        $file->move($this->uploadDirectory . $directory . '/', $fileEntity->getFullFilename());

        return $fileEntity;
    }

    public function deleteFile(File $file)
    {
        unlink($this->uploadDirectory . $file->getRelativePath());
        $this->em->remove($file);
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
