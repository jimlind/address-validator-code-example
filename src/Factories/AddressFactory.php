<?php
namespace JimLind\Factories;

use JimLind\Models\Address;

class AddressFactory
{
    public function build(string $streetAddress, string $city, string $postalCode) {
        return new Address($streetAddress, $city, $postalCode);
    }
}