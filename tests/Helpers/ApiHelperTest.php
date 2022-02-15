<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JimLind\Factories\AddressFactory;
use JimLind\Helpers\ApiHelper;
use JimLind\Models\Address;
use PHPUnit\Framework\TestCase;

class ApiHelperTest extends TestCase
{
    public function testValueIsUnchangedOnBadResponse(): void
    {
        $mock = new MockHandler([new Response('200', ['content-type' => ['text/plain']])]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $apiHelper = new ApiHelper($this->getAddressFactory(), uniqid(), $client);
        $inputAddressList = [$this->createMock(Address::class)];
        $outputAddressList = $apiHelper->validateAddressList($inputAddressList);

        // Input the same as the output. Nothing has changed.
        $this->assertCount(1, $outputAddressList);
        $this->assertSame($inputAddressList[0], $outputAddressList[0]);
    }

    public function testCanParseBadKey(): void
    {
        $body = file_get_contents(__DIR__ . '/data/response-bad-key.json');
        $mock = new MockHandler([new Response('200', ['content-type' => ['application/json']], $body)]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $apiHelper = new ApiHelper($this->getAddressFactory(), uniqid(), $client);
        $inputAddressList = [$this->createMock(Address::class)];
        $outputAddressList = $apiHelper->validateAddressList($inputAddressList);

        // Input the same as the output. Nothing has changed.
        $this->assertSame($inputAddressList[0], $outputAddressList[0]);
    }

    public function testCanParseInvalidResponse(): void
    {
        $body = file_get_contents(__DIR__ . '/data/response-invalid.json');
        $mock = new MockHandler([new Response('200', ['content-type' => ['application/json']], $body)]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $apiHelper = new ApiHelper($this->getAddressFactory(), uniqid(), $client);
        $inputAddressList = [$this->createMock(Address::class)];
        $outputAddressList = $apiHelper->validateAddressList($inputAddressList);

        // Output has status of invalid and correct address
        $this->assertEquals($outputAddressList[0]->getStatus(), Address::INVALID);
        $this->assertEquals($outputAddressList[0]->getStreetAddress(), '1 Empora St');
        $this->assertEquals($outputAddressList[0]->getCity(), 'Title');
        $this->assertEquals($outputAddressList[0]->getPostalCode(), '11111');
    }

    public function testCanParseSuspectResponse(): void
    {
        $body = file_get_contents(__DIR__ . '/data/response-suspect.json');
        $mock = new MockHandler([new Response('200', ['content-type' => ['application/json']], $body)]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $apiHelper = new ApiHelper($this->getAddressFactory(), uniqid(), $client);
        $inputAddressList = [$this->createMock(Address::class)];
        $outputAddressList = $apiHelper->validateAddressList($inputAddressList);

        // Output has status of valid and correct address
        $this->assertEquals($outputAddressList[0]->getStatus(), Address::VALID);
        $this->assertEquals($outputAddressList[0]->getStreetAddress(), '123 E Main St');
        $this->assertEquals($outputAddressList[0]->getCity(), 'Columbus');
        $this->assertEquals($outputAddressList[0]->getPostalCode(), '43215-5207');
    }

    private function getAddressFactory()
    {
        // TODO: AddressFactory doesn't do much (by design) but would make sense to mock this.
        return new AddressFactory();
    }
}
