<?php

declare(strict_types = 1);

final class EncodeName {
    public const FILE_EXTENSION = '.json';

    private const MAX_COUNT = 3;

    private $counter = 0;

    public function encode(string $path): string {
        try {
            $fileName = sha1(random_bytes(36));
            $filePath = $path . $fileName . self::FILE_EXTENSION;

            if ($this->counter <= self::MAX_COUNT && file_exists($filePath)) {
                $this->counter++;
                return $this->encode($path);
            }

            if ($this->counter >= self::MAX_COUNT && file_exists($filePath)) {
                error_log('could not derive filename');
                return '';
            }

            return $fileName;
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }

        return '';
    }
}
