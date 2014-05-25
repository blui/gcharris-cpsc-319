EJS.config({
    //cache: false
});
$.ajaxSetup({
    cache: false
});

$(document).ready(function() {
    $(window)
    .scroll(function() {
        if ($('.navbar').offset().top > 10) {
            $('.navbar-inner').addClass('sticky');
        } else {
            $('.navbar-inner').removeClass('sticky');
        }
    })
    .on('unload.cleanup', function() {
        $('.navbar .nav > li.active:first').toggleClass('active normal');
    });

    $('#sub-nav').css('opacity', 0).fadeTo(250, 1);
    $('#main-content').css('opacity', 0);

    $('.navbar .nav a').click(function(e) {
        if ($(this).attr('href') == '#' || $(this).attr('href').indexOf('settings') >= 0) {
            return;
        }

        $(window).off('unload.cleanup');

        e.preventDefault();
        $(this).closest('ul').find('li.active:first').toggleClass('active normal');
        $(this).parent().addClass('active');
        var linkLocation = $(this).attr('href');

        $('.navbar-inner').removeClass('sticky');
        $('#sub-nav').delay(125).fadeTo(200, 0);
        $('#main-content').fadeTo(250, 0, function() {
            window.location = linkLocation;
        });
    });

    $.address.change(function(event) {
        if ($.address.path() != common.navigation.oldPath || $.address.queryString() == '') {
            if (frp.permission_level != '0' && frp.module == 'staff' && $.address.path() == '/') {
                $.address.autoUpdate(false);
                $.address.path('/volunteers');
                $.address.autoUpdate(true);
            }

            common.navigation.changeState($.address.path(), $.address.queryString() + (common.navigation.firstRun ? '&firstrun=1' : ''));
            common.navigation.oldPath = $.address.path();
        }

        if (!common.navigation.firstRun && $.address.queryString() != common.navigation.oldQuery && $.address.queryString() != '') {
            common.navigation.changeState($.address.path(), $.address.queryString(), $('#content-div'));
            common.navigation.oldQuery = $.address.queryString();
        }

        common.navigation.firstRun = false;
    });

    common.modalBox.init();

    $('#logout-button').click(function(e) {
        e.preventDefault();

        $('#user-menu-button')
        .popover({
            trigger: 'manual',
            title: 'Logging out...',
            html: true,
            content: '<div id="loading-bar" class="progress progress-striped active"><div class="bar" style="width:100%;"></div></div>',
            placement: 'bottom'
        })
        .popover('show');

        $.ajax({
            url: 'auth/logout',
            type: 'POST',
            dataType: "json",
            complete: function(e) {
                $('#user-menu-button').popover('destroy');
            },
            success: function(response) {
                location.reload();
            }
        });
    });

    // .ajax-activity-iconshould be a hidden element with a loading spinner
    var ajaxIcon = $('#ajax-loading-bar'),
        timer = null,
        counter = 0;
    $(document).ajaxSend(function(e, jqXHR) {
        // get the REAL window event that triggered this mess, if possible
        var event = window.event || e;

        // if the event's target is `a.btn` animate the button
        var target = $(event.target && event.target.activeElement ? event.target.activeElement : event.srcElement);

        if (target.is('a.btn,:input')) {
            if (target.is(':input') && !target.is('button[type="submit"]')) {
                target = $('button[type="submit"]:first', target.closest('form'));
            }

            target.addClass('btn-striped').data('loading-text', '<i class="icon-refresh ' + (target.hasClass('btn-primary') ? 'icon-white' : '') + '"></i> ' + target.text()).button('loading');

            jqXHR.always(function() {
                target.button('reset').removeClass('btn-striped');
            });
        }

        // if we aren't already showing (or waiting to show) the global ajaxIcon queue it up now.
        if (++counter === 1) {
            clearTimeout(timer);
            // Only show the icon if loading takes longer than 400ms.
            timer = setTimeout(function() {
                ajaxIcon.removeClass('hidden');
            }, 400);
        }
    }).ajaxComplete(function() {
        // hide our global spinner after all queued AJAX calls are complete.
        if (--counter === 0) {
            clearTimeout(timer);
            ajaxIcon.addClass('hidden');
        }
    });

    $('body').on('focus', 'input', function(){
        $(this).attr('autocomplete', 'off');
    });
});

var common = {
    utility: {
        makeSortable: function(context) {
            $('thead th[id]', context)
            .click(function() {
                var sort = $(this).attr('id').split('-')[1];
                var isDefault = $(this).hasClass('default');
                var sortby = $.address.parameter('sort') == sort && $.address.parameter('dir') == 1 && !isDefault ? '' : sort;
                var dir = $.address.parameter('sort') == sort ? ($.address.parameter('dir') == 0 ? 1 : (isDefault ? 0 : '')) : 0;

                $.address.autoUpdate(false);
                $.address.parameter('sort', sortby);
                $.address.parameter('dir', dir);

                if ($.address.parameterNames().length == 0) {
                    $.address.parameter('p', 1);
                }

                $.address.update();
                $.address.autoUpdate(true);
            })
            .each(function() {
                if ($.address.parameter('sort') !== undefined) {
                    var $this = $(this);
                    $this.find('i').remove();
                    $this.append($.address.parameter('sort') == $this.attr('id').split('-')[1] ? ' <i class="icon-chevron-' + ($.address.parameter('dir') == 1 ? 'down' : 'up') + '"></i>' : '');
                }
            });
        },
        checkall: function(context) {
            var $checkall = $('.check-all', context);
            var $checkboxes = $checkall.closest('table').find('td input[type=checkbox]');

            $checkall.change(function(){
                $checkboxes
                .prop('checked', this.checked)
                .each(function(){
                    $(this).closest('tr').toggleClass('checked', this.checked);
                })
            });

            $checkboxes.change(function(){
                var checked = $checkboxes.filter(':checked').length;
                $checkall.prop('checked', $checkboxes.length == checked);
                $(this).closest('tr').toggleClass('checked', this.checked);
            });

            $checkall.closest('table').on('click', '> tbody > tr', function(e) {
                if (!$(e.target).is('.btn,input,i[class^="icon-"],i[class*=" icon-"]')) {
                    var checkbox = $('> td:first input[type="checkbox"]', this);

                    if (checkbox.length) {
                        var ischecked = checkbox.is(':checked');
                        checkbox.prop('checked', !ischecked).trigger('change');
                        checkbox.closest('tr').toggleClass('checked', !ischecked);
                    }
                }
            });
        },
        validation: function(context) {
            $('input,select,textarea', context).not('[type=submit]').jqBootstrapValidation({
                preventSubmit: true,
                submitSuccess: function($form, event) {
                    event.preventDefault();
                    
                    if ($('input,select,textarea', $form).jqBootstrapValidation('hasErrors')) {
                        event.stopImmediatePropagation();
                    }
                },
                submitError: function($form, event, errors) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                },
                filter: function() {
                    return !$(this).is('[name$="ids\[\]"],[multiple="multiple"],[type="hidden"]');
                }
            });
        },
        commaSeparateNumber: function(val) {
            while (/(\d+)(\d{3})/.test(val.toString())) {
                val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
            }

            return val;
        }
    },
    content: {
        lastXhr: false,
        load: function(controller, data, div, callback, animate) {
            var url = frp.module + '/ajax' + controller,
                type = 'get',
                data = data || {},
                content = div || $('#main-content'),
                animate = animate === undefined ? true : animate;

            if (animate && content.css('opacity') == 1) {
                content.fadeTo(200, 0);
            }

            if (common.content.lastXhr) {
                common.content.lastXhr.abort();
            }

            common.content.lastXhr = common.ajax(url, type, data, function(data) {
                common.content.lastXhr = false;
                common.content.renderEJS(common.content.ejsURL(controller, div), data, div, callback);

                if (animate) {
                    content.fadeTo(200, 1);
                }
            });
        },
        ejsURL: function(controller, div) {
            return frp.base + 'ejs/' + frp.module + controller + (controller == '/' ? 'index' : '') + (div ? '.partial' : '') + '.ejs';
        },
        renderEJS: function(url, data, div, callback) {
            var $content = div || $('#main-content'),
                html = new EJS({
                    url: url
                }).render({
                    'payload': data
                });

            $content.html(html);

            common.utility.validation($content);

            if (frp.permission_level == '0') {
                $('#generate-csv-btn').click(function(e) {
                    e.preventDefault();
                    var query = $.address.queryString();
                    if (!query) {
                        query += "csv=1";
                    } else {
                        query += "&csv=1";
                    }

                    window.location = frp.base + '/' + frp.module + '/ajax' + $.address.path() + '?' + query;
                });
            } else {
                $('#generate-csv-btn').hide();
            }

            // Load the module specific javascript
            if (!callback) {
                window[frp.module]['init']();
            }

            $('input.datepicker')
            .datepicker({
                format: 'mm/dd/yyyy',
                endDate: 'today',
                autoclose: true,
                forceParse: false
            })
            .mask('99/99/9999').attr('autocomplete', 'off');

            $('.pagination a').click(function(e) {
                e.preventDefault();
                $(this).parent().addClass('active');

                var page = $(this).attr('href').split('p=')[1];
                if (page) {
                    $.address.parameter('p', page);
                }
            });

            $('#pagination-count').select2({minimumResultsForSearch: -1}).change(function() {
                $.address.parameter('count', $(this).val());
            });

            if (callback) {
                callback();
            }
        }
    },
    ajax: function(url, type, data, success, fail) {
        var defaultFailure = function(data) {
                alert(data.message);
            },
            failure = fail || defaultFailure,
            obj = {
                'success': success,
                'failure': failure
            },
            handleReturn = function(obj) {
                return function(data, textStatus) {
                    if (data.ok === 1) {
                        obj.success(data.payload, textStatus);
                    } else {
                        obj.failure(data.payload, textStatus);
                    }
                };
            };

        return $.ajax({
            url: url,
            type: type,
            data: data,
            dataType: "json",
            success: handleReturn(obj)
        });
    },
    email: {
        box: function(title, recipients, checked_recipients, ajax_url, field, showFilters) {
            showFilters = showFilters || 0
            common.modalBox.showModal('common/email.ejs', {
                recipients: recipients,
                checked_recipients: checked_recipients,
                title: title,
                field: field,
                showFilters: showFilters,
                filters: $.address.queryString()
            });

            $("#upload_field").html5_upload({
                url: 'partners/ajax/upload',
                sendBoundary: window.FormData || $.browser.mozilla,
                onStart: function(event, total) {
                    $("#progress_report_bar_container").show();
                    return true;

                },
                setProgress: function(val) {
                    $("#progress_report_bar").css('width', Math.ceil(val * 100) + "%");
                },
                onFinishOne: function(event, response, name, number, total) {
                    $("#progress_report_bar_container").hide();
                    //  alert(response);
                    var resp = jQuery.parseJSON(response);

                    $("#uploaded").append("<div <div style=\"padding-bottom:5px\"><button aria-hidden=\"true\" class=\"btn remove_attachment\" type=\"button\"><i class=\"icon-remove\"></i></button><input type=\"hidden\" name=\"uploads[]\" value=\"" + resp.payload.id + "\"> " + resp.payload.name + " (" + resp.payload.size + ")</div>");
                    $('.remove_attachment').click(function() {
                        $(this).parent().remove();
                    });
                },
                onError: function(event, name, error) {
                    alert('error while uploading file ' + name);
                    $("#progress_report_bar_container").hide();
                }
            });

            $('#wysiwyg').wysihtml5({
                toolbar: {
                    code: function(locale, options) {
                        return '<li><a class="btn" data-wysihtml5-command="insertHTML" data-wysihtml5-command-value="{name}" href="javascript:;">Recipient Name</a></li>';
                    }
                }
            }
            );

            $('#email-form').submit(function(e) {
                e.preventDefault();

                var $this = $(this),
                    type = 'POST';

                common.ajax(ajax_url, type, $this.serialize(), function() {
                    common.modalBox.hideModal();
                });
            });
        }
    },
    navigation: {
        oldPath: '',
        oldQuery: '',
        firstRun: true,
        changeState: function(val, data, div) {
            var isFound = this.setActiveNavItem(val);

            if (!isFound)
                val = '/';

            common.content.load(val, data, div);
        },
        setActiveNavItem: function(val) {
            $('#sub-nav li').removeClass('active');
            var navItems = $('#sub-nav').find('a[href="#' + val + '"]');

            if (navItems.length > 0) {
                navItems.parent().addClass('active');
            } else {
                $('#sub-nav').find('a[href="#/"]').parent().addClass('active');
            }

            return navItems.length > 0;
        }

    },
    modalBox: {
        init: function(modal) {
            var $modal = modal || $('#modal');
            $modal.on('shown', function() {
                $(this).find('button.btn,textarea,input:not(:hidden),select').first().focus();

                $('input.datepicker', $modal)
                .datepicker({
                    format: 'mm/dd/yyyy',
                    endDate: 'today',
                    autoclose: true,
                    forceParse: false
                })
                .on('show', function() {
                    $(this).data('datepicker').picker.css('z-index', $modal.css('z-index') + 10);
                })
                .mask('99/99/9999')
                .attr('autocomplete', 'off');
            });
        },
        showModal: function(url, data, modal) {
            var $modal = modal || $('#modal'),
                html = new EJS({
                    url: frp.base + 'ejs/' + url
                }).render(data);

            $modal
            .css('width', 'auto')
            .html(html);

            var minWidth = 0;

            $modal
            .show()
            .find('.form-horizontal .control-label')
            .css('width', 'auto')
            .each(function() {
                var width = $(this).outerWidth();

                if (width > minWidth) {
                    minWidth = width;
                }
            })
            .css('width', minWidth + 20);

            $modal
            .find('.form-horizontal .controls')
            .css('margin-left', minWidth + 40);

            $modal.modal({width: $modal.width()});

            common.modalBox.validation($modal);

            return $modal;
        },
        hideModal: function() {
            var $modal = $('#modal');

            $modal.modal('hide');
        },
        showError: function(msg, afterFn) {
            return common.modalBox.message(msg, "Error", false, afterFn || function() {}, function() {});
        },
        showAlert: function(msg, title, afterFn) {
            return common.modalBox.message(msg, title, false, afterFn || function() {}, function() {});
        },
        showConfirm: function(msg, options) {
            options = options || {};

            msg += options.deleteConfirm ? '<br><br><span class="label label-warning">Warning</span> <small>Deleting this may delete other data associated to it. Please make sure that you would like to delete this before typing &quot;delete&quot; and clicking OK.</small><br><input class="input-block-level" type="text" placeholder="Type in &quot;delete&quot; to confirm..." pattern="delete" required style="margin:5px 0 -10px 0;">' : '';

            if (options.deleteConfirm) {
                options.form = true;
            }

            return common.modalBox.message(msg, options.title || "Please Confirm", true, options.yesFn || function() {}, options.noFn || function() {}, options.form === undefined ? false : options.form, options.modal === undefined ? false : options.modal);
        },
        message: function(msg, title, confirm, yesFn, noFn, form, modal) {
            var $modal = modal || $('#modal');

            $modal
            .html((form ? '<form class="form-horizontal modal-form">' : '') + '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="modalLabel">' + (title || "Error") + '</h3></div><div class="modal-body">' + msg + '</div><div class="modal-footer">' + (confirm ? '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> Cancel</button>' : '') + '<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> OK</button></div>' + (form ? '</form>' : ''))
            .modal('show')
            .find('.modal-footer button').click(function(e) {
                e.preventDefault();

                if ($(this).hasClass('btn-primary') || !confirm) {
                    if (form) {
                        $modal.find('> form').submit();
                        return;
                    }

                    yesFn($modal);
                } else {
                    noFn($modal);
                }

                $modal.modal('hide');
            });

            if (form) {
                common.modalBox.validation($modal);

                $modal.find('> form').submit(function(e) {
                    e.preventDefault();

                    yesFn($modal);
                    $modal.modal('hide');
                });                
            }

            return $modal;
        },
        validation: function(modal) {
            var $modal = modal || $('#modal');

            if ($('.nav-tabs', $modal).length) {
                $('input,select,textarea', $modal).not('[type=submit]').bind('invalid', function(){
                    var $this = $(this);
                    var tabpane = $this.closest('.tab-pane');
                    var id = tabpane.attr('id');
                    tabpane.closest('.modal-body').find('.nav-tabs a[href="#' + id + '"]').click();
                    $this.focus();
                });
            }

            common.utility.validation($modal);
        }
    }
};
