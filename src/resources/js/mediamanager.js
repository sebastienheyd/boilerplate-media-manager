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

        if ($('#media-content').data('mce') === 1) {
            if ($('#media-content').data('field') !== '') {
                window.parent.postMessage({
                    action: 'insertMedia',
                    url: $(this).attr('href'),
                    name: $(this).attr('data-filename'),
                    field: $('#media-content').data('field'),
                    type: $('#media-content').data('return')
                }, '*');
            } else if (typeof parent.tinymce !== 'undefined') {
                window.parent.postMessage({
                    mceAction: 'insertMedia',
                    url: $(this).attr('href'),
                    name: $(this).attr('data-filename')
                }, '*');
            }
        } else {
            $(this).closest('.media').find('input[type="checkbox"]').iCheck('toggle');
        }
    });

    // Refresh
    $(document).on('click', '.btn-refresh', function (e) {
        e.preventDefault();
        var path = $('#media-content').data('path');
        loadPath(path, true);
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

        var checkedFiles = $('.media div.checked input[type="checkbox"]');

        clipboard.path = $('#media-list').data('path');
        clipboard.files = [];
        checkedFiles.each(function (i, e) {
            clipboard.files.push($(e).val());
        });
    });

    // Delete checked
    $(document).on('click', '.delete-checked:enabled', function (e) {
        e.preventDefault();

        bootbox.confirm(locales.deleteConfirm, function (confirm) {
            if (confirm === false) {
                return;
            }

            $('#disable').show();

            $.ajax({
                url: routes.ajaxDelete,
                type: 'post',
                data: {path: clipboard.path, files:clipboard.files},
                success: function(res) {
                    if (res.status === 'success') {
                        growl(locales.deleteSuccess, 'success');
                        $('#disable').hide();
                        $(clipboard.files).each(function (i, e) {
                            $('.media[data-filename="'+e+'"]').remove();
                        });
                        clipboard.files = [];
                    } else {
                        growl(res.message, 'error');
                    }
                }
            });
        });
    });

    // Copy checked
    $(document).on('click', '.copy-checked:enabled', function (e) {
        e.preventDefault();
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

    // Delete
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var path = $('#media-content').data('path');
        var fileName = $(this).attr('data-filename');
        var files = [];
        files.push(fileName);

        bootbox.confirm(locales.deleteConfirm, function (confirm) {
            if (confirm === false) {
                return;
            }

            $.ajax({
                url: routes.ajaxDelete,
                type: 'post',
                data: {path: path, files: files},
                success: function () {
                    growl(locales.deleteSuccess, 'success');
                    $(files).each(function (i, e) {
                        $('.media[data-filename="'+e+'"]').remove();
                    });
                }
            });
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

function loadPath(path, clearcache = false)
{
    $('#loading').css({
        position: 'absolute',
        display: 'flex',
        width: $('#media-content').width(),
        height: $('#media-content').height() === 0 ? 200 : $('#media-content').height()
    });

    $.ajax({
        url: routes.ajaxList,
        type: 'post',
        data: {
            path: path,
            mce: $('#media-content').data('mce'),
            display: $('#media-content').data('display'),
            type: $('#media-content').data('type'),
            clearcache: clearcache
        },
        success: function (html) {
            $('#media-content').html(html);
            $('#media-content').data('path', $('#media-list').data('path'));
            $('.lazy').lazy();

            // iCheck
            $('#media-list input[type="checkbox"]').iCheck({
                checkboxClass: 'icheckbox_square-blue'
            });

            // Show move button
            showMove();

            // Upload button
            uploadButton(path);

            $('#loading').css('display', 'none');
        }
    });
}

function uploadButton(path)
{
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
            growl(data.files[0].name + ' : ' + data.jqXHR.responseJSON.error, 'danger');
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

function showMove()
{
    if (clipboard.files.length === 0) {
        return;
    }

    $('#nb-files-selected').text(clipboard.files.length);
    $('#btn-paste-group').show();
    $('#media-content .box-header').addClass('blur');
    $('.btn-paste').attr('disabled', true);
    if (clipboard.path !== $('#media-list').data('path')) {
        $('.btn-paste').attr('disabled', false);
    }
}
