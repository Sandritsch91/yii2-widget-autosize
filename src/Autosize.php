<?php

namespace sandritsch91\yii2\autosize;

use yii\helpers\Html;
use yii\widgets\InputWidget;

class Autosize extends InputWidget
{
    /**
     * The class used to generate the form field.
     * @var string|Html
     */
    public string|Html $htmlClass = 'yii\helpers\Html';

    /**
     * @var array the event handlers for the underlying JS plugin.
     */
    public array $clientEvents = [];

    /**
     * @return string
     */
    public function run(): string
    {
        if ($this->hasModel()) {
            $id = $this->options['id'] ?? $this->htmlClass::getInputId($this->model, $this->attribute);
            $html = $this->htmlClass::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            $id = $this->options['id'] ?? $this->getId();
            $html = $this->htmlClass::textarea($this->name, $this->value, $this->options);
        }

        $this->registerClientScript($id);

        return $html;
    }

    /**
     * Registers the needed JavaScript.
     */
    protected function registerClientScript(string $id): void
    {
        $view = $this->getView();
        AssetBundle::register($view);

        $js = "autosize(document.getElementById('$id'));";
        $view->registerJs($js);

        // Register client events
        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "document.getElementById('$id').addEventListener('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }
    }
}
