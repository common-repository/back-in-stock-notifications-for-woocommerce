jQuery(document).ready(function($) {
    $('.single_variation_wrap').on('show_variation', function ( event, variation ) {
        $('#wsnm-cta').on('click', wsnm_button_click);
    });
    $('#wsnm-cta').on('click', wsnm_button_click);
    function wsnm_button_click(){
        const $cta = $(this);
        if(!$cta.hasClass('wsnm-waiting')){
            const product_id = $cta.data('product');
            let variation_id = "";
            if ($cta.data('variation')) {
                variation_id = $cta.data('variation');
            }
            const nonce = $cta.data('nonce');
            let data = {
                'action': 'wsnm_open_popup',
                'product': product_id,
                'variation': variation_id,
                'nonce': nonce
            };
            $cta.addClass('wsnm-waiting');
            $cta.append('<span class="wsnm-spinner"></span>');
            $cta.prop("disabled", true);
            $.post(ajax_object.ajax_url, data, function(response) {
                $cta.removeClass('wsnm-waiting');
                $cta.prop("disabled", false);
                $('.wsnm-spinner').remove();
                if(!response.error){
                    $('body').append(response.content);
                    $('#wsnm-submit-form').on('click touch', function(){
                        $('#wsnm-out-of-stock-form').trigger('submit');
                    })
                    if(response.recaptcha_status){
                        grecaptcha.render('ligh-recaptcha-id', {
                            'sitekey': response.recaptcha_key
                        });
                    }
                    $('#wsnm-out-of-stock-form').on('submit', function(e){
                        e.preventDefault();
                        if(!$('#wsnm-submit-form').hasClass('wsnm-waiting')){
                            const email = $('input[name=wsnm_form_email]').val();
                            const nonce = $('input[name=wsnm_add_request_field]').val();
                            let recaptcha = '';
                            if($('textarea[name=g-recaptcha-response]').length){
                                recaptcha = $('textarea[name=g-recaptcha-response]').val();
                            }
                            let first_name = '';
                            let last_name = '';
                            if($('input[name=wsnm_form_first_name]').length){
                                first_name = $('input[name=wsnm_form_first_name]').val();
                            }
                            if($('input[name=wsnm_form_last_name]').length){
                                last_name = $('input[name=wsnm_form_last_name]').val();
                            }
                            let data = {
                                'action': 'wsnm_save_request',
                                'first_name': first_name,
                                'last_name': last_name,
                                'email': email,
                                'product': product_id,
                                'variation': variation_id,
                                'recaptcha': recaptcha,
                                'nonce': nonce
                            };
                            $('#wsnm-submit-form').addClass('wsnm-waiting');
                            $('#wsnm-submit-form').append('<span class="wsnm-spinner"></span>');
                            $.post(ajax_object.ajax_url, data, function(response) {
                                $('#wsnm-submit-form').removeClass('wsnm-waiting');
                                $('.wsnm-spinner').remove();
                                if(response.status){
                                    $('#wsnm-modal .wsnm-modal-body').html(response.message);
                                    setTimeout(function(){
                                        jQuery('#wsnm-modal').remove();
                                    },3000);
                                }else{
                                    $('#wsnm-ajax-response').text(response.message);
                                    grecaptcha.reset();
                                    if(response.code === 'recaptcha-issue'){
                                        $('#ligh-recaptcha-id').css({
                                            'border-color': 'red',
                                            'border-style': 'solid',
                                            'border-width': '1px'
                                        })
                                    }
                                }
                                console.log(response);
                            })
                        }
                    })
                }

                $('.wsnm-modal-close').on('click', function(){
                    $('#wsnm-modal').remove();
                })
            });
        }
    }
    $(window).on('click', function(e) {
        if (e.target.id == 'wsnm-modal') {
            $('#wsnm-modal').remove();
        }
    })
});
