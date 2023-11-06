<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/NoAnalyticDataException.php';

final class FrappeDataset {
    public string $name = '';

    public string $type = '';

    public array $values = [];

    public function __construct(string $name, string $type, array $values) {
        if (empty($values) || $name === '') {
            throw new NoAnalyticDataException();
        }

        $this->name = $name;
        $this->type = $type;
        $this->values = $values;
    }
}
