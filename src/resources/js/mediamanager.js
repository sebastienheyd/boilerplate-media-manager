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
            $(this).closest('.media').find('input[type="checkbox"]').trigger('click');
        }
    });

    // Refresh
    $(document).on('click', '.btn-refresh', function (e) {
        e.preventDefault();
        var path = $('#media-content').data('path');
        loadPath(path, true);
    });

    // Check all
    $(document).on('click', '.check-all', function () {
        $('.media input[type="checkbox"]').prop("checked", $(this).prop('checked')).trigger('change');
    });

    // Active delete selection button
    $(document).on('change', '.media input[type="checkbox"]', function (e) {
        var checkedFiles = $('.media input[type="checkbox"]:checked');
        $('.delete-checked, .copy-checked').attr('disabled', !checkedFiles.length > 0);
    });

    // Delete checked
    $(document).on('click', '.delete-checked:enabled', function (e) {
        e.preventDefault();

        bootbox.confirm(locales.deleteConfirm, function (confirm) {
            if (confirm === false) {
                return;
            }

            $('#disable').show();

            var checkedFiles = $('.media input[type="checkbox"]:checked');

            var files = [];
            checkedFiles.each(function (i, e) {
                files.push($(e).val());
            });

            $.ajax({
                url: routes.ajaxDelete,
                type: 'post',
                data: {path: $('#media-list').data('path'), files:files},
                success: function (res) {
                    if (res.status === 'success') {
                        growl(locales.deleteSuccess, 'success');
                        $('#disable').hide();
                        $(files).each(function (i, e) {
                            $('.media[data-filename="'+e+'"]').remove();
                        });
                        $('.media input[type="checkbox"]').trigger('change');
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

        var checkedFiles = $('.media input[type="checkbox"]:checked');

        clipboard.path = $('#media-list').data('path');
        clipboard.files = [];
        checkedFiles.each(function (i, e) {
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
                    $('.media input[type="checkbox"]').trigger('change');
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
    loadPath($('#media-content').data('path'));
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
            display: $('#media-content').data('display'),
            type: $('#media-content').data('type'),
            clearcache: clearcache
        },
        success: function (html) {
            $('#media-content').html(html);
            $('#media-content').data('path', $('#media-list').data('path'));
            $('.media[data-url="'+$('#media-content').data('selected')+'"]').addClass('selected');
            $('.lazy').lazy();

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
    $('#media-content .card-header').addClass('blur');
    $('.btn-paste').attr('disabled', true);

    var enabled = true;
    clipboard.files.forEach(function (file) {
        if ($('#media-list').data('path').startsWith((clipboard.path === '/' ? '' : clipboard.path) + '/' + file) ||
            $('#media-list').data('path') === clipboard.path) {
            enabled = false;
        }
    });

    if (enabled) {
        $('.btn-paste').attr('disabled', false);
    }
}
