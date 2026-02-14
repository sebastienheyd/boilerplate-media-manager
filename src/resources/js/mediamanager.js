/** global: bootbox */
/** global: locales */
/** global: routes */
/** global: localStorage */
/** global: parent */
/** global: clipboard */

var searchXhr = null;
var isSearching = false;
var searchDebounceTimer = null;

$(function () {

    if (localStorage.getItem('mediamanager_list_display')) {
        $('#media-content').attr('data-display', localStorage.getItem('mediamanager_list_display'));
    }

    // Sort state (multi-sort)
    var sortColumns = JSON.parse(localStorage.getItem('mediamanager_sorts') || '[{"field":"name","order":"asc"}]');

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
        if (isSearching) {
            var term = $('#search-input').val().trim();
            if (term.length >= 3) {
                searchFiles(term);
            }
        } else {
            var path = $('#media-content').data('path');
            loadPath(path, true);
        }
    });

    // Check all
    $(document).on('click', '.check-all', function () {
        $('.media input[type="checkbox"]').prop("checked", $(this).prop('checked')).trigger('change');
    });

    // Active delete selection button
    $(document).on('change', '.media input[type="checkbox"]', function (e) {
        var checkedFiles = $('.media input[type="checkbox"]:checked');
        $('.delete-checked, .cut-checked').attr('disabled', checkedFiles.length === 0);
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

            if (isSearching) {
                // Group files by path for search results
                var filesByPath = {};
                checkedFiles.each(function (i, e) {
                    var filePath = $(e).closest('.media').data('path');
                    if (!filesByPath[filePath]) {
                        filesByPath[filePath] = [];
                    }
                    filesByPath[filePath].push($(e).val());
                });

                var requests = [];
                $.each(filesByPath, function (path, files) {
                    requests.push($.ajax({
                        url: routes.ajaxDelete,
                        type: 'post',
                        data: {path: path, files: files}
                    }));
                });

                $.when.apply($, requests).done(function () {
                    growl(locales.deleteSuccess, 'success');
                    $('#disable').hide();
                    checkedFiles.each(function (i, e) {
                        $(e).closest('.media').remove();
                    });
                    $('.media input[type="checkbox"]').trigger('change');
                    if ($('#media-list .media').length === 0) {
                        loadPath($('#media-content').data('path'));
                    }
                });
            } else {
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
                            if ($('#media-list .media').length === 0) {
                                $('.btn-refresh').trigger('click');
                            }
                        } else {
                            growl(res.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Cut checked
    $(document).on('click', '.cut-checked:enabled', function (e) {
        e.preventDefault();

        var checkedFiles = $('.media input[type="checkbox"]:checked');

        clipboard.files = [];
        checkedFiles.each(function (i, el) {
            var path = isSearching
                ? ($(el).closest('.media').data('path') || $('#media-list').data('path'))
                : $('#media-list').data('path');
            clipboard.files.push({name: $(el).val(), path: path});
        });

        growl(clipboard.files.length + ' ' + locales.cutFiles, 'info');

        // Show paste button only if not in search mode
        if (!isSearching) {
            updatePasteButton();
        }
    });

    $(document).on('click', '.btn-paste', function (e) {
        e.preventDefault();

        if (isSearching) {
            return;
        }

        var currentPath = $('#media-list').data('path') || '/';

        bootbox.confirm(locales.pasteConfirm + ' "' + currentPath + '" ?', function (confirm) {
            if (confirm === false) {
                return;
            }

            // Group files by source path
            var filesByPath = {};
            clipboard.files.forEach(function (f) {
                if (!filesByPath[f.path]) {
                    filesByPath[f.path] = [];
                }
                filesByPath[f.path].push(f.name);
            });

            var requests = [];
            $.each(filesByPath, function (from, files) {
                requests.push($.ajax({
                    url: routes.ajaxPaste,
                    type: 'post',
                    data: {
                        destination: currentPath,
                        from: from,
                        files: files
                    }
                }));
            });

            var count = clipboard.files.length;

            $.when.apply($, requests).done(function () {
                clipboard.files = [];
                $('.btn-paste').hide();
                growl(locales.pasteSuccess.replace(':count', count), 'success');
                loadPath(currentPath);
            }).fail(function () {
                growl(locales.pasteSuccess.replace(':count', 0), 'error');
            });
        });
    });

    // Delete
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var btn = $(this);
        var path = isSearching ? btn.data('path') || btn.closest('.media').data('path') : $('#media-content').data('path');
        var fileName = btn.attr('data-filename');
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
                    if ($('#media-list .media').length === 0) {
                        if (isSearching) {
                            loadPath($('#media-content').data('path'));
                        } else {
                            $('.btn-refresh').trigger('click');
                        }
                    }
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

        if (isSearching) {
            var term = $('#search-input').val().trim();
            if (term.length >= 3) {
                searchFiles(term);
            }
        } else {
            loadPath($('#media-content').data('path'));
        }
    });

    // Rename
    $(document).on('click', '.btn-rename', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var btn = $(this);
        var path = isSearching ? (btn.data('path') || btn.closest('.media').data('path')) : $('#media-content').data('path');
        var fileName = btn.attr('data-filename');
        var type = btn.attr('data-type');
        var name = type === 'folder' ? fileName : btn.attr('data-name');

        bootbox.prompt({
            title: locales.renameTitle,
            value: name,
            callback: function (newName) {
                if (newName !== null && newName !== '') {
                    $.ajax({
                        url: routes.rename,
                        type: 'post',
                        data: {path: path, type: type, fileName: fileName, newName: newName},
                        success: function (result) {
                            if (result.status === 'success') {
                                growl(locales.renameSuccess, 'success');
                            } else {
                                growl(result.message, 'error');
                            }
                            if (isSearching) {
                                var term = $('#search-input').val().trim();
                                if (term.length >= 3) {
                                    searchFiles(term);
                                }
                            } else {
                                loadPath(path);
                            }
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
        $('#search-input').val('');
        $('.btn-search-clear').hide();
        loadPath(href);
    });

    // History back
    $(window).on('popstate', function () {
        loadPath(location.pathname);
    });

    // Live search
    $(document).on('input', '#search-input', function () {
        var term = $(this).val().trim();
        clearTimeout(searchDebounceTimer);

        $('.btn-search-clear').toggle(term.length > 0);

        if (term.length >= 3) {
            searchDebounceTimer = setTimeout(function () {
                searchFiles(term);
            }, 300);
        } else if (isSearching) {
            loadPath($('#media-content').data('path'));
        }
    });

    // Clear search
    $(document).on('click', '.btn-search-clear', function (e) {
        e.preventDefault();
        $('#search-input').val('');
        $(this).hide();
        if (isSearching) {
            loadPath($('#media-content').data('path'));
        }
    });

    // Back to list from search results
    $(document).on('click', '.btn-back-to-list', function (e) {
        e.preventDefault();
        $('#search-input').val('');
        $('.btn-search-clear').hide();
        loadPath($('#media-content').data('path'));
    });

    // Navigate to path from search results
    $(document).on('click', '.link-search-path', function (e) {
        e.preventDefault();
        var path = $(this).data('path');
        var href = routes.ajaxList.replace('/ajax/list', '') + path;
        history.pushState({page: href}, '', href);
        $('#search-input').val('');
        $('.btn-search-clear').hide();
        loadPath(href);
    });

    // Sort via table headers (Shift+Click for multi-sort) or dropdown (tiles mode)
    $(document).on('click', 'th.sortable, .btn-sort', function (e) {
        e.preventDefault();
        var field = $(this).data('sort');
        var idx = sortColumns.findIndex(function (s) {
            return s.field === field; });

        if (e.shiftKey) {
            if (idx !== -1) {
                sortColumns[idx].order = sortColumns[idx].order === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumns.push({field: field, order: 'asc'});
            }
        } else {
            if (idx !== -1 && sortColumns.length === 1) {
                sortColumns[0].order = sortColumns[0].order === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumns = [{field: field, order: 'asc'}];
            }
        }

        localStorage.setItem('mediamanager_sorts', JSON.stringify(sortColumns));
        updateSortIndicators(sortColumns);

        if (isSearching) {
            var term = $('#search-input').val().trim();
            if (term.length >= 3) {
                searchFiles(term);
            }
        } else {
            loadPath($('#media-content').data('path'));
        }
    });

    // Default on page load
    loadPath($('#media-content').data('path'));
});

function loadPath(path, clearcache = false)
{
    showBodySpinner();

    var sorts = localStorage.getItem('mediamanager_sorts') || '[{"field":"name","order":"asc"}]';

    $.ajax({
        url: routes.ajaxList,
        type: 'post',
        data: {
            path: path,
            display: $('#media-content').data('display'),
            type: $('#media-content').data('type'),
            clearcache: clearcache,
            sorts: sorts
        },
        success: function (html) {
            var $cardBody = $('#media-content .card-body');
            if ($cardBody.length) {
                var $response = $('<div>').html(html);
                $cardBody.html($response.find('.card-body').html());

                // Update sort dropdown in header
                var $oldSort = $('#media-content .card-header .btn-sort').closest('.btn-group.float-right');
                var $newSort = $response.find('.card-header .btn-sort').closest('.btn-group.float-right');
                if ($newSort.length) {
                    if ($oldSort.length) {
                        $oldSort.replaceWith($newSort);
                    } else {
                        $newSort.insertBefore($('#media-content .card-header .input-group'));
                    }
                } else {
                    $oldSort.remove();
                }
            } else {
                $('#media-content').html(html);
                $('#media-content').prev('#loading').hide();
            }

            $('#media-content').data('path', $('#media-list').data('path'));
            $('.media[data-url="'+$('#media-content').data('selected')+'"]').addClass('selected');
            $('.lazy').lazy();

            isSearching = false;
            $('.fileinput-button, .add-folder, .cut-checked').removeClass('disabled').show();

            // Uncheck all and disable cut/delete buttons when changing folder
            $('.media input[type="checkbox"]').prop('checked', false);
            $('.delete-checked, .cut-checked').attr('disabled', true);

            // Show/hide paste button
            updatePasteButton();

            // Upload button
            uploadButton(path);
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

function searchFiles(term)
{
    if (searchXhr) {
        searchXhr.abort();
    }

    showBodySpinner();

    searchXhr = $.ajax({
        url: routes.ajaxSearch,
        type: 'post',
        data: {
            term: term,
            type: $('#media-content').data('type'),
            display: $('#media-content').data('display'),
            sorts: localStorage.getItem('mediamanager_sorts') || '[{"field":"name","order":"asc"}]'
        },
        success: function (html) {
            $('#media-content .card-body').html(html);
            $('#media-breadcrumb').hide();
            isSearching = true;
            $('.fileinput-button, .add-folder').hide();
            $('.btn-paste').hide();
            $('.lazy').lazy();

            // Uncheck all and disable cut/delete buttons in search mode
            $('.media input[type="checkbox"]').prop('checked', false);
            $('.delete-checked, .cut-checked').attr('disabled', true);

            searchXhr = null;
        },
        error: function (xhr) {
            searchXhr = null;
        }
    });
}

function updateSortIndicators(columns)
{
    // Update sort dropdown items (tiles mode)
    $('.btn-sort').each(function () {
        var field = $(this).data('sort');
        $(this).find('.fa-sort-up, .fa-sort-down, .sort-priority').remove();
        var idx = columns.findIndex(function (s) {
            return s.field === field; });
        if (idx !== -1) {
            var icon = columns[idx].order === 'asc' ? 'up' : 'down';
            $(this).append(' <span class="fa fa-sort-' + icon + ' ml-1"></span>');
            if (columns.length > 1) {
                $(this).append('<span class="sort-priority">' + (idx + 1) + '</span>');
            }
        }
    });
}

function showBodySpinner()
{
    var $cardBody = $('#media-content .card-body');
    if ($cardBody.length) {
        $cardBody.html('<div id="loading"><div><span class="fa fa-4x fa-sync-alt fa-spin"></span></div></div>');
    }
}

function updatePasteButton()
{
    if (clipboard.files.length === 0) {
        $('.btn-paste').hide();
        return;
    }

    $('.btn-paste').show();

    var currentPath = $('#media-list').data('path');
    var allFromHere = clipboard.files.every(function (f) {
        return f.path === currentPath;
    });
    var insideCutFolder = clipboard.files.some(function (f) {
        var prefix = (f.path === '/' ? '' : f.path) + '/' + f.name;
        return currentPath.startsWith(prefix);
    });

    $('.btn-paste').attr('disabled', allFromHere || insideCutFolder);
}

