<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Service\RateImport\RateImportService;
use PHPUnit\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RatesGetCommand extends Command
{
    protected static $defaultName = 'app:rates:get';

    private RateImportService $rateImportService;

    public function __construct(RateImportService $rateImportService, string $name = null)
    {
        parent::__construct($name);
        $this->rateImportService = $rateImportService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $amount = $this->rateImportService->import();
        } catch (Exception $exception) {
            //@TODO add logging here
            return 1;
        }

        $output->writeln(sprintf("Rates imported successfully, total currencies: %d", $amount));

        return 0;
    }
}