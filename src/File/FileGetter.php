<?php

declare(strict_types = 1);

final class FileGetter {
    public const FILE_EXTENSION = '.json';

    public function get(string $path): array {
        try {
            $files = glob($path . '/*');

            if ($files && is_array($files)) {
                return $files;
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }

        return [];
    }
}
