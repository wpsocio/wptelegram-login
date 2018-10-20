(function( $, I18n ) {
	'use strict';
	var app1 = {};
    app1.configure = function(){
        app1.metabox = $( '#cmb2-metabox-wptelegram_login' );
        app1.bot_token = app1.metabox.find('#bot_token');
        app1.bot_username = app1.metabox.find('#bot_username');
    };
    app1.set_read_only_fields = function(){
        app1.bot_username.prop("readonly", true);
        app1.metabox.find('#avatar_meta_key').prop("readonly", true);
    };
    app1.init = function () {
        app1.configure();
        app1.set_read_only_fields();
        app1.check_redirect_to();
        app1.metabox.on( 'blur', '#bot_token,#bot_username', app1.handle_blur );
        app1.metabox.on( 'click', '#button-bot_token', app1.test_bot_token );
        app1.metabox.on( 'dblclick', '#bot_username,#avatar_meta_key', app1.handle_double_click );
        app1.metabox.on( 'change', 'input[type=checkbox][name=disable_signup]', app1.toggle_disable_signup_dependents );
        app1.metabox.on( 'change', 'input[type=radio][name=redirect_to]', app1.check_redirect_to );
        app1.metabox.on( 'change', 'input[type=radio][name=button_style],#corner_radius', app1.fix_corner_radius );

        // trigger on ready
        app1.metabox.find('input[type=checkbox][name=disable_signup]').trigger('change');
    };
    app1.test_bot_token = function( evt, params ){
    	if (!app1.bot_token.val().trim()) {
    		alert(I18n.empty_bot_token);
    		return;
    	}
        if (  !app1.validate('bot_token') ) {
            return;
        }

        var id = $(this).attr('data-id');

    	var val = app1[id].val().replace(/[\s@]/g,'');
        
    	switch (id) {
		    case 'bot_token':
		        app1.handle_test_bot_token(val);
		        break;
		}
    };
    app1.handle_test_bot_token = function( bot_token ){
    	app1.send_ajax_request( bot_token, '/getMe', {}, app1.handle_bot_token_test );
    };
    app1.send_ajax_request = function( bot_token, endpoint, data, response_handler ){
    	var url = 'https://api.telegram.org/bot' + bot_token + endpoint;
        $.ajax({
            type: "POST",
            contentType: "application/json; charset=utf-8",
            url: url,
            dataType: "json",
            crossDomain:true,
            data: JSON.stringify( data ),
            complete: response_handler
        });
    };
    app1.handle_blur = function ( evt ) {
        app1.metabox.find('.info').addClass("hidden").siblings().remove();
    	var id = $(this).attr('id');
        var val = app1[id].val();
    	if ( '' != val ) {
    		app1.validate(id);
    	}
    };
    app1.handle_double_click = function ( evt ) {
    	$(this).prop("readonly", false).focus();
    };
    app1.check_redirect_to = function ( evt ) {
        var redirect_to = app1.metabox.find('input[type=radio][name=redirect_to]:checked').val();
        var redirect_url = app1.metabox.find('#redirect_url');
        if ('custom_url'!=redirect_to) {
            redirect_url.hide();
        } else {
            redirect_url.show();
        }
    };
    app1.toggle_disable_signup_dependents = function ( evt ) {
        var $this = $(this);
        var id = $this.attr('id');

        var elem_rows = app1.metabox.find('[data-the-conditional-id="'+id+'"]').closest('.cmb-row');

        if ($this.is(':checked')) {
            elem_rows.hide();
        } else {
            elem_rows.show();
        }
    };
	app1.fix_corner_radius = function ( event ) {

		var form = app1.metabox.closest('#wptelegram_login').get(0);
		if ('corner_radius' == event.target.id) {
			var corner_radius = form.corner_radius.value.replace(/[^0-9]+/g, '');
			form.corner_radius.realValue = corner_radius;
		} else {
			var corner_radius = (form.corner_radius.realValue || form.corner_radius.defaultValue).replace(/[^0-9]+/g, '');
			form.corner_radius.value = corner_radius;
		}
		if (''==corner_radius) {
			return;
		}
		corner_radius = parseInt(corner_radius);
		if (corner_radius <= 0) {
			form.corner_radius.value = 0;
		} else if (form.button_style.value == 'small' && corner_radius > 10) {
			form.corner_radius.value = 10;
		} else if (form.button_style.value == 'medium' && corner_radius > 14) {
			form.corner_radius.value = 14;
		} else if (form.button_style.value == 'large' && corner_radius > 20) {
			form.corner_radius.value = 20;
		} else if (form.corner_radius.value != corner_radius.toString()) {
			form.corner_radius.value = corner_radius;
		}
	};
    app1.handle_bot_token_test = function( jqXHR, textStatus ) {
        var elem = app1.metabox.find('#bot_token-info');
        
        elem.removeClass("hidden");

        if ( undefined == jqXHR  || '' == jqXHR.responseText ) {
            elem.text('');
            elem.append('<span>'+I18n.error+' '+I18n.could_not_connect+'</span>');
        }else if ( true == JSON.parse( jqXHR.responseText ).ok ){
            var result = JSON.parse( jqXHR.responseText ).result;
            elem.text( result.first_name + ' ' + ( undefined == result.last_name ? ' ' :  result.last_name ) + '(@' + result.username + ')' );
            app1.bot_username.val(result.username);
        }else{
            elem.text(I18n.error + ' ' + jqXHR.status + ' (' + jqXHR.statusText + ')');
        }
    };
    app1.validate = function( id ){
		var val = app1.metabox.find('#'+id).val().replace(/[\s@]/g,'');
		app1.metabox.find('#'+id).val(val);
    	var regex;
    	switch (id) {
		    case 'bot_token':
		        regex = new RegExp(/^\d{9}:[\w-]{35}$/);
		        break;
		    case 'bot_username':
		        regex = new RegExp(/^[a-z]\w{3,30}[^\W_]$/i);
		        break;
		}
		if ( regex.test( val ) ) {
	        app1.metabox.find('#'+id+'-err').addClass("hidden");
	        return true;
	    } else {
	        app1.metabox.find('#'+id+'-err').removeClass("hidden");
	        return false;
	    }
    };
	$( document ).ready( app1.init );
})( jQuery, wptelegram_login_I18n );