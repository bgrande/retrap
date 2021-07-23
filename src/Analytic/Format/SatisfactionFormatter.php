<?php

declare(strict_types=1);

require_once __DIR__ . '/Formatable.php';
require_once dirname(__DIR__) . '/Entity/Frappe.php';
require_once dirname(__DIR__) . '/Entity/FrappeDataset.php';

final class SatisfactionFormatter implements Formatable {
    private const TYPE = 'pie';

    /**
     * @param stdClass $toFormat
     * @return string json encoded string
     */
    public function format(\stdClass $toFormat): string {
        $byAnswer = $this->getByAnswer($toFormat);
        $datasets = [];

        $name = $toFormat->name;
        $type = self::TYPE;
        $labels = ['Not helpful', 'Don\'t like it', 'Little bit helpful', 'Mostly helpful', 'Helpful'];

        $datasets[] = new FrappeDataset($name, $type ?? '', array_values($byAnswer));

        $output = new Frappe($labels, $datasets);
        return json_encode($output);
    }

    /**
     * @param stdClass $toFormat
     * @return array
     * @throws Exception
     */
    private function getByAnswer(stdClass $toFormat): array {
        $byAnswer = array_fill(0,4, 0);

        foreach ($toFormat->values as $value) {
            $byAnswer[$value]++;
        }

        return $byAnswer;
    }
}
