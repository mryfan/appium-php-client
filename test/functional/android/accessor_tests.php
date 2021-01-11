<?php

use FanYang\Appium\AppiumTestCase;
// require_once "vendor/autoload.php";
define("APP_PATH", realpath(dirname(__FILE__).'/../../apps/ApiDemos-debug.apk'));
if (!APP_PATH) {
    die("App did not exist!");
}
require_once('PHPUnit/Extensions/AppiumTestCase.php');

class ContextTests extends AppiumTestCase
{
    public function testFindByAndroidUIAutomator()
    {
        $el = $this->byAndroidUIAutomator('new UiSelector().description("Animation")');
        $this->assertNotNull($el);
        $this->assertEquals('FangYang\Appium\AppiumTestCase\AppiumTestCaseElement', get_class($el));
    }

    public function testElementFindByAndroidUIAutomator()
    {
        sleep(1);
        $el = $this->byClassName('android.widget.ListView');
        $sub_el = $el->byAndroidUIAutomator('new UiSelector().description("Animation")');
        $this->assertEquals('Animation', $sub_el->text());
        $this->assertEquals('FangYang\Appium\AppiumTestCase\AppiumTestCaseElement', get_class($sub_el));
    }

    public function testFindByAccessibilityId()
    {
        $el = $this->byAccessibilityId('Animation');
        $this->assertNotNull($el);
        $this->assertEquals('FangYang\Appium\AppiumTestCase\AppiumTestCaseElement', get_class($el));
    }

    public function testElementFindByAccessibilityId()
    {
        $el = $this->byClassName('android.widget.ListView');
        $sub_el = $el->byAccessibilityId('Animation');
        $this->assertEquals('Animation', $sub_el->text());
        $this->assertEquals('FangYang\Appium\AppiumTestCase\AppiumTestCaseElement', get_class($sub_el));
    }

    public static $browsers = array(
        array(
            'local' => true,
            'port' => 4723,
            'browserName' => '',
            'desiredCapabilities' => array(
                'app' => APP_PATH,
                'platformName' => 'Android',
                'platformVersion' => '4.4',
                'deviceName' => 'Android Emulator'
            )
        )
    );
}
