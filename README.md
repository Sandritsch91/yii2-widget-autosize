# yii2-widget-autosize

An [autosize](https://www.jacklmoore.com/autosize/) widget for [yii2](https://www.yiiframework.com/)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist sandritsch91/yii2-widget-autosize
```

or add

```json
"sandritsch91/yii2-widget-autosize": "*"
```

to the require section of your composer.json

## Usage

with a model:

```php
use sandritsch91\yii2-widget-autosize\Autosize;

echo Autosize::widget([
    'model' => $model,                          // The model to be used in the form
    'attribute' => 'content',                   // The attribute to be used in the form
    'htmlClass' => yii\bootstrap5\Html::class,  // Optional. The class used to generate the form field
    'clientEvents' => [                         // Optional. Pass the client events to be attached to the textarea
        'autosize:resized' => 'function() { console.log("resized"); }'
    ]
]);
```

with an ActiveForm:

```php
use sandritsch91\yii2-widget-autosize\Autosize;

echo $form->field($model, 'content')->widget(Autosize::class, [
    'clientEvents' => [
        'autosize:resized' => 'function() { console.log("resized"); }'
    ]
]);
```

without a model:

```php
use sandritsch91\yii2-widget-autosize\Autosize;

echo Autosize::widget([
    'name' => 'myText',         // The name of the input
    'value' => 'Hello World',   // The value of the input
    'clientEvents' => [
        'autosize:resized' => 'function() { console.log("resized"); }'
    ]
]);
```

## Widget options
- clientEvents: The client events to be attached to the textarea. Defaults to []
  - autosize:resized: Triggered when the textarea is resized
  - autosize:update: Dispatch this event to update the textarea. No event is triggered by the plugin
  - autosize:destroy: Dispatch this event to destroy the textarea. No event is triggered by the plugin
- htmlClass: The class used to generate the form field. Defaults to yii\helpers\Html::class
