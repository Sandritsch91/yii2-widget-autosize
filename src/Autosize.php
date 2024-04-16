<?php

namespace sandritsch91\yii2\autosize;

use yii\helpers\Html;
use yii\widgets\InputWidget;

class Autosize extends InputWidget
{
    /**
     * The Html class to use.
     * @var string|Html
     */
    public string|Html $htmlClass = 'yii\helpers\Html';
    /**
     * @var array the options for the underlying JS plugin.
     */
    public array $clientOptions = [];
    /**
     * @var array the event handlers for the underlying JS plugin.
     */
    public array $clientEvents = [];

    /**
     * @return string
     */
    public function run(): string
    {
        $this->registerClientScript();

        return ($this->hasModel())
            ? $this->htmlClass::activeInput('text', $this->model, $this->attribute, $this->options)
            : $this->htmlClass::input('text', $this->name, $this->value, $this->options);
    }

    /**
     * Registers the needed JavaScript.
     */
    protected function registerClientScript(): void
    {
        $view = $this->getView();
        AssetBundle::register($view);

        $js = "autosize(document.getElementById('#$this->id', $this->clientOptions));";
        $view->registerJs($js);

        // Register client events
        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "document.getElementById('#$this->id').addEventListener('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }
    }
}
