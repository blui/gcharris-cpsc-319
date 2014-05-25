$(document).ready(function() {
    $('body').on('focus', 'input', function(){ $(this).attr('autocomplete', 'off'); });
    $('#login-button').click(function(e){
        e.preventDefault();

        var email = $('input[name="email"]'),
        password = $('input[name="password"]');

        var $this = $(this);
        $this.addClass('btn-striped')
            .data('loading-text', '<i class="icon-refresh icon-white"></i> Signing in...')
            .button('loading');

        $.ajax({
            url: '/auth/login',
            type: 'POST',
            data: {
                email: email.val(), 
                password: password.val()
            },
            dataType: "json",
            success: function(response){
                if(response === 1){
                    location.reload();
                }else{
                    $("#invalid-cred").show();
                    password.val("");
                }
            }
        }).done(function(data, textStatus, jqXHR){
            if(data !== 1){
                $this.button('reset').removeClass('btn-striped');
            }
        });
    });
    
        $('#reset-button').click(function(e){
        e.preventDefault();

        var email = $('input[name="email"]');

        var $this = $(this);
        $this.addClass('btn-striped')
            .data('loading-text', '<i class="icon-refresh icon-white"></i> Sending Reset...')
            .button('loading');

        $.ajax({
            url: 'recover',
            type: 'POST',
            data: {
                email: email.val()
            },
            dataType: "json",
            success: function(response){
                if(response === 1){
                    $("#reset-box").text("Instructions on how to reset your password have been sent to your email.").addClass('alert alert-success');
                }else{
                    $("#invalid-email").show();
                }
            }
        }).done(function(data, textStatus, jqXHR){
            if(data !== 1){
                $this.button('reset').removeClass('btn-striped');
            }
        });
    });

    $('input[name="email"]').focus();
});