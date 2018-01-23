jQuery(function($){
    var _l = $('.login');
    _l.find('#login').addClass('animated').addClass('flipInX');
    var _form = _l.find('form').attr('data-step', '1').attr('data-flag', '0');
    var _login = _l.find('#user_login').closest('p').addClass('animated').attr('autosuggest', 'off').attr('autocomplete', 'off');
    var _password = _l.find('#user_pass').closest('p').addClass('animated').attr('autosuggest', 'off').attr('autocomplete', 'off');
    _password.hide();
    _form.on('submit', function(){
        var _t = $(this);
        var do_submit = true;
        if(_t.attr('data-flag') == '0' && _login.find('input').val() != '' ){
            _login.addClass('flipOutX').hide();
            _password.show().addClass('flipInX');
            _t.attr('data-flag', '1').attr('data-step', '2');
            do_submit = false;
        }
        if(_login.find('input').val() == ''){
            _login.find('input').focus();
            do_submit = false;
        }else if(_password.find('input').val() == ''){
            _password.find('input').focus();
            do_submit = false;
        }
        return do_submit;
    });
});
