/**
 * https://jsfiddle.net/KyleMit/X9tgY/
 */
(function ($, window) {

    $.fn.contextMenu = function (settings) {

        return this.each(function () {

            // Open context menu
            $(this).on("contextmenu", function (e) {

                // return native menu if pressing control
                if (e.ctrlKey) {
                    return;
                }

                //open menu
                var $menu = $(settings.menuSelector)
                    .data("invokedOn", $(this))
                    .show()
                    .css({
                        position: "absolute",
                        left: getMenuPosition(e.pageX, 'width', 'scrollLeft'),
                        top: getMenuPosition(e.pageY, 'height', 'scrollTop')
                    })
                    .off('click')
                    .on('click', 'a', function (e) {
                        $menu.hide();
                        if (typeof settings.onSelect === 'function') {
                            settings.onSelect.call(this, $menu.data("invokedOn"), $(e.target), e);
                        }
                    });

                if (typeof settings.onShow === 'function') {
                    settings.onShow.call(this, $menu, $menu.data("invokedOn"));
                }

                return false;
            });

            //make sure menu closes on any click
            $(window).click(function () {
                $(settings.menuSelector).hide();
            });
        });

        function getMenuPosition(mouse, direction, scrollDir)
        {
            var win = $(window)[direction](),
                scroll = $(window)[scrollDir](),
                menu = $(settings.menuSelector)[direction](),
                position = mouse;

            // opening menu would pass the side of the page
            if (mouse + menu > win && menu < mouse) {
                position -= menu;
            }

            return position;
        }

    };
})(jQuery, window);