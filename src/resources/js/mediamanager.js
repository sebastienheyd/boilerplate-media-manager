/** global: bootbox */
/** global: locales */
/** global: routes */
/** global: localStorage */
/** global: parent */
/** global: clipboard */

$(function () {

    if (localStorage.getItem('mediamanager_list_display')) {
        $('#media-content').attr('data-display', localStorage.getItem('mediamanager_list_display'));
    }

    // Click on media
    $(document).on('click', '.link-media', function (e) {
        e.preventDefault();

        if ($('#media-content').data('mce') === 1 && typeof parent.tinymce !== 'undefined') {
            window.parent.postMessage({
                mceAction: 'insertMedia',
                url: $(this).attr('href'),
                name: $(this).attr('data-filename')
            }, '*');
        } else {
            $(this).closest('.media').find('input[type="checkbox"]').iCheck('toggle');
        }
    });

    // Refresh
    $(document).on('click', '.btn-refresh', function (e) {
        e.preventDefault();
        var path = $('#media-content').data('path');
        loadPath(path);
    });

    // Check all
    $(document).on('ifChanged', '.check-all', function () {
        if ($(this).iCheck('udpate')[0].checked === true) {
            $('.media input[type="checkbox"]').iCheck('check');
        } else {
            $('.media input[type="checkbox"]').iCheck('unCheck');
        }
    });

    // Active delete all button
    $(document).on('ifToggled', '.media input[type="checkbox"]', function (e) {
        var checked = false;

        $('.media input[type="checkbox"]').each(function (i, e) {
            if ($(e).parent().iCheck('update')[0].checked === true) {
                checked = true;
            }
        });

        $('.delete-checked, .copy-checked').attr('disabled', !checked);

        if (!checked) {
            $('.check-all').iCheck('unCheck');
        }
    });

    // Delete checked
    $(document).on('click', '.delete-checked', function (e) {
        e.preventDefault();
        deleteChecked();
    });

    // Copy checked
    $(document).on('click', '.copy-checked', function (e) {
        e.preventDefault();

        var checked = $('.media div.checked input[type="checkbox"]');

        if (checked.length === 0) {
            return;
        }

        clipboard.path = $('#media-list').data('path');

        checked.each(function (i, e) {
            clipboard.files.push($(e).val());
        });

        $('#nb-files-selected').text(clipboard.files.length);
        $('#btn-paste-group').show();
    });

    $(document).on('click', '.btn-paste', function (e) {
        e.preventDefault();

        $.ajax({
            url: routes.ajaxPaste,
            type: 'post',
            data: {
                destination: $('#media-list').data('path'),
                from: clipboard.path,
                files: clipboard.files
            },
            success: function (res) {
                if (res.status === 'success') {
                    loadPath($('#media-list').data('path'));
                    growl(locales.pasteSuccess, 'success');
                    clipboard.files = [];
                } else {
                    growl(res.message, 'error');
                }
            }
        });
    });

    $(document).on('click', '.btn-paste-cancel', function (e) {
        e.preventDefault();
        clipboard.files = [];
        $('#btn-paste-group').hide();
    });

    // Delete key
    $(document).on('keyup', function (e) {
        // Delete checked
        if (e.keyCode == 46) {
            deleteChecked();
        }
    });

    // Delete
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var path = $('#media-content').data('path');
        var fileName = $(this).attr('data-filename');

        bootbox.confirm(locales.deleteConfirm, function (confirm) {
            if (confirm !== false) {
                $.ajax({
                    url: routes.ajaxDelete,
                    type: 'post',
                    data: {path: path, fileName: fileName},
                    success: function () {
                        growl(locales.deleteSuccess, 'success');
                        loadPath(path);
                    }
                });
            }
        });
    });

    // New folder
    $(document).on('click', 'a.add-folder', function (e) {
        e.preventDefault();
        var path = $('#media-content').data('path');

        bootbox.prompt(locales.folderName, function (name) {
            if (name !== null && name !== '') {
                $.ajax({
                    url: routes.newFolder,
                    type: 'post',
                    data: {path: path, name: name},
                    success: function () {
                        growl(locales.folderSuccess, 'success');
                        loadPath(path);
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-toggle-display', function (e) {
        e.preventDefault();
        $('.btn-toggle-display').toggleClass('btn-secondary').toggleClass('btn-default');
        $('#media-content').data('display', $(this).data('display'));
        localStorage.setItem('mediamanager_list_display', $(this).data('display'));
        loadPath($('#media-content').data('path'));
    });

    // Rename
    $(document).on('click', '.btn-rename', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var path = $('#media-content').data('path');
        var fileName = $(this).attr('data-filename');

        bootbox.prompt({
            title: locales.renameTitle,
            value: fileName,
            callback: function (name) {
                if (name !== null && name !== '') {
                    $.ajax({
                        url: routes.rename,
                        type: 'post',
                        data: {path: path, fileName: fileName, newName: name},
                        success: function (result) {
                            if (result.status === 'success') {
                                growl(locales.renameSuccess, 'success');
                            } else {
                                growl(result.message, 'danger');
                            }
                            loadPath(path);
                        }
                    });
                }
            }
        });
    });

    // View
    $(document).on('click', '.btn-view', function (e) {
        e.preventDefault();
        e.stopPropagation();

        window.open($(this).attr('href'), '_blank');
    });

    // Load on breadcrumb click
    $(document).on('click', '#media-breadcrumb a, #media-list a.link-folder', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        history.pushState({page: href}, '', href);
        loadPath(href);
    });

    // History back
    $(window).on('popstate', function () {
        loadPath(location.pathname);
    });

    // Default on page load
    loadPath(window.location.pathname);
});

function deleteChecked()
{
    var checked = $('.media div.checked input[type="checkbox"]');

    if (checked.length === 0) {
        return;
    }

    bootbox.confirm(locales.deleteConfirm, function (confirm) {
        if (confirm !== false) {
            $('#disable').show();
            var path = $('#media-content').data('path');

            var i = 0;
            checked.each(function (i, e) {
                $.ajax({
                    url: routes.ajaxDelete,
                    type: 'post',
                    data: {path: path, fileName: $(e).val()},
                    success: function () {
                        if (++i === checked.length) {
                            growl(locales.deleteSuccess, 'success');
                            $('#disable').hide();
                            loadPath(path);
                        }
                    }
                });
            });
        }
    });
}

function loadPath(path)
{

    $.ajax({
        url: routes.ajaxList,
        type: 'post',
        data: {
            path: path,
            mce: $('#media-content').data('mce'),
            display: $('#media-content').data('display'),
            type: $('#media-content').data('type')
        },
        beforeSend: function () {
            $('#loading').css({
                position: 'absolute',
                display: 'flex',
                width: $('#media-content').width(),
                height: $('#media-content').height() === 0 ? 200 : $('#media-content').height()
            });
        },
        complete: function () {
            $('#loading').css('display', 'none');
        },
        success: function (html) {
            $('#media-content').html(html);
            $('#media-content').data('path', $('#media-list').data('path'));
            $('.lazy').lazy();

            // iCheck
            $('#media-list input[type="checkbox"]').iCheck({
                checkboxClass: 'icheckbox_square-blue'
            });

            if (clipboard.files.length > 0) {
                $('#nb-files-selected').text(clipboard.files.length);
                $('#btn-paste-group').show();
                $('#media-content .box-header').addClass('blur');
                $('.btn-paste').attr('disabled', true);
                if (clipboard.path !== $('#media-list').data('path')) {
                    $('.btn-paste').attr('disabled', false);
                }
            }

            // Upload button
            $('#fileupload').fileupload({
                dataType: 'json',
                formData: {path: path},
                url: routes.ajaxUpload,
                start: function () {
                    $('#disable,#progress').show();
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css('width', progress + '%').text(progress + '%');
                },
                fail: function (e, data) {
                    var msg = data.files[0].name + ' : ' + data.jqXHR.responseJSON.error;
                    growl(msg, 'danger');
                },
                always: function (e, data) {
                    if (data.jqXHR.responseJSON.status === 'error') {
                        growl(data.files[0].name + ' : ' + data.jqXHR.responseJSON.error, 'danger');
                    }

                    if ($('#fileupload').fileupload('active') === 1) {
                        growl(locales.uploadSuccess, 'success');
                        $('#disable').hide();
                        loadPath(path);
                    }
                }
            });
        }
    });
}
