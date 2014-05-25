var staff = {
    init: function() {
        if ($.address.path() == '/') {
            this.addAccount();
            this.editAccount();
            this.removeAccount();
        } else if ($.address.path() == '/volunteers') {
            this.addVolunteer();
            this.editVolunteer();
            this.removeAccount();
        } else if ($.address.path() == '/contributions') {
            this.addContribution();
            this.removeContribution();
            this.editContribution();
        }
    },
    addContribution: function() {
        $('#create-new-contribution-form').submit(function(e) {
            e.preventDefault();

            var $this = $(this),
                    url = 'staff/ajax/createcontribution/',
                    type = 'POST';

            common.ajax(url, type, $this.serialize(), function() {
                common.navigation.changeState($.address.path(), $.address.queryString(), $('#content-div'));
                common.navigation.oldQuery = $.address.queryString();
            });
        });
    },
    removeContribution: function() {
        $('.remove-btn').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            common.modalBox.showConfirm('Are you sure you want to delete <strong>' + $('td:first-child', $this.closest('tr')).text() + '\'s</strong> contribution of <strong>' + $('td:nth-child(4)', $this.closest('tr')).text() + '</strong> hours on <strong>' + $('td:nth-child(3)', $this.closest('tr')).text() + '</strong> in <strong>' + $('td:nth-child(2)', $this.closest('tr')).text() + '</strong>?', {
                deleteConfirm: true,
				yesFn: function() {
                    var id = $('input[name="id"]', $this.closest('td')).val(),
                            url = 'staff/ajax/deletecontribution/',
                            type = 'POST';

                    common.ajax(url, type, {
                        id: id
                    }, function() {
                        common.navigation.changeState($.address.path(), $.address.queryString(), $('#content-div'));
                        common.navigation.oldQuery = $.address.queryString();
                    });
                }
            });
        });
    },
    editContribution: function() {
        $('.edit-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="id"]', $(this).closest('td')).val();

            //Load row from the DB
            common.ajax('staff/ajax/contributions', 'get', {
                id: id
            }, function(result) {
                common.modalBox.showModal('staff/editcontribution.ejs', {payload: result});
                staff.saveEdit('editcontribution');
            });

        });
    },
    addVolunteer: function() {
        $('#create-new-volunteer-form-job-type').select2({minimumResultsForSearch: -1});
        $('#create-new-volunteer-form').submit(function(e) {
            e.preventDefault();

            var $this = $(this),
                    url = 'staff/ajax/createvolunteer/',
                    type = 'POST';

            common.ajax(url, type, $this.serialize(), function() {
                common.navigation.changeState($.address.path(), $.address.queryString(), $('#content-div'));
                common.navigation.oldQuery = $.address.queryString();
            });
        });
    },
    editVolunteer: function() {
        $('.edit-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="id"]', $(this).closest('td')).val();

            //Load row from the DB
            common.ajax('staff/ajax/volunteers', 'get', {
                id: id
            }, function(result) {
                common.modalBox.showModal('staff/editvolunteer.ejs', {payload: result});
                staff.saveEdit('editvolunteer');
            });

        });
    },
    addAccount: function() {
        $('#add-new-account-button').click(function(e) {
            //Load row from the DB
            common.ajax('staff/ajax/create', 'get', {}, function(result) {
                common.modalBox.showModal('staff/create.ejs', {type: 'add', payload: result});
                staff.saveEdit('createuser');
            });
        });
    },
    editAccount: function() {
        $('.edit-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="id"]', $(this).closest('td')).val();

            //Load row from the DB
            common.ajax('staff/ajax/', 'get', {
                id: id
            }, function(result) {
                common.modalBox.showModal('staff/create.ejs', {type: 'edit', payload: result});
                staff.saveEdit('edituser');
            });

        });
    },
    saveEdit: function(method) {
        $('#staff-create-form').submit(function(e) {
            e.preventDefault();

            var $this = $(this),
                    url = 'staff/ajax/' + method + '/',
                    type = 'POST';

            common.ajax(url, type, $this.serialize(), function() {
                common.modalBox.hideModal();
                if ($.address.queryString()) {
                    common.navigation.changeState($.address.path(), $.address.queryString(), $('#content-div'));
                    common.navigation.oldQuery = $.address.queryString();
                } else {
                    $.address.update();
                }
            });
        });
    },
    removeAccount: function() {
        $('.remove-btn').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            common.modalBox.showConfirm('Are you sure you want to delete \'' + $('td:first-child', $this.closest('tr')).text() + '\'?', {
				deleteConfirm: true,
                yesFn: function() {
                    var id = $('input[name="id"]', $this.closest('td')).val(),
                            url = 'staff/ajax/deleteuser/',
                            type = 'POST';

                    common.ajax(url, type, {
                        id: id
                    }, function() {
                        $.address.update();
                    });
                }
            });
        });
    }
};