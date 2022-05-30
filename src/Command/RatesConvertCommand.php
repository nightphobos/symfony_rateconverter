<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\DTO\ConvertDTO;
use App\Service\RateConvert\RateConvertService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RatesConvertCommand extends Command
{
    protected static $defaultName = 'app:rates:convert';

    private RateConvertService $rateConvertService;

    public function __construct(RateConvertService $rateConvertService, string $name = null)
    {
        parent::__construct($name);
        $this->rateConvertService = $rateConvertService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('from', InputArgument::REQUIRED, 'Current we convert')
            ->addArgument('to', InputArgument::REQUIRED, 'Current we convert to')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount we are converting')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $amount = $input->getArgument('amount');

        /** @var ConvertDTO $result */
        $result = $this->rateConvertService->rateCount($from, $to, $amount);

        if (is_null($result)){
            $output->writeln("Conversion is not possible");
        } else {
            $output->writeln(sprintf("Conversion is possible, chain: %s, result sum %f", $result->chain, $result->result));
        }

        return 0;
    }
}