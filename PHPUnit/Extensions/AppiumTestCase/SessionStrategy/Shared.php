<?php

namespace FanYang\Appium\AppiumTestCase\SessionStrategy;

use PHPUnit\Extensions\Selenium2TestCase\Session;
use PHPUnit\Extensions\Selenium2TestCase\SessionStrategy;


class Shared implements SessionStrategy
{
    private $original;
    private $session;
    private $mainWindow;
    private $lastTestWasNotSuccessful = false;

    public function __construct(SessionStrategy $originalStrategy)
    {
        $this->original = $originalStrategy;
    }

    public function session(array $parameters)
    {
        if ($this->lastTestWasNotSuccessful) {
            if ($this->session !== NULL) {
                $this->session->stop();
                $this->session = NULL;
            }
            $this->lastTestWasNotSuccessful = false;
        }
        if ($this->session === NULL) {
            $this->session    = $this->original->session($parameters);
            $this->mainWindow = $this->session->windowHandle();
        } else {
            $this->session->window($this->mainWindow);
        }

        return $this->session;
    }

    public function notSuccessfulTest()
    {
        $this->lastTestWasNotSuccessful = true;
    }

    public function endOfTest(Session $session = NULL)
    {
    }
}
