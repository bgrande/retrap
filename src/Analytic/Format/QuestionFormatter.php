<?php

declare(strict_types=1);

require_once __DIR__ . '/Formatable.php';
require_once dirname(__DIR__) . '/Entity/Frappe.php';
require_once dirname(__DIR__) . '/Entity/FrappeDataset.php';

final class QuestionFormatter implements Formatable {
    private const TYPE = 'percentage';

    /**
     * @param stdClass $toFormat
     * @return string json encoded string
     */
    public function format(\stdClass $toFormat): string {
        $output = [];

        $type = self::TYPE;
        foreach ($toFormat->set as $question) {
            $dataset = new FrappeDataset($question->name, $type ?? '', array_values($question->values));
            $output[] = new Frappe(array_values($question->labels), [$dataset]);
        }

        return json_encode($output);
    }
}
