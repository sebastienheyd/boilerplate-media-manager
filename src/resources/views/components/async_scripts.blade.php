@once
@component('boilerplate::minify')
<script>
    loadStylesheet("{{ mix('/select-media.min.css', '/assets/vendor/boilerplate-media-manager') }}", () => {
        loadScript("{{ mix('/select-media.min.js', '/assets/vendor/boilerplate-media-manager') }}", () => {
            window.selectMediaLocales = {
                confirm: "{{ __('boilerplate-media-manager::message.deletemedia') }}"
            }
        });
    });
</script>
@endcomponent
@endonce
