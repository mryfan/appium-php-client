<?php

namespace FanYang\Appium\AppiumTestCase;

use InvalidArgumentException;
use PHPUnit\Extensions\Selenium2TestCase\Driver;
use PHPUnit\Extensions\Selenium2TestCase\Element;
use PHPUnit\Extensions\Selenium2TestCase\ElementCriteria;
use PHPUnit\Extensions\Selenium2TestCase\URL;


class AppiumTestCaseElement extends Element
{
    public function byIOSUIAutomation($value)
    {
        return $this->by('-ios uiautomation', $value);
    }

    public function by($strategy, $value)
    {
        $el = $this->element($this->using($strategy)->value($value));

        return $el;
    }

    public function element(ElementCriteria $criteria)
    {
        $value = $this->postCommand('element', $criteria);

        return static::fromResponseValue($value, $this->getSessionUrl()->descend('element'), $this->driver);
    }

    /**
     * @return \self
     * @throws InvalidArgumentException
     */
    public static function fromResponseValue(array $value, URL $parentFolder, Driver $driver)
    {
        if (!isset($value['ELEMENT'])) {
            throw new InvalidArgumentException('Element not found.');
        }
        $url = $parentFolder->descend($value['ELEMENT']);

        return new self($driver, $url);
    }

    public function byAndroidUIAutomator($value)
    {
        return $this->by('-android uiautomator', $value);
    }

    public function byAccessibilityId($value)
    {
        return $this->by('accessibility id', $value);
    }

    public function setImmediateValue($value)
    {
        $data = [
            'id'    => $this->getId(),
            'value' => $value,
        ];
        $url  = $this->getSessionUrl()->descend('appium')->descend('element')->descend($this->getId())->descend('value');
        $this->driver->curl('POST', $url, $data);
    }

    // override to return Appium element

    public function setText($keys)
    {
        $data = [
            'id'    => $this->getId(),
            'value' => [$keys],
        ];
        $url  = $this->getSessionUrl()->descend('appium')->descend('element')->descend($this->getId())->descend('replace_value');
        $this->driver->curl('POST', $url, $data);
    }

    public function elements(ElementCriteria $criteria)
    {
        $values   = $this->postCommand('elements', $criteria);
        $elements = [];
        foreach ($values as $value) {
            $elements[] = static::fromResponseValue($value, $this->getSessionUrl()->descend('element'), $this->driver);
        }

        return $elements;
    }

    /**
     * @param   string  $value  e.g. 'container'
     *
     * @return Element
     */
    public function byClassName($value)
    {
        return $this->by('class name', $value);
    }

    /**
     * @param   string  $value  e.g. 'div.container'
     *
     * @return Element
     */
    public function byCssSelector($value)
    {
        return $this->by('css selector', $value);
    }

    /**
     * @param   string  $value  e.g. 'uniqueId'
     *
     * @return Element
     */
    public function byId($value)
    {
        return $this->by('id', $value);
    }

    /**
     * @param   string  $value  e.g. 'Link text'
     *
     * @return Element
     */
    public function byLinkText($value)
    {
        return $this->by('link text', $value);
    }

    /**
     * @param   string  $value  e.g. 'Link te'
     *
     * @return Element
     */
    public function byPartialLinkText($value)
    {
        return $this->by('partial link text', $value);
    }

    /**
     * @param   string  $value  e.g. 'email_address'
     *
     * @return Element
     */
    public function byName($value)
    {
        return $this->by('name', $value);
    }

    /**
     * @param   string  $value  e.g. 'body'
     *
     * @return Element
     */
    public function byTag($value)
    {
        return $this->by('tag name', $value);
    }

    /**
     * @param   string  $value  e.g. '/div[@attribute="value"]'
     *
     * @return Element
     */
    public function byXPath($value)
    {
        return $this->by('xpath', $value);
    }
}
