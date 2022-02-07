<?php
namespace JimLind\Commands;

use JimLind\Factories\AddressFactory;
use JimLind\Helpers\ApiHelper;
use JimLind\Helpers\FileHelper;
use JimLind\Helpers\OutputHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidationCommand extends Command
{
    protected static $defaultName = 'validation';

    private $addressFactory = null;
    private $apiHelper = null;
    private $fileHelper = null;

    public function __construct(AddressFactory $addressFactory, ApiHelper $apiHelper, FileHelper $fileHelper, OutputHelper $outputHelper)
    {
        parent::__construct();
        $this->addressFactory = $addressFactory;
        $this->apiHelper = $apiHelper;
        $this->fileHelper = $fileHelper;
        $this->outputHelper = $outputHelper;
    }

    protected function configure()
    {
        $this->setDescription('Validates all addresses found on a file');
        $this->addArgument('file', InputArgument::REQUIRED, 'A string that will be reversed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parsedAddressData = $this->fileHelper->csvFileToArray($input->getArgument('file'));
        $rawAddressList = array_map('self::translateToAddressModel', $parsedAddressData);
        $verifiedAddressList = $this->apiHelper->validateAddressList($rawAddressList);

        // var_dump($rawAddressList);
        // var_dump($formattedAddressList);

        $this->outputHelper->writeResults($output, $rawAddressList, $verifiedAddressList);

        //$output->writeln('My work here is done.');

        return Command::SUCCESS;
    }

    private function translateToAddressModel($value)
    {
        return $this->addressFactory->build($value[0], $value[1], $value[2]);
    }
}