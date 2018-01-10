<?php

namespace pine\yii\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\base\InvalidConfigException;
use common\models\File;

/**
 * File View Widget
 */
class FileView extends InputWidget
{
    public $options = ['class' => 'file-view'];

    public $template = "{label}\n{input}\n{hint}\n{error}";


    /**
     * Initializes the widget.
     *
     * @throws InvalidConfigException if the "mask" property is not set.
     */
    public function init()
    {
        if ($this->model === null || !$this->model) {
            throw new InvalidConfigException("'model' property must be specified.");
        }
        if ($this->attribute === null || !$this->attribute) {
            throw new InvalidConfigException("'attribute' property must be specified.");
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
         echo $this->renderFileView();
         $this->registerClientScript();
    }

    public function renderFileView()
    {
        $content = '';

        $multiple = false;

        $rules = $this->model->rules();
        if (count($rules) > 0) {
            foreach ($rules as $rule) {
                if ('file' == $rule[1]) {
                    $attributes = $rule[0];
                    $options = array_slice($rule, 2);
                    if (isset($options['maxFiles']) && $options['maxFiles'] > 1 && in_array($this->attribute, $attributes)) {
                        $multiple = true;
                    }
                }
            }
        }

        if ($multiple) {
            $files = File::find()->where([
                'deleted' => 0,
                'model' => get_class($this->model),
                'property' => $this->attribute,
                'pk' => $this->model->primaryKey,
            ])->all();

            if (count($files) > 0) {
                foreach ($files as $file) {
                    $content .= $this->renderItemFileView($file);
                }
            } else {
                $content .= '<span class="not-set">(' . Yii::t('app', 'not set') . ')</span>';
            }
        } else {

            $file = File::find()->where([
                'deleted' => 0,
                'model' => get_class($this->model),
                'property' => $this->attribute,
                'pk' => $this->model->primaryKey,
            ])->one();

            if (NULL !== $file) {
                $content .= $this->renderItemFileView($file);
            } else {
                $content .= '<span class="not-set">(' . Yii::t('app', 'not set') . ')</span>';
            }
        }

        return $this->renderBeginFileView() . "\n" .$content . "\n" . $this->renderEndFileView();
    }

    public function renderItemFileView($file)
    {
        $content = '';

        $inputID = Html::getInputId($this->model, $this->attribute);

        $liOptions = [
            'id' => 'file-view-' . $inputID . '-file-' . $file->id,
            'class' => 'file-view-file ' . str_replace('/', '-', $file->kind) . ' ' . $file->extension,
        ];
        $content .= Html::beginTag('li', $liOptions);

        $linkOptions = [
            'class' => 'file-view-file-link',
        ];
        if (!in_array($file->kind, ['image/jpeg','image/png'])) {
            $content .= Html::a($file->name . ' (' . $file->sizeInfo . ')', ['/file/file/view', 'id'=>$file->id], $linkOptions);
        } else {
            $imageOptions = [
                'class' => 'file-view-file-image',
                'width' => '100',
                'height' => '100',
                'alt' => $this->model->getAttributeLabel($this->attribute),
            ];

            if (isset($this->options['imageWidth']) && $this->options['imageWidth'] != '') {
                $imageOptions['width'] = intval($this->options['imageWidth']);
            }

            if (isset($this->options['imageHeight']) && $this->options['imageHeight'] != '') {
                $imageOptions['height'] = intval($this->options['imageHeight']);
            }

            if (isset($this->options['imageAlt']) && $this->options['imageAlt'] != '') {
                $imageOptions['alt'] = $this->options['imageAlt'];
            }

            $content .= Html::a(Html::img($file->path, $imageOptions), ['/file/file/view', 'id'=>$file->id], $linkOptions);
        }

        $content .= Html::endTag('li');

        return $content;
    }

    public function renderBeginFileView()
    {
        $inputID = Html::getInputId($this->model, $this->attribute);
        $attribute = Html::getAttributeName($this->attribute);
        $options = $this->options;
        $class = isset($options['class']) ? [$options['class']] : [];
        $class[] = "file-$inputID";
        $options['class'] = implode(' ', $class);

        return Html::beginTag('ul', $options);
    }

    public function renderEndFileView()
    {
        return Html::endTag('ul');
    }

    /**
     * Registers the needed client script and options.
     */
    public function registerClientScript()
    {
         $js = '';
         $view = $this->getView();
         $this->initClientOptions();
         if (!empty($this->mask)) {
             $this->clientOptions['mask'] = $this->mask;
         }
         $this->hashPluginOptions($view);
         if (is_array($this->definitions) && !empty($this->definitions)) {
             $js .= '$.extend($.' . self::PLUGIN_NAME . '.defaults.definitions, ' . Json::encode($this->definitions) . ");\n";
         }
         if (is_array($this->aliases) && !empty($this->aliases)) {
             $js .= '$.extend($.' . self::PLUGIN_NAME . '.defaults.aliases, ' . Json::encode($this->aliases) . ");\n";
         }
         $id = $this->options['id'];
         $js .= '$("#' . $id . '").' . self::PLUGIN_NAME . "(" . $this->_hashVar . ");\n";
         MaskedInputAsset::register($view);
         $view->registerJs($js);
    }
}
