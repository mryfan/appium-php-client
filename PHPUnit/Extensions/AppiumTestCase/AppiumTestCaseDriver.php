<?php

namespace FanYang\Appium\AppiumTestCase;

use PHPUnit\Extensions\Selenium2TestCase\Driver;
use PHPUnit\Extensions\Selenium2TestCase\Session\Timeouts;
use PHPUnit\Extensions\Selenium2TestCase\URL;


class AppiumTestCaseDriver extends Driver
{
    private $seleniumServerUrl;
    private $seleniumServerRequestsTimeout;

    public function __construct(URL $seleniumServerUrl, $timeout = 60)
    {
        parent::__construct($seleniumServerUrl, $timeout);

        $this->seleniumServerUrl             = $seleniumServerUrl;
        $this->seleniumServerRequestsTimeout = $timeout;
    }

    public function startSession(array $desiredCapabilities, URL $browserUrl)
    {
        $sessionCreation = $this->seleniumServerUrl->descend("/wd/hub/session");
        $response        = $this->curl('POST', $sessionCreation, [
            'desiredCapabilities' => $desiredCapabilities,
        ]);
        $sessionPrefix   = $response->getURL();

        $timeouts = new Timeouts($this, $sessionPrefix->descend('timeouts'),
            $this->seleniumServerRequestsTimeout * 1000);

        return new AppiumTestCaseSession($this, $sessionPrefix, $browserUrl, $timeouts);
    }

    public function getServerUrl()
    {
        return $this->seleniumServerUrl;
    }
}
