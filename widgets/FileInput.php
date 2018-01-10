<?php

namespace pine\yii\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\widgets\ActiveField;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use common\models\File;
use pine\yii\widgets\FileInputAsset;

/**
 * File Input Widget
 */
class FileInput extends InputWidget
{
    public $form;

    public $options = ['class' => 'form-group'];

    public $template = "{label}\n{input}\n{hint}\n{error}\n{preview}";

    public $previewTemplate = "{image}\n{delete}";

    public $inputOptions = ['class' => 'form-control'];

    public $errorOptions = ['class' => 'help-block'];

    public $labelOptions = ['class' => 'control-label'];

    public $hintOptions = ['class' => 'hint-block'];

    public $previewOptions = ['class' => 'preview-block'];

    public $selectors = [];

    public $parts = [];

    public function init()
    {
        if ($this->form === null || !$this->form) {
            throw new InvalidConfigException("'form' property must be specified.");
        }
        parent::init();
    }

    public function run()
    {
        if ($this->hasModel()) {
            echo $this->renderActiveFileInput();
        } else {
            echo Html::fileInput($this->name, $this->value, $this->options);
        }

        $this->registerClientScript();
    }

    public function renderActiveFileInput($content = null)
    {
        if ($content === null) {

            $rules = $this->model->rules();
            if (count($rules) > 0) {
                foreach ($rules as $rule) {
                    if ('file' == $rule[1]) {
                        $attributes = $rule[0];
                        $options = array_slice($rule, 2);
                        if (isset($options['maxFiles']) && $options['maxFiles'] > 1 && in_array($this->attribute, $attributes)) {
                            $this->inputOptions['multiple'] = true;
                        }
                    }
                }
            }

            if (!isset($this->parts['{input}'])) {
                if (isset($this->inputOptions['multiple']) && $this->inputOptions['multiple'] === true) {
                    $attribute = $this->attribute.'[]';
                } else {
                    $attribute = $this->attribute;
                }
                $this->parts['{input}'] = Html::activeFileInput($this->model, $attribute, $this->inputOptions);
            }
            if (!isset($this->parts['{label}'])) {
                $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $this->labelOptions);
            }
            if (!isset($this->parts['{error}'])) {
                $this->parts['{error}'] = Html::error($this->model, $this->attribute, $this->errorOptions);
            }
            if (!isset($this->parts['{hint}'])) {
                $this->parts['{hint}'] = '';
            }
            if (!isset($this->parts['{preview}'])) {
                $this->parts['{preview}'] = $this->renderPreviewActiveFileInput();
            }
            $content = strtr($this->template, $this->parts);
        } elseif (!is_string($content)) {
            $content = call_user_func($content, $this);
        }

        return $this->renderBeginActiveFileInput() . "\n" . $content . "\n" . $this->renderEndActiveFileInput();
    }

    public function renderPreviewActiveFileInput()
    {
        $content = '';

        $inputID = Html::getInputId($this->model, $this->attribute);
        $attribute = Html::getAttributeName($this->attribute);

        if (!isset($this->previewOptions['id'])) {
            $this->previewOptions['id'] = 'preview-block-' . $inputID;
        }

        $content .= Html::beginTag('ul', $this->previewOptions);

        if (isset($this->inputOptions['multiple']) && $this->inputOptions['multiple'] === true) {
            $files = File::find()->where([
                'deleted' => 0,
                'model' => get_class($this->model),
                'property' => $this->attribute,
                'pk' => $this->model->primaryKey,
            ])->all();

            if (count($files) > 0) {
                foreach($files as $file) {
                    $content .= $this->renderFileActiveFileInput($file);
                }
            }
        } else {
            $file = File::find()->where([
                'deleted' => 0,
                'model' => get_class($this->model),
                'property' => $this->attribute,
                'pk' => $this->model->primaryKey,
            ])->one();

            if (NULL !== $file) {
                $content .= $this->renderFileActiveFileInput($file);
            }
        }


        $content .= Html::endTag('div');

        return $content;
    }

    public function renderFileActiveFileInput($file)
    {
        $content = '';

        $inputID = Html::getInputId($this->model, $this->attribute);

        $liOptions = [
            'id' => 'preview-block-' . $inputID . '-file-'.$file->id,
            'class' => 'file-view-file ' . str_replace('/', '-', $file->kind) . ' ' . $file->extension,
        ];
        $content .= Html::beginTag('li', $liOptions);

        $linkOptions = [
            'class' => 'preview-block-file-link',
        ];
        if (!in_array($file->kind, ['image/jpeg','image/png'])) {
            $content .= Html::a($file->name . ' (' . $file->sizeInfo . ')', ['/file/file/view', 'id'=>$file->id], $linkOptions);
        } else {
            $imageOptions = [
                'class' => 'preview-block-file-image ' . str_replace('/', '-', $file->kind) . ' ' . $file->extension,
                'width' => '100',
                'height' => '100',
                'alt' => $this->model->getAttributeLabel($this->attribute),
            ];

            if (isset($this->previewOptions['imageWidth']) && $this->previewOptions['imageWidth'] != '') {
                $imageOptions['width'] = intval($this->previewOptions['imageWidth']);
            }

            if (isset($this->previewOptions['imageHeight']) && $this->previewOptions['imageHeight'] != '') {
                $imageOptions['height'] = intval($this->previewOptions['imageHeight']);
            }

            if (isset($this->previewOptions['imageAlt']) && $this->previewOptions['imageAlt'] != '') {
                $imageOptions['alt'] = $this->previewOptions['imageAlt'];
            }

            $content .= Html::a(Html::img($file->path, $imageOptions), ['/file/file/view', 'id'=>$file->id], $linkOptions);
        }

        $buttonLabel = '<span class="glyphicon glyphicon-trash"></span>';

        if (isset($this->previewOptions['buttonLabel']) && $this->previewOptions['buttonLabel'] != '') {
            $buttonLabel = $this->previewOptions['buttonLabel'];
        }

        $buttonOptions = [
            'id' => 'preview-block-' . $inputID . '-button-' . $file->id,
            'class' => 'btn btn-xs btn-danger preview-block-' . $inputID . '-button',
            'value' => Url::toRoute(['/file/file/delete', 'id' => $file->id]),
            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
            'data-file-id' => $file->id,
        ];

        if (isset($this->previewOptions['buttonClass']) && $this->previewOptions['buttonClass'] != '') {
            $buttonOptions['class'] = $this->previewOptions['buttonClass'];
        }

        if (isset($this->previewOptions['buttonConfirm']) && $this->previewOptions['buttonConfirm'] != '') {
            $buttonOptions['confirm'] = $this->previewOptions['buttonConfirm'];
        }

        $content .= Html::button($buttonLabel, $buttonOptions);

        $content .= Html::endTag('li');

        return $content;
    }

    public function renderBeginActiveFileInput()
    {
        $inputID = Html::getInputId($this->model, $this->attribute);
        $attribute = Html::getAttributeName($this->attribute);
        $options = $this->options;
        $class = isset($options['class']) ? [$options['class']] : [];
        $class[] = "field-$inputID";
        if ($this->model->isAttributeRequired($attribute)) {
            $class[] = $this->form->requiredCssClass;
        }
        if ($this->model->hasErrors($attribute)) {
            $class[] = $this->form->errorCssClass;
        }
        $options['class'] = implode(' ', $class);
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::beginTag($tag, $options);
    }

    public function renderEndActiveFileInput()
    {
        return Html::endTag(isset($this->options['tag']) ? $this->options['tag'] : 'div');
    }

    public function registerClientScript()
    {

        $inputID = Html::getInputId($this->model, $this->attribute);

        $js = <<< JS

            $('.preview-block-$inputID-button').on('click', function(event) {
                if (confirm($(this).attr('confirm'))) {
                    var fileId = $(this).attr('data-file-id');
                    $.post($(this).attr('value')).done(function(result) {
                        console.log('#preview-block-$inputID-file-'+fileId);
                        $('#preview-block-$inputID-file-'+fileId).remove().fadeOut(500);
                        $('#preview-block-$inputID-button-'+fileId).remove().fadeOut(500);
                    });
                }
                return false;
            });

JS;

        $view = $this->getView();

        FileInputAsset::register($view);

        $view->registerJs($js);
    }
}
