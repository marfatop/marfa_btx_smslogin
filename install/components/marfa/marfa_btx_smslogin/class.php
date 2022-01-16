<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();


/**
 *
 */
class MarfaLoginBySMS extends CBitrixComponent
{
    function __construct($component)
    {
        parent::__construct($component);
    }

    public function executeComponent()
    {
        $this->GetMain();
    }

    private function GetMain()
    {
        global $APPLICATION;
        $curDir = $APPLICATION->GetCurPage();
        return $curDir;
    }
}