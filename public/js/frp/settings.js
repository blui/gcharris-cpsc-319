var settings = {
    init: function(){
         this.setup();
    },
    setup: function(){
        $('#my-account-form').submit(function(e){
            e.preventDefault();

            common.ajax('/settings/ajax/edit', 'POST', $(this).serialize(), function(){
                window.location.reload();
            });
        });
    }
};