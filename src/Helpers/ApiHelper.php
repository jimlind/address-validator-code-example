<?php
namespace JimLind\Helpers;

use GuzzleHttp\Client;
use JimLind\Models\Address;

class ApiHelper
{
    const URL = 'https://api.address-validator.net/api/verify';

    const STATUS_VALID = 'VALID';
    const STATUS_SUSPECT = 'SUSPECT';
    const STATUS_INVALID = 'INVALID';

    private $apiKey = '';
    private $guzzleClient = null;

    public function __construct(string $apiKey, Client $guzzleClient)
    {
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

        $newAddress = new Address($jsonResponse->addressline1, $jsonResponse->city, $jsonResponse->postalcode);
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

//https://api.originalAddress-validator.net/api/verify?StreetAddress=Heilsbronner%20Str.%204&City=Neuendettelsau&PostalCode=91564&CountryCode=de&Geocoding=true&APIKey=av-d2e28fbffeb6daba18e92d5a88c29f4a

//$response = $client->request('')        
// $Params = array('StreetAddress' => $StreetAddress,
//                 'City' => $City,
//                 'PostalCode' => $PostalCode,
//                 'State' => $State,
//                 'CountryCode' => $CountryCode,
//                 'Locale' => $Locale,
//                 'APIKey' => 'your API key');

        // $client = new Client([
        //     // Base URI is used with relative requests
        //     'base_uri' => 'http://httpbin.org',
        //     // You can set any number of default request options.
        //     'timeout'  => 2.0,
        // ]);



// $headers = ['X-Foo' => 'Bar'];
// $body = 'Hello!';
// $request = new Request('HEAD', 'http://httpbin.org/head', $headers, $body);


// $client->request('GET', 'http://httpbin.org', [
//     'query' => ['foo' => 'bar']
// ]);

// $code = $response->getStatusCode(); // 200
// $reason = $response->getReasonPhrase(); // OK

// $body = $response->getBody();
// // Implicitly cast the body to a string and echo it
// echo $body;
// // Explicitly cast the body to a string
// $stringBody = (string) $body;

// ...
// // build API request
// $APIUrl = 'https://api.originalAddress-validator.net/api/verify';
// $Params = array('StreetAddress' => $StreetAddress,
//                 'City' => $City,
//                 'PostalCode' => $PostalCode,
//                 'State' => $State,
//                 'CountryCode' => $CountryCode,
//                 'Locale' => $Locale,
//                 'APIKey' => 'your API key');
// $Request = http_build_query($Params, '', '&');
// $ctxData = array(
//     'method'=>"POST",
//     'header'=>"Connection: close\r\n".
//     "Content-Type: application/x-www-form-urlencoded\r\n".
//     "Content-Length: ".strlen($Request)."\r\n",
//     'content'=>$Request);
// $ctx = stream_context_create(array('http' => $ctxData));

// // send API request
// $result = json_decode(file_get_contents(
//     $APIUrl, false, $ctx));

// // check API result
// if ($result && $result->{'status'} == 'VALID') {
//     $formattedaddress = $result->{'formattedaddress'};
// } else {
//     echo $result->{'info'};
// }