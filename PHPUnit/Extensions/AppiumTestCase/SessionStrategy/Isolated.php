<?php

namespace FanYang\Appium\AppiumTestCase\SessionStrategy;


use FanYang\Appium\AppiumTestCase\AppiumTestCaseDriver;
use PHPUnit\Extensions\Selenium2TestCase\Session;
use PHPUnit\Extensions\Selenium2TestCase\SessionStrategy;
use PHPUnit\Extensions\Selenium2TestCase\URL;

class Isolated implements SessionStrategy
{
    public function session(array $parameters)
    {
        $seleniumServerUrl = URL::fromHostAndPort($parameters['host'], $parameters['port'], $parameters['secure']);
        $driver            = new AppiumTestCaseDriver($seleniumServerUrl, $parameters['seleniumServerRequestsTimeout']);
        $capabilities      = array_merge($parameters['desiredCapabilities'], [
                'browserName' => $parameters['browserName'],
            ]);
        $session           = $driver->startSession($capabilities, $parameters['browserUrl']);

        return $session;
    }

    public function notSuccessfulTest()
    {
    }

    public function endOfTest(Session $session = NULL)
    {
        if ($session !== NULL) {
            $session->stop();
        }
    }
}
