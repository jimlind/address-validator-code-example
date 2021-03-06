<?php

use JimLind\Helpers\OutputHelper;
use JimLind\Models\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

class OutputHelperTest extends TestCase
{
    public function testValidAddress(): void
    {
        $originalStreetAddress = uniqid();
        $originalCity = uniqid();
        $originalPostalCode = uniqid();
        $originalAddress = new Address($originalStreetAddress, $originalCity, $originalPostalCode);

        $verifiedStreetAddress = uniqid();
        $verifiedCity = uniqid();
        $verifiedPostalCode = uniqid();
        $verifiedAddress = new Address($verifiedStreetAddress, $verifiedCity, $verifiedPostalCode);
        $verifiedAddress->setStatus(Address::VALID);

        $outputString = "$originalStreetAddress, $originalCity, $originalPostalCode -> $verifiedStreetAddress, $verifiedCity, $verifiedPostalCode";
        /** @var ConsoleOutput&MockObject */
        $output = $this->createMock(ConsoleOutput::class);
        $output->expects($this->once())
            ->method('writeln')
            ->with($this->equalTo($outputString));

        $outputHelper = new OutputHelper();
        $outputHelper->writeResults($output, [$originalAddress], [$verifiedAddress]);
    }

    public function testInvalidAddress(): void
    {
        $originalStreetAddress = uniqid();
        $originalCity = uniqid();
        $originalPostalCode = uniqid();
        $originalAddress = new Address($originalStreetAddress, $originalCity, $originalPostalCode);

        $verifiedAddress = new Address('', '', '');
        $verifiedAddress->setStatus(Address::INVALID);

        $outputString = "$originalStreetAddress, $originalCity, $originalPostalCode -> Invalid Address";
        /** @var ConsoleOutput&MockObject */
        $output = $this->createMock(ConsoleOutput::class);
        $output->expects($this->once())
            ->method('writeln')
            ->with($this->equalTo($outputString));

        $outputHelper = new OutputHelper();
        $outputHelper->writeResults($output, [$originalAddress], [$verifiedAddress]);
    }
}
