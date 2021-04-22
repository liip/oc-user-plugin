<?php

namespace Liip\User\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Str;

/**
 * AccessToken Form Widget
 */
class AccessToken extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'accesstoken';

    /**
     * @inheritDoc
     */
    public function init()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('accesstoken');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return $value;
    }

    public function onRefresh()
    {
        $id = $this->getId('input');
        $this->vars['id'] = $this->getId();
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = Str::random(80);

        return [
            "#wrapper-$id" => $this->makePartial('control')
        ];
    }
}
