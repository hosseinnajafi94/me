<?php
namespace me\widgets;
use Me;
use me\helpers\Json;
use me\helpers\Html;
use me\helpers\ArrayHelper;
use me\assets\ActiveFormAsset;
class ActiveForm extends Widget {
    public static $counter      = 0;
    public static $autoIdPrefix = 'form';
    public $action              = [];
    public $method              = 'post';
    public $attributes          = [];
    public $options             = [];
    public $fieldConfig         = [];
    public $fieldClass          = 'me\widgets\ActiveField';
    public $requiredCssClass    = 'required';
    public $errorCssClass       = 'has-error';
    public $enableClientScript  = true;
    public function init() {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        ob_start();
        ob_implicit_flush(false);
    }
    public function run() {
        $content = ob_get_clean();
        $html    = '';
        $html    .= Html::beginForm($this->action, $this->method, $this->options) . "\n";
        $html    .= $content;
        $html    .= Html::endForm() . "\n";
        if ($this->enableClientScript) {
            $this->registerClientScript();
        }
        return $html;
    }
    /**
     * @return ActiveFie Description
     */
    public function field($model, $attribute, $options = []) {
        ArrayHelper::AddIfNotExist($this->fieldConfig, 'class', $this->fieldClass);
        $config = ArrayHelper::Extend($this->fieldConfig, $options, [
                    'model'     => $model,
                    'attribute' => $attribute,
                    'form'      => $this,
        ]);
        return Me::createObject($config);
    }
    public function registerClientScript() {
        $id          = $this->options['id'];
        $options     = json_encode($this->getClientOptions());
        $attributes  = Json::encode($this->attributes);
        $view       = $this->getView();
        ActiveFormAsset::register($view);
        $view->registerJs("$('#$id').activeForm($attributes, $options);");
    }
    public function getClientOptions() {
        return [];
//        $options = [
//            'encodeErrorSummary'  => $this->encodeErrorSummary,
//            'errorSummary'        => '.' . implode('.', preg_split('/\s+/', $this->errorSummaryCssClass, -1, PREG_SPLIT_NO_EMPTY)),
//            'validateOnSubmit'    => $this->validateOnSubmit,
//            'errorCssClass'       => $this->errorCssClass,
//            'successCssClass'     => $this->successCssClass,
//            'validatingCssClass'  => $this->validatingCssClass,
//            'ajaxParam'           => $this->ajaxParam,
//            'ajaxDataType'        => $this->ajaxDataType,
//            'scrollToError'       => $this->scrollToError,
//            'scrollToErrorOffset' => $this->scrollToErrorOffset,
//            'validationStateOn'   => $this->validationStateOn,
//        ];
//        if ($this->validationUrl !== null) {
//            $options['validationUrl'] = Url::to($this->validationUrl);
//        }
//
//        // only get the options that are different from the default ones (set in yii.activeForm.js)
//        return array_diff_assoc($options, [
//            'encodeErrorSummary'  => true,
//            'errorSummary'        => '.error-summary',
//            'validateOnSubmit'    => true,
//            'errorCssClass'       => 'has-error',
//            'successCssClass'     => 'has-success',
//            'validatingCssClass'  => 'validating',
//            'ajaxParam'           => 'ajax',
//            'ajaxDataType'        => 'json',
//            'scrollToError'       => true,
//            'scrollToErrorOffset' => 0,
//            'validationStateOn'   => self::VALIDATION_STATE_ON_CONTAINER,
//        ]);
    }
}