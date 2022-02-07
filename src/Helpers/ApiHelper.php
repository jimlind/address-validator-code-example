<?php
namespace JimLind\Helpers;

use GuzzleHttp\Client;
use JimLind\Factories\AddressFactory;
use JimLind\Models\Address;

class ApiHelper
{
    const URL = 'https://api.address-validator.net/api/verify';

    const STATUS_VALID = 'VALID';
    const STATUS_SUSPECT = 'SUSPECT';
    const STATUS_INVALID = 'INVALID';

    private $addressFactory = null;
    private $apiKey = '';
    private $guzzleClient = null;

    public function __construct(AddressFactory $addressFactory, string $apiKey, Client $guzzleClient)
    {
        $this->addressFactory = $addressFactory;
        $this->apiKey = $apiKey;
        $this->guzzleClient = $guzzleClient;
    }

    public function validateAddressList(array $addressList): array {
        return array_map('self::getVerificationResult', $addressList);
    }

    private function getVerificationResult(Address $originalAddress): Address {
        $parameterList = [
            'StreetAddress' => $originalAddress->getStreetAddress(),
            'City' => $originalAddress->getCity(),
            'PostalCode' => $originalAddress->getPostalCode(),
            'CountryCode' => $originalAddress->getCountryCode(),
            'APIKey' => $this->apiKey,
        ];
        $response = $this->guzzleClient->request('GET', self::URL, ['query' => $parameterList]);

        // Validate API response headers
        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();
        $contentType = $response->getHeader('content-type')[0];
        if ($code !== 200 || $reason !== 'OK' || strpos($contentType, 'application/json') === false) {
            // Not valid response headers - Return unverified originalAddress
            return $originalAddress;
        }

        $jsonResponse = json_decode($response->getBody());
        if (!in_array($jsonResponse->status, [self::STATUS_VALID, self::STATUS_SUSPECT, self::STATUS_INVALID])) {
            // Not valid json status - Return unverified originalAddress
            return $originalAddress;
        }

        $newAddress = $this->addressFactory->build($jsonResponse->addressline1, $jsonResponse->city, $jsonResponse->postalcode);
        switch ($jsonResponse->status) {
            case self::STATUS_VALID:
            case self::STATUS_SUSPECT:
                $newAddress->setStatus(Address::VALID);
                break;
            case self::STATUS_INVALID:
                $newAddress->setStatus(Address::INVALID);
                break;
            }
       
        return $newAddress;
    }
}