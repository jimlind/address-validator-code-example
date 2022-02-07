<?php
namespace JimLind\Helpers;

use JimLind\Models\Address;
use Symfony\Component\Console\Output\ConsoleOutput;

class OutputHelper
{
    public function writeResults(ConsoleOutput $output, array $rawAddressList, array $verifiedAddressList) {
        for ($index = 0; $index <= count($rawAddressList)-1; $index++) {

            $rawAddressString = $this->addressToString($rawAddressList[$index]);
            $verifiedAddressString = $this->addressToString($verifiedAddressList[$index]);

            $output->writeln($rawAddressString . ' -> ' . $verifiedAddressString);
        }
    }

    private function addressToString(Address $address): string {
        if ($address->getStatus() === Address::INVALID) {
            return 'Invalid Address';
        }

        $valueList = [$address->getStreetAddress(), $address->getCity(), $address->getPostalCode()];
        
        return implode(', ', $valueList);
    }
}