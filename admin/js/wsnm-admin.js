jQuery(document).ready(function($){
    //confirmation email
    $('input[name=wsnm_subscribe_confirmation_status]').on('change', function (){
        if(this.checked) {
            $('#wsnm_subscribe_confirmation_row').css('display', 'block');
        }else{
            $('#wsnm_subscribe_confirmation_row').css('display', 'none');
        }
    })
    //recaptcha
    $('input[name=wsnm_form_recaptcha_status]').on('change', function (){
        if(this.checked) {
            $('#wsnm_form_recaptcha').css('display', 'block');
        }else{
            $('#wsnm_form_recaptcha').css('display', 'none');
        }
    })

    //Pause notification - disable notice
    $('input[name=wsnm_product_pause]').on('change', function (){
        if(this.checked) {
            $('.wsnm-automatically-mode-enabled').addClass('wsnm-disable-me');
        }else{
            $('.wsnm-automatically-mode-enabled').removeClass('wsnm-disable-me');
        }
    })

    $('#wsnm_btn_background_color').wpColorPicker();
    $('#wsnm_btn_text_color').wpColorPicker();
})