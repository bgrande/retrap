<?php

declare(strict_types=1);

require_once __DIR__ . '/Formatable.php';
require_once dirname(__DIR__) . '/Entity/Frappe.php';
require_once dirname(__DIR__) . '/Entity/FrappeDataset.php';

final class PerMonthFormatter implements Formatable {
    private const TYPE = 'bar';

    /**
     * @param stdClass $toFormat
     * @return string json encoded string
     */
    public function format(\stdClass $toFormat): string {
        $byYear = $this->getByYear($toFormat);
        $labels = [];
        $datasets = [];

        foreach ($byYear as $year => $values) {
            $name = $toFormat->name . ' ' . $year;
            $type = self::TYPE;
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

            $datasets[] = new FrappeDataset($name, $type ?? '', array_values($values));
        }

        $output = new Frappe($labels, $datasets);
        return json_encode($output);
    }

    /**
     * @param stdClass $toFormat
     * @return array
     * @throws Exception
     */
    private function getByYear(stdClass $toFormat): array {
        $byYear = [];
        foreach ($toFormat->values as $value) {
            $date = new \DateTime($value);

            $year = $date->format('Y');

            if (!isset($byYear[$year])) {
                $byYear[$year] = array_fill(1,12, 0);
            }

            $month = (int)$date->format('n');

            $byYear[$year][$month]++;
        }

        return $byYear;
    }
}
