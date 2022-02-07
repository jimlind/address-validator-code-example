<?php
namespace JimLind\Models;

class Address
{
    const NOT_VALIDATED = 'not-validated';
    const VALID = 'valid';
    const INVALID = 'invalid';

    private $streetAddress = '';
    private $city = '';
    private $postalCode = '';
    private $status = self::NOT_VALIDATED;

    public function __construct(string $streetAddress, string $city, string $postalCode){
        $this->streetAddress = $streetAddress;
        $this->city = $city;
        $this->postalCode = $postalCode;
    }

    public function getStreetAddress() {
        return $this->streetAddress;
    }

    public function getCity() {
        return $this->city;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }

    public function getCountryCode() {
        return 'US';
    }

    public function setStatus(string $status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
    }
}