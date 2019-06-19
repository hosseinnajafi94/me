<?php
namespace me\widgets;
use Me;
use me\assets\ActiveFormAsset;
use me\components\Model;
use me\helpers\ArrayHelper;
use me\helpers\Html;
use me\helpers\Json;
class ActiveForm extends Widget {
    public static $counter       = 0;
    public static $autoIdPrefix  = 'form';
    public $action               = [];
    public $method               = 'post';
    public $attributes           = [];
    public $options              = [];
    public $fieldConfig          = [];
    public $fieldClass           = 'me\widgets\ActiveField';
    public $requiredCssClass     = 'required';
    public $enableClientScript   = true;
    public $errorSummaryCssClass = 'error-summary';
    public $errorCssClass        = 'has-error';
    public $successCssClass      = 'has-success';
    public $validateOnSubmit     = true;
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
     * @param Model $model model
     * @param string $attribute attribute name
     * @param array $options options
     * @return ActiveFie Description
     */
    public function field(Model $model, string $attribute, array $options = []): ActiveField {
        ArrayHelper::AddIfNotExist($this->fieldConfig, 'class', $this->fieldClass);
        $config = ArrayHelper::Extend($this->fieldConfig, $options, [
            'model'     => $model,
            'attribute' => $attribute,
            'form'      => $this,
        ]);
        return Me::createObject($config);
    }
    /**
     * @return void
     */
    public function registerClientScript() {
        $id         = $this->options['id'];
        $options    = Json::encode($this->getClientOptions());
        $attributes = Json::encode($this->attributes);
        $view       = $this->getView();
        ActiveFormAsset::register($view);
        $view->registerJs("$('#$id').activeForm($attributes, $options);");
    }
    /**
     * @return array
     */
    public function getClientOptions() {
        $options = [
            'errorSummary'     => '.' . implode('.', preg_split('/\s+/', $this->errorSummaryCssClass, -1, PREG_SPLIT_NO_EMPTY)),
            'errorCssClass'    => $this->errorCssClass,
            'successCssClass'  => $this->successCssClass,
            'validateOnSubmit' => $this->validateOnSubmit,
        ];
        return array_diff_assoc($options, [
            'errorSummary'     => '.error-summary',
            'errorCssClass'    => 'has-error',
            'successCssClass'  => 'has-success',
            'validateOnSubmit' => true,
        ]);
    }
}