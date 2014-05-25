var partners = {
    init: function() {
        this.emailPartners();
        this.editPartner();
        this.removePartner();
        this.addPartner();
    },
    emailPartners: function() {
        common.utility.checkall($('#partner-table'));

        $('#email-partners').click(function(e) {
            e.preventDefault();

            common.ajax('partners/ajax/', 'get', {}, function(result) {
                var checked_recipients = $('input[name="checked_ids[]"]').serializeArray(),
                        ajax_url = 'partners/ajax/email/';
                common.email.box("Email Partners", result, checked_recipients, ajax_url, {
                    "email": "email",
                    "name": "name"
                });

            });
        });
    },
    editPartner: function() {
        $('.edit-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="checked_ids[]"]', $(this).closest('tr')).val();

            //Load row from the DB
            common.ajax('partners/ajax/', 'get', {
                id: id
            }, function(result) {
                common.modalBox.showModal('partners/edit.ejs', result[0]);
                partners.saveEdit();
            });

        });
    },
    saveEdit: function() {
        $('#edit-partner-form').submit(function(e) {
            e.preventDefault();

            var $this = $(this),
                    url = 'partners/ajax/edit/',
                    type = 'POST';

            common.ajax(url, type, $this.serialize(), function() {
                common.modalBox.hideModal();
                $.address.update();
            });

        });
    },
    removePartner: function() {
        $('.remove-btn').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            common.modalBox.showConfirm('Are you sure you want to delete \'' + $('td:nth-child(2)', $this.closest('tr')).text() + '\'?', {
                deleteConfirm: true,
                yesFn: function() {
                    var id = $('input[name="checked_ids[]"]', $this.closest('tr')).val(),
                            url = 'partners/ajax/delete/',
                            type = 'POST';

                    common.ajax(url, type, {
                        id: id
                    }, function() {
                        $.address.update();
                    });
                }
            });
        });
    },
    addPartner: function() {
        $('#add-new-partner-form').submit(function(e) {
            e.preventDefault();
            var url = 'partners/ajax/create/',
                    type = 'POST';

            common.ajax(url, type, $(this).serialize(), function() {
                $.address.update();
            });
        });
    }
};




