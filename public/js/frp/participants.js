var participants = {
    init: function() {
        this.editParticipant();
        this.addParticipant();
        this.removeParticipant();
        this.emailParticipants();
    },
    emailParticipants: function() {
        var checkAll = $('.check-all'),
                checkboxes = checkAll.closest('table').find('td input[type=checkbox]');

        checkAll.change(function() {
            checkboxes.prop('checked', this.checked)
        });

        checkboxes.change(function() {
            var all = checkboxes.length;
            var checked = checkboxes.filter(':checked').length;
            checkAll.prop('checked', all == checked);
        });

        $('#email-participants').click(function(e) {
            e.preventDefault();
            var title = "Email Participants",
                    ajax_url = 'participants/ajax/email/',
                    fields = {
                'email': 'guardian_email',
                'name': 'guardian_first_name',
                'last_name': 'guardian_last_name'
            };

            if ($('#check-all').is(':checked')) {
                common.email.box(title, {}, {}, ajax_url, fields, 1);
            } else {
                common.ajax('participants/ajax/', 'get', {}, function(result) {
                    var checked_recipients = $('input[name="checked_ids[]"]:checked').serializeArray()
                    console.log(checked_recipients);
                    common.email.box(title, result.items, checked_recipients, ajax_url, fields, 0);
                });
            }
        });

    },
    addParticipant: function() {
        $('#add-new-participant-btn').click(function(e) {
            e.preventDefault();

            common.ajax('participants/ajax/', 'get', {
                id: 0
            }, function(result) {
                participants.showAddEdit('create', result);
            });
        });
    },
    editParticipant: function() {
        $('.edit-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="checked_ids[]"]', $(this).closest('tr')).val();

            //Load row from the DB
            common.ajax('participants/ajax/', 'get', {
                id: id
            }, function(result) {
                participants.showAddEdit('edit', result);
            });
        });
    },
    showAddEdit: function(type, result, callback, query_info) {
        var modal = common.modalBox.showModal('participants/family.ejs', result);

        $('.select2', modal).select2({
            minimumResultsForSearch: 10,
            allowClear: true
        });
        $('#family-form-phone-number,#family-form-emergency-contact-phone').mask('(999) 999-9999');
        $('#family-form-postal-code').mask('a9a');

        $('#family-form-add-child').click(function(e) {
            e.preventDefault();
            ($.proxy(participants.addChildField, this))();
        });

        $('.remove-child-btn').click(function() {
            ($.proxy(participants.removeChildField, this))();
        });

        $('#family-form-family-role-other').focus(function() {
            $(this).prev().prop('checked', true);
        });

        participants.saveEdit(type, $.isFunction(callback) ? callback : false, query_info ? query_info : false);

        if (query_info && query_info.name) {
            if (/[0-9]/.test(query_info.name.substr(0, 1))) {
                if (query_info.name.length == 10) {
                    $('#family-form-phone-number').val('(' + query_info.name.substr(0, 3) + ') ' + query_info.name.substr(3, 3) + '-' + query_info.name.substr(6));
                }
            } else {
                var space = query_info.name.indexOf(' ');

                if (space == -1) {
                    space = query_info.name.length;
                }

                $('#family-form-guardian-name').val(query_info.name.substr(0, space));
                $('#family-form-guardian-name').next().val(query_info.name.substr(space + 1));
            }
        }

        modal.on('shown', function() {
            $('#family-form-emergency-contact-relation').select2({
                maximumSelectionSize: 1,
                selectOnBlur: true,
                tags: [
                    'Husband',
                    'Wife',
                    'Partner',
                    'Step-Parent',
                    'Grandparent',
                    'Relative',
                    'Friend',
                    'Family Friend'
                ]
            });
        });
    },
    addChildField: function() {
        var controlgroup = $(this).closest('.control-group').prev();
        var i = 0;

        if (controlgroup.hasClass('family-form-children')) {
            i = controlgroup.attr('id').split('-');
            i = parseInt(i[i.length - 1]) + 1;
        }

        var html = '<div class="family-form-children control-group" id="family-form-children-' + i + '" style="display:none;">'
                + '    <label class="control-label" for="family-form-child-' + (i + 1) + '" style="width:' + $('label', controlgroup).css('width') + '">Child ' + (i + 1) + '</label>'
                + '    <div class="controls" style="margin-left:' + $('.controls', controlgroup).css('margin-left') + '">'
                + '        <input type="hidden" name="children[' + i + '][id]">'
                + '        <input name="children[' + i + '][first_name]" type="text" id="family-form-child-' + (i + 1) + '" placeholder="First name..." style="width:95px;" pattern="([A-Za-z\'-])+( [A-Za-z\'-]+)*">'
                + '        <input name="children[' + i + '][last_name]" type="text" id="last_name" placeholder="Last name..." style="width:95px;" pattern="([A-Za-z\'-])+( [A-Za-z\'-]+)*">'
                + '        <input class="datepicker" name="children[' + i + '][birthday]" style="width:75px;" type="text" placeholder="Birth date..."> <a class="remove-child-btn btn"><i class="icon-remove"></i> Remove</a>'
                + '    </div>'
                + '</div>';

        controlgroup.after(html);

        $('#family-form-children-' + i + ' input.datepicker')
        .datepicker({
            format: 'mm/dd/yyyy',
            endDate: 'today',
            autoclose: true,
            forceParse: false
        })
        .mask('99/99/9999');

        common.utility.validation($('#family-form-children-' + i));

        $('#family-form-children-' + i + ' .remove-child-btn').click(function() {
            ($.proxy(participants.removeChildField, this))();
        });

        $('#family-form-children-' + i).slideDown();

        var i = 1;
        $('.family-form-children.control-group label').each(function() {
            $(this).text('Child ' + i++);
        });
    },
    removeChildField: function() {
        $(this).closest('.control-group').slideUp(function() {
            $(this).remove();
            var i = 1;
            $('.family-form-children.control-group label').each(function() {
                $(this).text('Child ' + i++);
            });
        });
    },
    saveEdit: function(act, callback, query_info) {
        $('#edit-family-form').submit(function(e) {
            e.preventDefault();

            var checkedRole = $('#tab2 input[name="guardian_role_radio"]:checked', this);
            var val = checkedRole.val();
            if (checkedRole.val() == 'Other' && checkedRole.next().val().length > 0) {
                val = checkedRole.next().val();
            }
            $('#tab2 input[name="guardian_role"]', this).val(val);

            var $this = $(this),
                    url = 'participants/ajax/' + act + '/',
                    type = 'POST';

            common.ajax(url, type, $this.serialize() + (query_info ? '&program_id=' + query_info.program_id + (query_info.program_session_id ? '&program_session_id=' + query_info.program_session_id : '') : ''), function(payload) {
                common.modalBox.hideModal();

                if (callback) {
                    callback(payload);
                } else {
                    $.address.autoUpdate(false);
                    if ($.address.parameterNames().length == 0) {
                        $.address.parameter('p', 1);
                    }
                    $.address.autoUpdate(true);
                    common.navigation.changeState($.address.path(), $.address.queryString(), $('#content-div'));
                    common.navigation.oldQuery = $.address.queryString();
                }
            });
        });
    },
    removeParticipant: function() {
        $('.remove-btn').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            common.modalBox.showConfirm('Are you sure you want to delete ' + $('td:nth-child(2)', $this.closest('tr')).text() + '\'s family?', {
				deleteConfirm: true,
                yesFn: function() {
                    var id = $('input[name="checked_ids[]"]', $this.closest('tr')).val(),
                            url = 'participants/ajax/delete/',
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
    }
};