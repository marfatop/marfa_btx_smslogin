
$('body').on('click', '.auth--email-btn-enter', function () {
    let prop_display = $('.auth--email').css('display')

    if (prop_display === 'none') {
        let el_show = $('.auth--email')
        let el_hide = $('.auth--sms')
        AuthBlockHide(el_show, el_hide)
        btnAuthToggle()
    }

});
$('body').on('click', '.auth--sms-btn-enter', function () {
    let prop_display = $('.auth--sms').css('display')

    if (prop_display === 'none') {
        let el_show = $('.auth--sms')
        let el_hide = $('.auth--email')
        AuthBlockHide(el_show, el_hide)
        btnAuthToggle()
    }
});


function btnAuthToggle() {

    $('.auth--email-btn-enter').toggleClass('btn--style-1')
    $('.auth--email-btn-enter').toggleClass('btn-primary')
    $('.auth--sms-btn-enter').toggleClass('btn--style-1')
    $('.auth--sms-btn-enter').toggleClass('btn-primary')

}

function AuthBlockHide(el_show, el_hide) {
    console.log(el_show)
    el_show.css('opacity', '0')
    el_show.css('display', 'block')
    let style_hide = {
        'transition': 'all 1s linear',
        'opacity': '0',
    }
    let style_show = {
        'transition': 'all 1s linear',
        'opacity': '1',
        'display': 'block'
    }
    el_hide.css(style_hide)
    setTimeout(() => el_hide.css('display', 'none'), 1000)
    setTimeout(() => el_show.css(style_show), 1000)

}

$('body').on('click', '.auth--sms-btn-code', function () {
    let tel = $('.auth--sms-tel').val()
    let dtel = tel.replace(/\D+/g, "")
    // console.log(tel)
    if (dtel.length === 11) {
        let path = '/local/modules/auth.by.sms/lib/ajax.php';
        $.ajax({
            type: 'POST',
            url: path,
            data: {phone: tel, auth: 'Y', action: 'sendSmsCode'},
            success: function (data) {
                let json = JSON.parse(data)
                if(json['timer']>0)
                {
                    setBlockBtnCode(json).then(removeBlockBtnCode())
                }
                if (json['result'] === 'error' && json['code']>0) {
                    addErrorSMSMsg(json)
                } else {
                    addSuccsessSMSMsg(json)
                    $('.auth--sms-code').css('visibility', 'visible')
                    // $('.bx-auth .bx-auth-note')[0].innerHTML = json['text']
                }
            }
        })
    }
})

function setTimerBtnCode(json){
    let seconds=json['timer']
    let old_msg=$('.auth--sms-btn-code').val
    let msg='', format_seconds


    let btn_msg=[];
    var seconds_timer_id = setInterval(function() {
        if (seconds > 0) {
            seconds --;
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            format_seconds=new Date(seconds*1000).toUTCString().split(/ /)[4]
            msg='Новый код через '+format_seconds

            $('.auth--sms-btn-code').val(msg)
        } else {
            clearInterval(seconds_timer_id)
            $('.auth--sms-btn-code').val(old_msg)
            btn_msg['text']='Осталось попыток ввода '+json['count_try']+'Генераций кода '+json['count_generate']
            removeBlockBtnCode()
            addErrorSMSMsg(btn_msg)
        }
    }, 1000);
}
function addSuccsessSMSMsg(json){
    console.log(json)
    let msg=''
    let msg_text= json['text'].length>0 ? '<span class="auth--sms-error-msg">'+json['text']+'</span>' : ''
    let msg_timer= json['timer'].length>0 ? '<span class="auth--sms-error-timer">'+json['timer']+'</span>' : ''
    msg=msg_text+msg_timer
    $('.bx-auth .bx-auth-note')[0].innerHTML =msg
}
function addErrorSMSMsg(json){
    console.log(json)
    let msg=''
    let msg_text= json['text'].length>0 ? '<span class="auth--sms-error-msg">'+json['text']+'</span>' : ''
    let msg_timer= json['timer'] ? '<span class="auth--sms-error-timer">'+json['timer']+'</span>' : ''
    $('.bx-auth-note')[0].innerHTML =msg_text
    //console.log($('.bx-auth-note'))
    msg=msg_text+msg_timer
    $('.auth--sms .error').innerHTML =msg
}

function setBlockBtnCode(json){
    let timer=json['timer']
    return new Promise(function(resolve, reject){ setTimeout(function() {
        setTimerBtnCode(json)
        $('.auth--sms-btn-code').toggleClass('btn-primary', false).toggleClass('btn--style-1', true)
        $('.auth--sms-btn-code').prop('disabled', 'disabled');
    }, timer)})
}
function removeBlockBtnCode(){
    $('.auth--sms-btn-code').toggleClass('btn-primary', true).toggleClass('btn--style-1', false)
    $('.auth--sms-btn-code').prop('disabled', '');
}
$('body').on('input', '.auth--sms-code', function (e) {
    let _this = e.target
    let maxVal = 9999;

    if (_this.value.match(/[^0-9]/g)) {
        _this.value = _this.value.replace(/[^0-9]/g, '');
    }

    if (_this.value < 0) _this.value = 0;
    if (_this.value > maxVal) _this.value = _this.value.slice(0, 4);

    if (this.value.length === 4) {
        $('.auth--sms-btn-enter').toggleClass('btn--style-1', false).toggleClass('btn-primary', true)
    }
})


$('body').on('click', '.auth--sms-btn-enter', function () {

    let code = $('.auth--sms-code').val()
    let tel = $('.auth--sms-tel').val()

    if (code.length === 4) {
        let path = '/local/modules/auth.by.sms/lib/ajax.php';

        $.ajax({
            type: 'POST',
            url: path,
            data: {phone: tel, code: code, auth: 'Y', action: 'confirmSmsCode'},
            success: function (data) {
                console.log(data)
                let json = JSON.parse(data)

                if (json['result'] === 'error') {
                    console.log(data)
                    $('.bx-auth .bx-auth-note')[0].innerHTML = json['text']
                    $('.auth--sms .error')[0].innerHTML = json['text']
                } else {
                    location.href = json['path'];
                }

            }
        })
    }

})
