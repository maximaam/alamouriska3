<?php

declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function md5, microtime, is_dir, sprintf;

final class FileUploader
{
    public function upload(UploadedFile $file, string $destinationPath, string $filename = null): ?string
    {
        if (null === $filename) {
            $filename = md5($file->getClientOriginalName().microtime());
        }

        $filename .= '.'.$file->guessExtension();

        if (false === is_dir($destinationPath)) {
            throw new InvalidArgumentException(sprintf('Directory [%s] does not exist.', $destinationPath));
        }

        $file->move($destinationPath, $filename);

        return $filename;
    }
}