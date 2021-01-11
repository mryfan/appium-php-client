<?php

namespace PHPUnit\Extensions\AppiumTestCase\SessionCommand;

use PHPUnit\Extensions\Selenium2TestCase\Command;
use PHPUnit\Framework\MockObject\BadMethodCallException;

class Context extends Command
{
    public function __construct($name, $commandUrl)
    {
        if (is_string($name)) {
            $jsonParameters = ['name' => $name];
        } elseif ($name == NULL) {
            $jsonParameters = NULL;
        } else {
            throw new BadMethodCallException("Wrong Parameters for context().");
        }

        parent::__construct($jsonParameters, $commandUrl);
    }

    public function httpMethod()
    {
        if ($this->jsonParameters) {
            return 'POST';
        }

        return 'GET';
    }
}
