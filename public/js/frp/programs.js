var programs = {
    init: function() {
        this.editProgram();
        this.removeProgram();
        this.addProgram();
        this.activateSession();
    },
    setProgramSessionID: function(id, bypass) {
        if (id > 0 || bypass) {
            if ($('#end-session-btn').css('opacity') == 0) {
                $('#end-session-btn').css('visibility', 'visible').fadeTo(250, 1);
            }

            var $this = $('.program-session-content').closest('tr').prev();
            $('input[name="program_session_id"]', $this).val(id);
        }
    },
    getAvailableIDs: function() {
        var $this = $('.program-session-content').closest('tr').prev();

        var ids = {};
        ids.program_id = $('input[name="id"]', $this).val();
        var program_session_id = $('input[name="program_session_id"]', $this).val();
        var previous = $('input[name="previous"]', $this).val();

        if (program_session_id > 0) {
            ids.program_session_id = program_session_id;
        }

        if (previous > 0) {
            ids.previous = previous;
        }

        return ids;
    },
    activateSession: function() {
        $('.start-session-btn').click(function(e) {
            e.preventDefault();
            var $this = $(this);

            //Get row id value
            var id = $('input[name="id"]', $this.closest('td')).val();
            var program_session_id = $('input[name="program_session_id"]', $this.closest('td')).val();
            var previous = $('input[name="previous"]', $this.closest('td')).val();
            var data = {get: 'session', program_id: id};
            if (program_session_id) {
                data.program_session_id = program_session_id;
            }
            if (previous) {
                data.previous = previous;
            }

            if (!previous) {
                $this.addClass('btn-primary').html('<i class="icon-play icon-white"></i> Session started');
            }

            var activeSession = $this.closest('tr').next();

            common.content.load('/session', data, activeSession.find('td:first'), function() {
                $('.program-session-content').css('position', 'relative');
                $("#shadow").fadeIn();
                activeSession.show().find('.program-session-content').slideDown();

                programs.startSession();
            }, false);
        });
    },
    startSession: function() {
        $('#child-search')
        .select2({
            multiple: true,
            minimumInputLength: 3,
            ajax: {
                url: frp.base + '/participants/ajax/search',
                dataType: 'json',
                quietMillis: 100,
                data: function(term, page) {
                    return {
                        q: term,
                        page: page
                    };
                },
                results: function(data, page) {
                    var data = data.payload;
                    var more = (page * data.pagination.itemCountPerPage) < data.pagination.totalItemCount;

                    if (page == 1) {
                        data.items.unshift({family_id: 0});
                    }

                    return {
                        results: data.items,
                        more: more
                    };
                }
            },
            id: function(entry) {
                return {id: entry.family_id};
            },
            formatResult: function(entry, container, query) {
                if (entry.family_id == 0) {
                    return '<i class="icon-plus"></i> Add \'<strong>' + query.term + '</strong>\'';
                } else {
                    return '<strong>' + entry.guardian_name + '</strong> <small style="font-style:italic;">' + (entry.phone_number ? ('(' + entry.phone_number.substr(0, 3) + ') ' + entry.phone_number.substr(3, 3) + '-' + entry.phone_number.substr(6)) : 'No phone number') + '</small><br><small>' + (entry.children_name || '<i>No children</i></small>');
                }
            },
            formatSelection: function(entry) {
                return entry.guardian_name;
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function(m) {
                return m;
            }
        })
        .on('selected', function(object, id) {
            var name = $('#child-search').select2('container').find('input.select2-input').val();
            $('#child-search').select2('val', '');
            $('#child-search').select2('container').find('input.select2-input').blur();
            id = object.choice ? object.choice.family_id : id;

            var ids = programs.getAvailableIDs();
            ids.name = name;

            if (id == 0) {
                common.ajax('participants/ajax/', 'get', {
                    id: 0
                }, function(result) {
                    participants.showAddEdit('create', result, function(payload){
                        programs.setProgramSessionID(payload.program_session_id || 0);
                        $('#session-nav a[href="#tab-checkin"]').trigger('click');
                    }, ids);
                });
            } else {
                var data = {
                    get: 'family',
                    id: id,
                    program_id: ids.program_id
                };

                if (ids.program_session_id) {
                    data.program_session_id = ids.program_session_id;
                }

                if (ids.previous) {
                    data.previous = ids.previous;
                }

                common.ajax('/programs/ajax/session', 'get', data, function(result) {
                    common.modalBox.showModal('programs/checkin.ejs', result);
                });
            }
        });

        $('#shadow').click(function() {
            $('.program-session-content').slideUp(function() {
                var ids = programs.getAvailableIDs();
                var btn = $('.start-session-btn', $(this).closest('tr').prev());

                $(this).closest('tr').hide().find('td:first').html('');

                if (!ids.program_session_id) {
                    btn.removeClass('btn-primary').html('<i class="icon-play"></i> Start a session');
                }

                if (ids.previous) {
                    common.navigation.changeState($.address.path(), $.address.queryString(), $('#content-div'));
                    common.navigation.oldQuery = $.address.queryString();
                }
            });

            $(this).fadeOut();
        });

        $('#initialize-registration-btn').click(function(e) {
            e.preventDefault();

            var ids = programs.getAvailableIDs();

            window.location.href = '/programs/register?id=' + ids.program_id;
        });

        $('#session-nav a').click(function() {
            var ids = programs.getAvailableIDs();
            var tabId = $(this).attr('href');
            var type = tabId.split('-')[1];
            var data = {
                'get': type == 'info' ? 'session' : type,
                'program_id': ids.program_id
            };

            if (ids.program_session_id) {
                data.program_session_id = ids.program_session_id;
            }

            if (ids.previous) {
                data.previous = ids.previous;
            }

            $('.tab-pane', $(this).closest('.tabbable')).html('Loading...');

            common.ajax(frp.base + '/programs/ajax/session', 'get', data, function(payload) {
                $(tabId).html(new EJS({url: frp.base + '/ejs/programs/session.' + type + '.ejs'}).render({payload: payload}));
                common.utility.validation($(tabId));
            });
        });

        $('#end-session-btn').click(function(e) {
            e.preventDefault();

            common.modalBox.showConfirm('Are you sure you want to end this session?', {
                yesFn: function() {
                    var ids = programs.getAvailableIDs();
                    var data = {
                        edit: 'session',
                        field: 'running',
                        value: 0,
                        program_id: ids.program_id
                    };

                    if (ids.program_session_id) {
                        data.program_session_id = ids.program_session_id;
                    }

                    if (ids.previous) {
                        data.previous = ids.previous;
                    }

                    common.ajax(frp.base + '/programs/ajax/session', 'post', data, function(payload) {
                        programs.setProgramSessionID(0, true);
                        $('#shadow').triggerHandler('click');
                    });
                }
            });
        });
    },
    editProgram: function() {
        $('.edit-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="id"]', $(this).closest('tr')).val();

            //Load row from the DB
            common.ajax('programs/ajax/', 'get', {
                id: id
            }, function(result) {
                common.modalBox.showModal('programs/edit.ejs', result);
                programs.saveEdit();
            });
        });
        $('.edit-resources-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="id"]', $(this).closest('tr')).val();

            //Load row from the DB
            common.ajax('programs/ajax/session', 'get', {
                get: 'resources',
                program_id: id
            }, function(result) {
                common.modalBox.showModal('programs/resources.ejs', {payload: result, program_id: id});
            });
        });
        $('.edit-referrals-btn').click(function(e) {
            e.preventDefault();

            //Get row id value
            var id = $('input[name="id"]', $(this).closest('tr')).val();

            //Load row from the DB
            common.ajax('programs/ajax/session', 'get', {
                get: 'referrals',
                program_id: id
            }, function(result) {
                common.modalBox.showModal('programs/referrals.ejs', {payload: result, program_id: id});
            });
        });
    },
    saveEdit: function() {
        $('#edit-program-form').submit(function(e) {
            e.preventDefault();

            var $this = $(this),
                    url = 'programs/ajax/edit/',
                    type = 'POST';

            common.ajax(url, type, $this.serialize(), function() {
                common.modalBox.hideModal();
                $.address.update();
            });
        });
    },
    removeProgram: function() {
        $('.remove-btn').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            common.modalBox.showConfirm('Are you sure you want to delete \'' + $('td:nth-child(2)', $this.closest('tr')).text() + '\'?', {
               deleteConfirm: true,
               yesFn: function() {
                    var id = $('input[name="id"]', $this.closest('tr')).val(),
                            url = ($.address.path() == '/sessions' ? 'programs/ajax/session' : 'programs/ajax/delete/'),
                            type = 'POST';

                    var data = {
                        id: id
                    };

                    if ($.address.path() == '/sessions') {
                        data = {
                            program_id: id,
                            program_session_id: $('input[name="program_session_id"]', $this.closest('tr')).val(),
                            delete: 'session',
                            previous: 1
                        };
                    }

                    common.ajax(url, type, data, function() {
                        $.address.update();
                    });
                }
            });
        });
    },
    addProgram: function() {
        $('#add-new-program-form').submit(function(e) {
            e.preventDefault();
            var url = 'programs/ajax/create/',
                    type = 'POST';

            common.ajax(url, type, $(this).serialize(), function() {
                $.address.update();
            });
        });
    }
};