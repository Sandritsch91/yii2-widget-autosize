<?php

namespace sandritsch91\yii2\autosize\tests;

use PHPUnit\Framework\TestCase;
use sandritsch91\yii2\autosize\AssetBundle;
use sandritsch91\yii2\autosize\Autosize;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\Application;
use yii\web\View;

class AutosizeTest extends TestCase
{
    private string $assetPath;

    protected function setUp(): void
    {
        $root = dirname(__DIR__);
        $this->assetPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('autosize-test-', true);
        FileHelper::createDirectory($this->assetPath);

        new Application([
            'id' => 'autosize-test',
            'basePath' => $root,
            'vendorPath' => $root . '/vendor',
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'test',
                    'scriptFile' => $root . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
                'assetManager' => [
                    'basePath' => $this->assetPath,
                    'baseUrl' => '/assets',
                ],
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Yii::$app = null;
        FileHelper::removeDirectory($this->assetPath);
    }

    public function testRendersTextareaForModel(): void
    {
        $model = new class extends Model {
            public $content = 'Hello';

            public function formName()
            {
                return 'Post';
            }
        };

        [$html, $js] = $this->render(['model' => $model, 'attribute' => 'content']);

        $this->assertSame('<textarea id="post-content" name="Post[content]">Hello</textarea>', $html);
        $this->assertSame('autosize(document.getElementById("post-content"));', $js);
    }

    public function testRendersTextareaWithoutModel(): void
    {
        [$html, $js] = $this->render([
            'name' => 'text',
            'value' => 'Hello',
            'options' => ['id' => 'my-text'],
        ]);

        $this->assertSame('<textarea id="my-text" name="text">Hello</textarea>', $html);
        $this->assertSame('autosize(document.getElementById("my-text"));', $js);
    }

    public function testListenersAreAttachedBeforeAutosizeRuns(): void
    {
        [, $js] = $this->render([
            'name' => 'text',
            'options' => ['id' => 'my-text'],
            'clientEvents' => ['autosize:resized' => 'function() {}'],
        ]);

        $this->assertSame(
            'document.getElementById("my-text").addEventListener("autosize:resized", function() {});' . "\n"
            . 'autosize(document.getElementById("my-text"));',
            $js
        );
    }

    public function testIdIsEncoded(): void
    {
        [, $js] = $this->render(['name' => 'text', 'options' => ['id' => "x');alert(1);//"]]);

        $this->assertStringNotContainsString("'", $js);
        $this->assertStringContainsString(self::encodedQuote(), $js);
    }

    public function testEventNameIsEncoded(): void
    {
        [, $js] = $this->render([
            'name' => 'text',
            'clientEvents' => ["it's" => 'function() {}'],
        ]);

        $this->assertStringNotContainsString("'", $js);
        $this->assertStringContainsString('"it' . self::encodedQuote() . 's"', $js);
    }

    public function testAssetBundleIsRegisteredAndResolvesToAutosizeScript(): void
    {
        $view = new View();
        Autosize::widget(['name' => 'text', 'view' => $view]);

        $this->assertArrayHasKey(AssetBundle::class, $view->assetBundles);

        $bundle = new AssetBundle();
        $this->assertFileExists(Yii::getAlias($bundle->sourcePath) . '/' . $bundle->js[0]);
    }

    /**
     * @return string a single quote as encoded by Json::htmlEncode()
     */
    private static function encodedQuote(): string
    {
        return chr(92) . 'u0027';
    }

    /**
     * @return array{0: string, 1: string} the widget HTML and its registered ready script
     */
    private function render(array $config): array
    {
        $view = new View();
        $html = Autosize::widget($config + ['view' => $view]);

        return [$html, implode("\n", $view->js[View::POS_READY])];
    }
}
