<?php
namespace me\validators;
use Me;
use me\components\View;
use me\components\Model;
use me\components\UploadedFile;
use me\assets\ValidationAsset;
use me\helpers\Json;
class FileValidator extends Validator {
    public $path;
    public $extensions;
    public $minSize;
    public $maxSize;
    public $maxFiles = 1;
    public $minFiles = 0;
    public $message;
    public $tooBig;
    public $tooSmall;
    public $tooMany;
    public $tooFew;
    public $wrongExtension;
    public function init() {
        parent::init();
        if ($this->message === null) {
            $this->message = 'File upload failed.';
        }
        if ($this->tooMany === null) {
            $this->tooMany = 'You can upload at most {limit}.';
        }
        if ($this->tooFew === null) {
            $this->tooFew = 'You should upload at least {limit}.';
        }
        if ($this->wrongExtension === null) {
            $this->wrongExtension = 'Only files with these extensions are allowed: {extensions}.';
        }
        if ($this->tooBig === null) {
            $this->tooBig = 'The file "{file}" is too big. Its size cannot exceed {limit}.';
        }
        if ($this->tooSmall === null) {
            $this->tooSmall = 'The file "{file}" is too small. Its size cannot be smaller than {limit}.';
        }
        if (is_array($this->extensions)) {
            $this->extensions = array_map('strtolower', $this->extensions);
        }
        else {
            $this->extensions = preg_split('/[\s,]+/', strtolower($this->extensions), -1, PREG_SPLIT_NO_EMPTY);
        }
        if ($this->maxFiles < $this->minFiles && $this->maxFiles === 1) {
            $this->maxFiles = $this->minFiles;
        }
    }
    public function validateValue(Model $model, string $attribute): array {
        /* @var $uploader UploadedFile */
        $value    = $model->$attribute;
        $model->$attribute = null;
        $multiple = ($this->maxFiles != 1 || $this->minFiles > 1);
        $uploader = Me::createObject(['class' => UploadedFile::class, 'file' => $value, 'multiple' => $multiple]);
        if ($uploader->multiple) {
            foreach ($uploader->name as $index => $name) {
                if ($this->isEmpty($name)) {
                    return [];
                }
                if (!empty($this->extensions) && !in_array($uploader->type[$index], $this->extensions, true)) {
                    return [$this->wrongExtension, ['extensions' => implode(', ', $this->extensions)]];
                }
                elseif ($this->maxSize !== null && $uploader->size[$index] > $this->maxSize) {
                    return [$this->tooBig, ['file' => $name, 'limit' => $this->maxSize]];
                }
                elseif ($this->minSize !== null && $uploader->size[$index] < $this->minSize) {
                    return [$this->tooSmall, ['file' => $name, 'limit' => $this->minSize]];
                }
            }
            $filesCount = count($uploader->name);
            if ($this->maxFiles !== null && $filesCount > $this->maxFiles) {
                return [$this->tooMany, ['limit' => $this->maxFiles]];
            }
            if ($this->minFiles && $this->minFiles > $filesCount) {
                return [$this->tooFew, ['limit' => $this->minFiles]];
            }
        }
        else {
            if ($this->isEmpty($uploader->name)) {
                return [];
            }
            if (!empty($this->extensions) && !in_array($uploader->type, $this->extensions, true)) {
                return [$this->wrongExtension, ['extensions' => implode(', ', $this->extensions)]];
            }
            elseif ($this->maxSize !== null && $uploader->size > $this->maxSize) {
                return [$this->tooBig, ['file' => $uploader->name, 'limit' => $this->maxSize]];
            }
            elseif ($this->minSize !== null && $uploader->size < $this->minSize) {
                return [$this->tooSmall, ['file' => $uploader->name, 'limit' => $this->minSize]];
            }
        }
        $model->$attribute = $uploader->save($this->path);
        return $this->required && $model->$attribute === null ? [$this->message, []] : [];
    }
    public function clientValidateAttribute(Model $model, string $attribute, View $view): string {
        $options             = [];
        $options['message']  = $this->formatMessage($this->message);
        $options['maxFiles'] = $this->maxFiles;
        $options['tooMany']  = $this->formatMessage($this->tooMany, ['limit' => $this->maxFiles]);
        if (!empty($this->extensions)) {
            $options['extensions']     = $this->extensions;
            $options['wrongExtension'] = $this->formatMessage($this->wrongExtension, ['extensions' => implode(', ', $this->extensions)]);
        }
        if ($this->minSize !== null) {
            $options['minSize']  = $this->minSize;
            $options['tooSmall'] = $this->formatMessage($this->tooSmall, ['limit' => $this->minSize]);
        }
        if ($this->maxSize !== null) {
            $options['maxSize'] = $this->maxSize;
            $options['tooBig']  = $this->formatMessage($this->tooBig, ['limit' => $this->maxSize]);
        }
        if ($this->minFiles > 0) {
            $options['minFiles'] = $this->minFiles;
            $options['tooFew']   = $this->formatMessage($this->tooFew, ['limit' => $this->minFiles]);
        }
        ValidationAsset::register($view);
        return 'me.validation.file(attribute, messages, ' . Json::encode($options) . ');';
    }
}