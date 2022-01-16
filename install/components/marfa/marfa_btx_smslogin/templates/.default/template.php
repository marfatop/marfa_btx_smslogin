<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="marfa--login--by--sms">
        <h2>По смс</h2>
        <div class="marfa--login--by--sms--body" style="display: flex; flex-direction: column;">
            <input type="tel" data-mask="+7 (999) 999-99-99" class="bx-auth-input auth--sms-tel js-input-mask" size="30" placeholder="Тел для смс" name="PERSONAL_PHONE" value=""  style="margin: 4px 0px; height:40px;">
            <div class="error"></div>
            <input class="btn btn-primary auth--sms-btn-code" type="button" value="Получить код" style="margin: 4px 0px;">
            <input class="bx-auth-input auth--sms-code" type="number" placeholder="код из смс"  style="margin: 4px 0px; height:40px; visibility: hidden">
        </div>
    </div>