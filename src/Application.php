<?php
namespace JimLind;

use JimLind\Commands\ValidationCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    public function __construct(ValidationCommand $validationCommand)
    {
        parent::__construct();
        $this->add($validationCommand);
    }
}