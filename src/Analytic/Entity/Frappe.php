<?php

declare(strict_types = 1);

require_once __DIR__ . '/FrappeDataset.php';

final class Frappe {
    public array $labels = [];

    public array $datasets = [];

    /**
     * @param array $labels
     * @param FrappeDataset[] $datasets
     */
    public function __construct(array $labels, array $datasets) {
        $this->labels = $labels;
        $this->datasets = $datasets;
    }
}
