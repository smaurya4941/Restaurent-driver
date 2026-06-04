<?php

namespace App\Commands;

use App\Services\IncentiveEngineService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RecalculateIncentives extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'incentives:recalculate';
    protected $description = 'Recalculate driver monthly incentive summaries for a month or date range.';
    protected $usage = 'incentives:recalculate --from=YYYY-MM --to=YYYY-MM [--driver_id=123]';
    protected $arguments = [];
    protected $options = [
        '--from' => 'Starting month in YYYY-MM format.',
        '--to' => 'Ending month in YYYY-MM format. Defaults to --from.',
        '--driver_id' => 'Optional driver id for targeted recalculation.',
    ];

    public function run(array $params)
    {
        $from = (string) ($this->readOption('from') ?? '');
        $to = (string) ($this->readOption('to') ?? $from);
        $driverIdOption = $this->readOption('driver_id');

        if (!$this->isValidMonth($from) || !$this->isValidMonth($to)) {
            CLI::error('Please provide valid --from and --to values in YYYY-MM format.');
            return;
        }

        [$fromYear, $fromMonth] = array_map('intval', explode('-', $from));
        [$toYear, $toMonth] = array_map('intval', explode('-', $to));

        if (strtotime($from . '-01') > strtotime($to . '-01')) {
            CLI::error('The --from month must not be later than the --to month.');
            return;
        }

        $driverId = $driverIdOption === null ? null : (int) $driverIdOption;
        $count = (new IncentiveEngineService())->recomputeRange($driverId, $fromYear, $fromMonth, $toYear, $toMonth);

        CLI::write('Recalculated ' . $count . ' driver-month summary record(s).', 'green');
    }

    private function isValidMonth(string $value): bool
    {
        return (bool) preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $value);
    }

    private function readOption(string $name): string|bool|null
    {
        $direct = CLI::getOption($name);
        if ($direct !== null) {
            return $direct;
        }

        foreach (CLI::getOptions() as $key => $value) {
            if (!str_starts_with((string) $key, $name . '=')) {
                continue;
            }

            return substr((string) $key, strlen($name) + 1);
        }

        return null;
    }
}
