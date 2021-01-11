# Appium PHP Client

本类库基于官方的 `"appium/appium-php"` 进行二次修改。 如需文档请自行百度appium用法，本类库只针对`Android` 设备,因为精力有限，只测试安卓设备

一、使用方法
(1)安装本类库
`composer require yangfan/appium-php-client dev-master`
或者也可以复制下面的json 然后用composer 安装

```json
{
    "name": "username/my-php-project",
    "require": {
        "yangfan/appium-php-client": "dev-master"
    }
}
```
(2)运行方法

`vendor/phpunit/phpunit/phpunit --debug --group realRun  <mytest.php>`

如果想知道上述命令的作用请自行查找各个工具的文档（可以看到，我下面的样例都是名称为 realRun 的组）。上述命令的意思是`以debug模式，只运行名称为realRun的组 的方法`，（看方法上面的注释）


mytest.php 的样例在下面，
## Usage and changes



```php
require_once('./vendor/autoload.php');

use FanYang\Appium\AppiumTestCase;

class test extends AppiumTestCase
{
    public static $browsers = [
        [
            'local'       => true,
            'port'        => 4723,
            'browserName' => '',

            'desiredCapabilities' => [
                'platformName'      => 'Android',
                'platformVersion'   => '7.1.2',
                'deviceName'        => 'Nexus 6',
                'unicodeKeyboard'   => true,
                'resetKeyboard'     => true,
                'noSign'            => true,
                'newCommandTimeout' => '1800',
            ],
        ],
    ];

    /**
     * 打开抖音
     *
     * @group realRun
     */
    public function testStartActivityDouYin()
    {
        $options = [
            'appPackage'  => 'com.ss.android.ugc.aweme',
            'appActivity' => '.splash.SplashActivity',
        ];
        $this->startActivity($options);
        $activity = $this->currentActivity();
        $this->assertTrue(strpos($activity, '.splash.SplashActivity') !== false);
    }

    /**
     * 推送文件
     *
     * @group realRun
     */
    public function testPushFile()
    {
        $content = file_get_contents('D:\工作沉淀\02测试相关\测试的素材\图片\29c3fce48a982c0eb0d644c999b60d21.jpeg');
        $data    = base64_encode($content);
        $a       = random_int(1, 9999);
        $path    = '/storage/emulated/0/Pictures/'.$a.'.jpg';
        $this->pushFile($path, $data);
        $data_ret = base64_decode($this->pullFile($path));
        $this->assertEquals($content, $data_ret);
    }

    /**
     * @group realRun
     * 点击同意个人信息保护指引
     */
    public function testClickPersonInfoProtect()
    {
        try {
            $this->byId('com.ss.android.ugc.aweme:id/b0d')->click();
        } catch (PHPUnit\Extensions\Selenium2TestCase\WebDriverException $e) {
            if (strpos($e->getMessage(),
                    'An element could not be located on the page using the given search parameters') === false) {
                throw $e;
            }
            echo '没有个人信息保护指引';
            flush();
            ob_flush();
        }
        $this->assertEquals('1', '1');
    }

    /**
     * @group realRun
     * 检测到更新  点击"以后再说"
     */
    public function testCheckUpdate()
    {
        try {
            $this->byId('com.ss.android.ugc.aweme:id/dpu')->click();
        } catch (PHPUnit\Extensions\Selenium2TestCase\WebDriverException $e) {
            if (strpos($e->getMessage(),
                    'An element could not be located on the page using the given search parameters') === false) {
                throw $e;
            }
            echo '没有检测到更新';
            flush();
            ob_flush();
        }
        $this->assertEquals('1', '1');
    }

    /**
     * @group realRun
     * 点击底部的+号
     */
    public function testClickBottomPlus()
    {
        $x      = 443;
        $y      = 1522;
        $action = $this->initiateTouchAction();
        $action->press(['x' => $x, 'y' => $y])->release()->perform();

        $this->assertEquals('1', '1');
    }

    /**
     * @group realRun
     *   点击"相册"
     */
    public function testClickPhotos()
    {
        $this->byId('com.ss.android.ugc.aweme:id/dk5')->click();
        $this->assertEquals('1', '1');
    }

    //    /**
    //     * @group realRun
    //     *   点击"视频"
    //     */
    //    public function testVideo()
    //    {
    //        $action = $this->initiateTouchAction();
    //        $action->press(['x' => 443, 'y' => 194])->release()->perform();
    //        $this->assertEquals('1', '1');
    //    }

    /**
     * @group realRun
     *   点击"图片"
     */
    public function testPicture()
    {
        $this->byAndroidUIAutomator('new UiSelector().text("图片")')->click();

        $this->assertEquals('1', '1');
    }

    /**
     * @group realRun
     *   点击"真实的图片选择"
     */
    public function testRealPicture()
    {
        $this->byId('com.ss.android.ugc.aweme:id/ctm')->click();

        $this->assertEquals('1', '1');
    }

    /**
     * @group realRun
     *   选择完图片后点击下一步
     */
    public function testClickNext()
    {
        $this->byId('com.ss.android.ugc.aweme:id/hic')->click();

        $this->assertEquals('1', '1');
    }

    /**
     * @group realRun
     *   音乐界面的下一步
     */
    public function testClickMusicNext()
    {
        $this->byAndroidUIAutomator('new UiSelector().text("下一步")')->click();

        $this->assertEquals('1', '1');
    }
    /**
     * @group realRun
     *   输入文字
     */
    public function testInput()
    {
        $el = $this->byId('com.ss.android.ugc.aweme:id/bqv');
        $el->setText('测试发布');
        $this->assertEquals('测试发布', $el->text());
    }
    /**
     * @group realRun
     *   选择完图片后点击下一步
     */
    public function testClickRelease()
    {
        $this->byId('com.ss.android.ugc.aweme:id/g1k')->click();

        $this->assertEquals('1', '1');
    }


}
```


## Methods added

### Methods in `PHPUnit_Extensions_AppiumTestCase`

* `byIOSUIAutomation`
* `byAndroidUIAutomator`
* `byAccessibilityId`
* `keyEvent`
* `pullFile`
* `pushFile`
* `backgroundApp`
* `isAppInstalled`
* `installApp`
* `removeApp`
* `launchApp`
* `closeApp`
* `endTestCoverage`
* `lock`
* `shake`
* `getDeviceTime`
* `hideKeyboard`
* `initiateTouchAction`
* `initiateMultiAction`
* `scroll`
* `dragAndDrop`
* `swipe`
* `tap`
* `pinch`
* `zoom`
* `startActivity`
* `getSettings`
* `setSettings`

### Methods in `PHPUnit_Extensions_AppiumTestCase_Element`

* `byIOSUIAutomation`
* `byAndroidUIAutomator`
* `byAccessibilityId`
* `setImmediateValue`

### Methods for Touch Actions and Multi Gesture Touch Actions

Appium 1.0 allows for much more complex ways of interacting with your app through Touch Actions and Multi Gesture Touch Actions. The `PHPUnit_Extensions_AppiumTestCase_TouchAction` class allows for the following events:

* `tap`
* `press`
* `longPress`
* `moveTo`
* `wait`
* `release`

All of these except `tap` and `release` can be chained together to create arbitrarily complex actions. Instances of the `PHPUnit_Extensions_AppiumTestCase_TouchAction` class are obtained through the Test Class's `initiateTouchAction` method, and dispatched through the `perform` method.

The Multi Gesture Touch Action API allows for adding an arbitrary number of Touch Actions to be run in parallel on the device. Individual actions created as above are added to the multi action object (an instance of `PHPUnit_Extensions_AppiumTestCase_MultiAction` obtained from the Test Class's `initiateMultiAction` method) through the `add` method, and the whole thing is dispatched using `perform`.
