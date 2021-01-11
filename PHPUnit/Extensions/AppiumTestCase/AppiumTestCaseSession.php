<?php

namespace FanYang\Appium\AppiumTestCase;

use PHPUnit\Extensions\Selenium2TestCase\Element;
use PHPUnit\Extensions\Selenium2TestCase\ElementCriteria;
use PHPUnit\Extensions\Selenium2TestCase\Session;
use PHPUnit\Extensions\Selenium2TestCase\Session\Timeouts;
use PHPUnit\Extensions\Selenium2TestCase\URL;

class AppiumTestCaseSession extends Session
{
    /**
     * @var string  the base URL for this session,
     *              which all relative URLs will refer to
     */
    private $baseUrl;

    public function __construct($driver, URL $url, URL $baseUrl, Timeouts $timeouts)
    {
        $this->baseUrl = $baseUrl;
        parent::__construct($driver, $url, $baseUrl, $timeouts);
    }

    /**
     * @param   array   WebElement JSON object
     *
     * @return Element
     */
    public function elementFromResponseValue($value)
    {
        return Element::fromResponseValue($value, $this->getSessionUrl()->descend('element'), $this->driver);
    }

    public function reset()
    {
        $url = $this->getSessionUrl()->addCommand('appium/app/reset');
        $this->driver->curl('POST', $url);
    }

    public function appStrings($language = NULL)
    {
        $url  = $this->getSessionUrl()->addCommand('appium/app/strings');
        $data = [];
        if (!is_null($language)) {
            $data['language'] = $language;
        }

        return $this->driver->curl('POST', $url, $data)->getValue();
    }

    public function keyEvent($keycode, $metastate = NULL)
    {
        $url  = $this->getSessionUrl()->addCommand('appium/device/keyevent');
        $data = [
            'keycode'   => $keycode,
            'metastate' => $metastate,
        ];
        $this->driver->curl('POST', $url, $data);
    }

    public function currentActivity()
    {
        $url = $this->getSessionUrl()->addCommand('appium/device/current_activity');

        return $this->driver->curl('GET', $url)->getValue();
    }

    public function currentPackage()
    {
        $url = $this->getSessionUrl()->addCommand('appium/device/current_package');

        return $this->driver->curl('GET', $url)->getValue();
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function postCommand($name, ElementCriteria $criteria)
    {
        $response = $this->driver->curl('POST', $this->url->addCommand($name), $criteria->getArrayCopy());

        return $response->getValue();
    }

    protected function initCommands()
    {
        $baseUrl  = $this->baseUrl;
        $commands = parent::initCommands();

        $commands['contexts'] = 'PHPUnit\Extensions\Selenium2TestCase\SessionCommand\GenericAccessor';
        $commands['context']  = 'PHPUnit\Extensions\AppiumTestCase\SessionCommand\Context';

        return $commands;
    }
}
