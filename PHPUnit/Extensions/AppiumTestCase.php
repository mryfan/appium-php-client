<?php

namespace FanYang\Appium;

use FanYang\Appium\AppiumTestCase\AppiumTestCaseElement;
use FanYang\Appium\AppiumTestCase\SessionStrategy\Shared;
use FanYang\Appium\AppiumTestCase\MultiAction;
use FanYang\Appium\AppiumTestCase\TouchAction;
use FanYang\Appium\AppiumTestCase\SessionStrategy\Isolated;
use InvalidArgumentException;
use PHPUnit\Extensions\Selenium2TestCase;
use PHPUnit\Extensions\Selenium2TestCase\Element;
use PHPUnit\Extensions\Selenium2TestCase\ElementCriteria;


abstract class AppiumTestCase extends Selenium2TestCase
{
    /**
     * @var array
     */
    private static $lastBrowserParams;
    protected $session;
    /**
     * @var array
     */
    private $parameters;

    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // Make sure we are using the Appium session
        self::setUpSessionStrategy(["sessionStrategy" => "isolated"]);

        // Appium doesn't use the browser per se, but the system fails
        // if it is not set
        self::setBrowser("");
        self::setBrowserUrl("");
    }

    protected function setUpSessionStrategy($params)
    {
        // This logic enables us to have a session strategy reused for each
        // item in self::$browsers. We don't want them both to share one
        // and we don't want each test for a specific browser to have a
        // new strategy
        if ($params == self::$lastBrowserParams) {
            // do nothing so we use the same session strategy for this
            // browser
        } elseif (isset($params['sessionStrategy'])) {
            $strat = $params['sessionStrategy'];
            if ($strat != "isolated" && $strat != "shared") {
                throw new InvalidArgumentException("Session strategy must be either 'isolated' or 'shared'");
            } elseif ($strat == "isolated") {
                self::$browserSessionStrategy = new Isolated;
            } else {
                self::$browserSessionStrategy = new Shared(self::defaultSessionStrategy());
            }
        } else {
            self::$browserSessionStrategy = self::defaultSessionStrategy();
        }
        self::$lastBrowserParams    = $params;
        $this->localSessionStrategy = self::$browserSessionStrategy;
    }

    private static function defaultSessionStrategy()
    {
        return new Isolated;
    }


    // We want to inject an Appium session into the PHPUnit-Selenium logic.

    /**
     * @param   boolean
     */
    public static function shareSession($shareSession)
    {
        if (!is_bool($shareSession)) {
            throw new InvalidArgumentException("The shared session support can only be switched on or off.");
        }
        if (!$shareSession) {
            self::$sessionStrategy = self::defaultSessionStrategy();
        } else {
            self::$sessionStrategy = new Shared(self::defaultSessionStrategy());
        }
    }

    /**
     * @param   string  $value  e.g. '.elements()[0]'
     *
     * @return Element
     */
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
        $session = $this->prepareSession();
        $value   = $session->postCommand('element', $criteria);

        return AppiumTestCaseElement::fromResponseValue($value, $session->getSessionUrl()->descend('element'),
            $session->getDriver());
    }

    /**
     * @param   string  $value  e.g. 'new UiSelector().description("Animation")'
     *
     * @return Element
     */
    public function byAndroidUIAutomator($value)
    {
        return $this->by('-android uiautomator', $value);
    }

    /**
     * @param   string  $value  e.g. 'Animation'
     *
     * @return Element
     */
    public function byAccessibilityId($value)
    {
        return $this->by('accessibility id', $value);
    }

    public function pullFile($path)
    {
        $session  = $this->prepareSession();
        $data     = [
            'path' => $path,
        ];
        $url      = $this->getSessionUrl()->descend('appium')->descend('device')->descend('pull_file');
        $response = $session->getDriver()->curl('POST', $url, $data);

        return $response->getValue();
    }

    public function pushFile($path, $base64Data)
    {
        $session = $this->prepareSession();
        $data    = [
            'path' => $path,
            'data' => $base64Data,
        ];
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('push_file');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function pullFolder($path)
    {
        $session  = $this->prepareSession();
        $data     = [
            'path' => $path,
        ];
        $url      = $this->getSessionUrl()->descend('appium')->descend('device')->descend('pull_folder');
        $response = $session->getDriver()->curl('POST', $url, $data);

        return $response->getValue();
    }

    public function backgroundApp($seconds)
    {
        $session = $this->prepareSession();
        $data    = [
            'seconds' => $seconds,
        ];
        $url     = $this->getSessionUrl()->descend('appium')->descend('app')->descend('background');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function isAppInstalled($bundleId)
    {
        // /appium/device/app_installed
        $session  = $this->prepareSession();
        $data     = [
            'bundleId' => $bundleId,
        ];
        $url      = $this->getSessionUrl()->descend('appium')->descend('device')->descend('app_installed');
        $response = $session->getDriver()->curl('POST', $url, $data);

        return $response->getValue();
    }

    public function installApp($path)
    {
        $session = $this->prepareSession();
        $data    = [
            'appPath' => $path,
        ];
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('install_app');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function removeApp($appId)
    {
        // /appium/device/remove_app
        $session = $this->prepareSession();
        $data    = [
            'appId' => $appId,
        ];
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('remove_app');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function launchApp()
    {
        // /appium/app/launch
        $session = $this->prepareSession();
        $url     = $this->getSessionUrl()->descend('appium')->descend('app')->descend('launch');
        $session->getDriver()->curl('POST', $url, NULL);
    }

    public function closeApp()
    {
        $session = $this->prepareSession();
        $url     = $this->getSessionUrl()->descend('appium')->descend('app')->descend('close');
        $session->getDriver()->curl('POST', $url, NULL);
    }

    /**
     * @param   array  $options  'appPackage' and 'appActivity' are required;
     *                           'appWaitPackage' and 'appWaitActivity' are optional
     *
     * @return void
     */
    public function startActivity($options)
    {
        $session = $this->prepareSession();
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('start_activity');
        $session->getDriver()->curl('POST', $url, $options);
    }

    public function endTestCoverage($intent, $path)
    {
        $session  = $this->prepareSession();
        $data     = [
            'intent' => $intent,
            'path'   => $path,
        ];
        $url      = $this->getSessionUrl()->descend('appium')->descend('app')->descend('end_test_coverage');
        $response = $session->getDriver()->curl('POST', $url, $data);

        return $response->getValue();
    }

    public function lock($seconds)
    {
        $session = $this->prepareSession();
        $data    = [
            'seconds' => $seconds,
        ];
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('lock');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function shake()
    {
        $session = $this->prepareSession();
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('shake');
        $session->getDriver()->curl('POST', $url, NULL);
    }

    public function touchId($match)
    {
        $session = $this->prepareSession();
        $data    = [
            'match' => $match,
        ];
        $url     = $this->getSessionUrl()->descend('appium')->descend('simulator')->descend('touch_id');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function toggleTouchIdEnrollment()
    {
        $session = $this->prepareSession();
        $url     = $this->getSessionUrl()->descend('appium')->descend('simulator')->descend('toggle_touch_id_enrollment');
        $session->getDriver()->curl('POST', $url);
    }

    public function getDeviceTime()
    {
        $session  = $this->prepareSession();
        $url      = $this->getSessionUrl()->descend('appium')->descend('device')->descend('system_time');
        $response = $session->getDriver()->curl('GET', $url);

        return $response->getValue();
    }

    public function hideKeyboard($args = ['strategy' => 'tapOutside'])
    {
        $data = [];
        if (array_key_exists('keyName', $args)) {
            $data['keyName'] = $args['keyName'];
        } elseif (array_key_exists('key', $args)) {
            $data['key'] = $args['key'];
        }
        if (array_key_exists('strategy', $args)) {
            $data['strategy'] = $args['strategy'];
        }
        $session = $this->prepareSession();
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('hide_keyboard');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function openNotifications()
    {
        $session = $this->prepareSession();
        $url     = $this->getSessionUrl()->descend('appium')->descend('device')->descend('open_notifications');
        $session->getDriver()->curl('POST', $url, []);
    }

    public function scroll($originElement, $destinationElement)
    {
        $action = $this->initiateTouchAction();
        $action->press(['element' => $originElement])->moveTo(['element' => $destinationElement])->release()->perform();

        return $this;
    }

    public function initiateTouchAction()
    {
        $session = $this->prepareSession();

        return new TouchAction($session->getSessionUrl(), $session->getDriver());
    }

    public function dragAndDrop($originElement, $destinationElement)
    {
        $action = $this->initiateTouchAction();
        $action->longPress(['element' => $originElement])->moveTo(['element' => $destinationElement])->release()->perform();

        return $this;
    }

    public function swipe($startX, $startY, $endX, $endY, $duration = 800)
    {
        $action = $this->initiateTouchAction();
        $action->press(['x' => $startX, 'y' => $startY])->wait($duration)->moveTo([
                'x' => $endX,
                'y' => $endY,
            ])->release()->perform();

        return $this;
    }

    public function tap($fingers, $x, $y = NULL, $duration = 0)
    {
        // php doesn't support overloading, so we need to do some twiddling
        if (gettype($x) != 'integer') {
            $multiAction = $this->initiateMultiAction();
            $element     = $x;
            if (!is_null($y)) {
                echo "setting duration to";
                $duration = $y;
            }

            for ($i = 0; $i < $fingers; $i++) {
                $action = $this->initiateTouchAction();
                $action->press(['element' => $element])->wait($duration)->release();
                $multiAction->add($action);
            }
            $multiAction->perform();
        } else {
            for ($i = 0; $i < $fingers; $i++) {
                $action = $this->initiateTouchAction();
                $action->press(['x' => $x, 'y' => $y])->wait($duration)->release();
                $action->perform();
            }
        }

    }

    public function initiateMultiAction()
    {
        $session = $this->prepareSession();

        return new MultiAction($session->getSessionUrl(), $session->getDriver());
    }

    // Get session Settings

    public function pinch(AppiumTestCaseElement $element)
    {
        $center = $this->elementCenter($element);

        $centerX = $center['x'];
        $centerY = $center['y'];

        $a1 = $this->initiateTouchAction();
        $a1->press(['x' => $centerX, 'y' => $centerY - 100])->moveTo(['x' => $centerX, 'y' => $centerY])->release();

        $a2 = $this->initiateTouchAction();
        $a2->press(['x' => $centerX, 'y' => $centerY + 100])->moveTo(['x' => $centerX, 'y' => $centerY])->release();

        $ma = $this->initiateMultiAction();
        $ma->add($a1);
        $ma->add($a2);
        $ma->perform();
    }

    // Set session Settings

    protected function elementCenter(AppiumTestCaseElement $element)
    {
        $size     = $element->size();
        $location = $element->location();

        $centerX = $location['x'] + $size['width'] / 2;
        $centerY = $location['y'] + $size['height'] / 2;

        return ['x' => $centerX, 'y' => $centerY];
    }

    // stolen from PHPUnit_Extensions_Selenium2TestCase_Element_Accessor
    // where it is mysteriously private, and therefore unusable

    public function zoom(AppiumTestCaseElement $element)
    {
        $center = $this->elementCenter($element);

        $centerX = $center['x'];
        $centerY = $center['y'];

        $a1 = $this->initiateTouchAction();
        $a1->press(['x' => $centerX, 'y' => $centerY])->moveTo(['x' => $centerX, 'y' => $centerY - 100])->release();

        $a2 = $this->initiateTouchAction();
        $a2->press(['x' => $centerX, 'y' => $centerY])->moveTo(['x' => $centerX, 'y' => $centerY + 100])->release();

        $ma = $this->initiateMultiAction();
        $ma->add($a1);
        $ma->add($a2);
        $ma->perform();
    }

    public function getSettings()
    {
        // /appium/settings
        $session  = $this->prepareSession();
        $url      = $this->getSessionUrl()->descend('appium')->descend('settings');
        $response = $session->getDriver()->curl('GET', $url);

        return $response->getValue();
    }

    public function updateSettings($settings)
    {
        // /appium/settings
        $session = $this->prepareSession();
        $data    = [
            'settings' => $settings,
        ];
        $url     = $this->getSessionUrl()->descend('appium')->descend('settings');
        $session->getDriver()->curl('POST', $url, $data);
    }

    public function elements(ElementCriteria $criteria)
    {
        $session  = $this->prepareSession();
        $values   = $session->postCommand('elements', $criteria);
        $elements = [];
        foreach ($values as $value) {
            $elements[] = AppiumTestCaseElement::fromResponseValue($value,
                $session->getSessionUrl()->descend('element'), $session->getDriver());
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
