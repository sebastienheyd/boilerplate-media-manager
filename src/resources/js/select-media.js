/** global: selectMediaLocales */
/** global: bootbox */
$(function () {
    $(document).on('change', '[data-action="setMediaFile"]', function () {
        $(this).next().addClass('d-none')
        $(this).prev().val('');

        if ($(this).val() !== '') {
            $(this).next().removeClass('d-none')
            $(this).prev().val($(this).val().replace(/.*\/(.*)\?.*$/, '$1'))
        }
    })

    $(document).on('click', '[data-action="clearMediaFile"]', function (e) {
        e.preventDefault()
        $(this).prev().val('').trigger('change');
    })

    $(document).on('click', '.btn-select-image, .btn-select-file', function () {
        $('body').css('overflow', 'hidden').append(
            '<div id="select-media-bg">' +
            '<div id="select-media-wrapper">' +
            '<div id="select-media-close">File manager<span class="fa fa-times"></span></div>' +
            '<iframe src="' + $(this).data('src') + '&selected=' + $('input[data-id=' + $(this).data('field') + ']').val() + '"></iframe>' +
            '</div>' +
            '</div>'
        );
    });

    $(document).on('click', '#select-media-close span', function () {
        $('body').css('overflow', '');
        $('#select-media-bg').remove();
    });

    $(document).on('click', '.select-image-view', function (e) {
        e.preventDefault();
        window.open($(this).closest('.select-image-wrapper').find('input').val());
    });

    $(document).on('click', '.select-image-edit', function (e) {
        e.preventDefault();
        $(this).closest('.select-image-wrapper').find('.btn-select-image').trigger('click');
    });

    $(document).on('click', '.select-image-delete', function (e) {
        e.preventDefault();
        var wrapper = $(this).closest('.select-image-wrapper');
        bootbox.confirm(selectMediaLocales.confirm, function (r) {
            if (r === false) {
                return;
            }

            wrapper.removeClass('editable');
            wrapper.find('input').val('').trigger('change');
            wrapper.find('.btn-select-image').html('<span class="fa fa-image fa-3x"></span>');
        })
    });

    $(document).on('change', 'input[data-name="hidden-image-selector-value"]', function () {
        let event = new CustomEvent('updateMedia', {
            detail: {
                name: $(this).attr('name'),
                value: $(this).val()
            }
        });

        this.dispatchEvent(event);

        window.postMessage({
            action: 'updateMedia',
            name: $(this).attr('name'),
            value: $(this).val(),
        }, '*');
    });

    window.addEventListener('message', function (e) {
        if (e.data.action === 'insertMedia') {
            $('input[data-id=' + e.data.field + ']').val(e.data.url).trigger('change');

            if (e.data.type === 'image') {
                $('button[data-field=' + e.data.field + ']').html('<img src="' + e.data.url + '" />');
                $('button[data-field=' + e.data.field + ']').parent().addClass('editable');
            }

            $('#select-media-bg').remove();
            $('body').css('overflow', 'auto');
        }
    });
});
