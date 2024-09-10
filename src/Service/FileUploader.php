<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    private SluggerInterface $slugger;
    private string $uploadsPath;

    public function __construct(string $uploadsPath, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadFile(File $file): string
    {
        $fileName = $this->slugger
            ->slug(pathinfo($file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename(), PATHINFO_FILENAME))
            ->append('-' . uniqid())
            ->append('.' . $file->guessExtension())
            ->toString()
        ;

        $newFile = $file->move($this->uploadsPath, $fileName);

        if (! $newFile) {
            throw new \Exception("Не удалось записать файл: $fileName");
        }

        return $fileName;
    }
}