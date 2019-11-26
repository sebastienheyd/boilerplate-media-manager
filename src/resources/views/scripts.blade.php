@include('boilerplate::load.icheck')
@push('js')
    <script src="{{ mix('/contextmenu.min.js', '/assets/vendor/boilerplate-media-manager') }}"></script>
    <script src="{{ mix('/vendor/blueimp-file-upload/jquery.fileupload.min.js', '/assets/vendor/boilerplate-media-manager') }}"></script>
    <script src="{{ mix('/vendor/jquery-lazy/jquery.lazy.plugins.js', '/assets/vendor/boilerplate-media-manager') }}"></script>
    <script>
        $(function () {

            if (localStorage.getItem('mediamanager_list_display')) {
                $('#media-content').attr('data-display', localStorage.getItem('mediamanager_list_display'));
            }

            // Click on media
            $(document).on('click', '.link-media', function (e) {
                e.preventDefault();

                if ($('#media-content').data('mce') == 1 && typeof parent.tinymce !== 'undefined') {

                    window.parent.postMessage({
                        mceAction: 'insertMedia',
                        url: $(this).attr('href'),
                        name: $(this).attr('data-filename')
                    }, '*');
                }
            });

            // Refresh
            $(document).on('click', '.btn-refresh', function (e) {
                e.preventDefault();
                var path = $('#media-content').data('path');
                loadPath(path);
            });

            // Check all
            $(document).on('ifChanged', '.check-all', function (e) {
                if ($(this).iCheck('udpate')[0].checked == true) {
                    $('.media input[type="checkbox"]').iCheck('check');
                } else {
                    $('.media input[type="checkbox"]').iCheck('unCheck');
                }
            });

            // Active delete all button
            $(document).on('ifToggled', '.media input[type="checkbox"]', function (e) {
                var checked = false;

                $('.media input[type="checkbox"]').each(function (i, e) {
                    if ($(e).parent().iCheck('update')[0].checked == true) {
                        checked = true;
                    }
                });

                $('.delete-checked').attr('disabled', !checked);

                if (!checked) {
                    $('.check-all').iCheck('unCheck');
                }
            });

            // Delete checked
            $(document).on('click', '.delete-checked', function (e) {
                e.preventDefault();
                deleteChecked();
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

                bootbox.confirm("{{ __('boilerplate-media-manager::message.delete.confirm') }}", function (confirm) {
                    if (confirm !== false) {
                        $.ajax({
                            url: "{{ route('mediamanager.ajax.delete') }}",
                            type: 'post',
                            data: {path: path, fileName: fileName},
                            success: function () {
                                growl("{{ __('boilerplate-media-manager::message.delete.success') }}", 'success');
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

                bootbox.prompt("{{ __('boilerplate-media-manager::message.folder.name') }}", function (name) {
                    if (name !== null && name !== '') {
                        $.ajax({
                            url: "{{ route('mediamanager.ajax.new-folder') }}",
                            type: 'post',
                            data: {path: path, name: name},
                            success: function () {
                                growl("{{ __('boilerplate-media-manager::message.folder.success') }}", 'success');
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
                    title: "{{ __('boilerplate-media-manager::message.rename.title') }}",
                    value: fileName,
                    callback: function (name) {
                        if (name !== null && name !== '') {
                            $.ajax({
                                url: "{{ route('mediamanager.ajax.rename') }}",
                                type: 'post',
                                data: {path: path, fileName: fileName, newName: name},
                                success: function (result) {
                                    if (result.status === 'success') {
                                        growl("{{ __('boilerplate-media-manager::message.rename.success') }}", 'success');
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

            // Visualiser
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
            $(window).on('popstate', function (e) {
                loadPath(location.pathname);
            });

            // Default on page load
            loadPath(window.location.pathname);
        });

        function deleteChecked() {
            var checked = $('.media div.checked input[type="checkbox"]');

            if (checked.length == 0) {
                return;
            }

            bootbox.confirm("{{ __('boilerplate-media-manager::message.delete.confirm') }}", function (confirm) {
                if (confirm !== false) {
                    $('#disable').show();
                    var path = $('#media-content').data('path');

                    var i = 0;
                    checked.each(function (i, e) {
                        $.ajax({
                            url: "{{ route('mediamanager.ajax.delete') }}",
                            type: 'post',
                            data: {path: path, fileName: $(e).val()},
                            success: function () {
                                if (++i == checked.length) {
                                    growl("{{ __('boilerplate-media-manager::message.delete.success') }}", 'success');
                                    $('#disable').hide();
                                    loadPath(path);
                                }
                            }
                        });
                    });
                }
            });
        }

        function loadPath(path) {

            $.ajax({
                url: "{{ route('mediamanager.ajax.list') }}",
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

                    // Context menu
                    $("#media-list tr.media").contextMenu({
                        menuSelector: "#contextMenu",
                        onShow: function (menu, el) {
                            $(menu).find('a').attr('data-filename', el.data('filename'));

                            $(menu).find('a.btn-view').hide();
                            if (el.find('a.btn-view').length > 0) {
                                $(menu).find('a.btn-view').attr('href', el.find('a.btn-view').attr('href')).show();
                            }
                        }
                    });

                    // Upload button
                    $('#fileupload').fileupload({
                        dataType: 'json',
                        formData: {path: path},
                        url: "{{ route('mediamanager.ajax.upload') }}",
                        start: function () {
                            $('#disable,#progress').show();
                        },
                        progressall: function (e, data) {
                            var progress = parseInt(data.loaded / data.total * 100, 10);
                            $('#progress .progress-bar').css('width', progress + '%').text(progress + '%');
                        },
                        fail: function (e, data) {
                            var msg = data.files[0].name + ' : ' + data.jqXHR.responseJSON.errors.file[0];
                            growl(msg, 'danger');
                        },
                        always: function (e, data) {
                            if ($('#fileupload').fileupload('active') == 1) {
                                growl("{{ __('boilerplate-media-manager::message.upload.success') }}", 'success');
                                $('#disable').hide();
                                loadPath(path);
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
