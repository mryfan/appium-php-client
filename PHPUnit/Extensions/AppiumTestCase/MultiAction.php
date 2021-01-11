<?php

namespace FanYang\Appium\AppiumTestCase;

use FanYang\Appium\AppiumTestCase\AppiumTestCaseElement;
use PHPUnit\Extensions\Selenium2TestCase\URL;

class MultiAction
{
    private $sessionUrl;
    private $driver;
    private $element;
    private $actions;


    public function __construct(URL $sessionUrl, AppiumTestCaseDriver $driver, AppiumTestCaseElement $element = NULL)
    {
        $this->sessionUrl = $sessionUrl;
        $this->driver     = $driver;
        $this->element    = $element;
        $this->actions    = [];
    }

    public function add(TouchAction $action)
    {
        if (is_null($this->actions)) {
            $this->actions = [];
        }

        $this->actions[] = $action;
    }

    public function perform()
    {
        $params = $this->getJSONWireGestures();

        $url = $this->sessionUrl->descend('touch')->descend('multi')->descend('perform');
        $this->driver->curl('POST', $url, $params);
    }

    public function getJSONWireGestures()
    {
        $actions = [];
        foreach ($this->actions as $action) {
            $actions[] = $action->getJSONWireGestures();
        }

        $gestures = [
            'actions' => $actions,
        ];
        if (!is_null($this->element)) {
            $gestures['elementId'] = $this->element->getId();
        }

        return $gestures;
    }
}
