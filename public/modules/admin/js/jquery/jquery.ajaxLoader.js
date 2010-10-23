/*jslint white: true, browser: true */ /*global jQuery: true */
/**
 *  @title: Ajax Loader
 *  @version: 2.0
 *  @author: Andreas Lagerkvist
 *  @date: 2008-09-25
 *  @url: http://andreaslagerkvist.com/jquery/ajax-loader/
 *  @license: http://creativecommons.org/licenses/by/3.0/
 *  @copyright: 2008 Andreas Lagerkvist (andreaslagerkvist.com)
 *  @does: Use this plug-in when you want to inform your visitors that a certain part of your page is currently loading.
 *  The plug-in adds a faded 'loading-div' on top of the selected element(s). The div is of course completely stylable.
 *  @howto:
 *  jQuery('#contact').ajaxLoader(); would add the overlay on top of the #contact-element.
 *
 *  When you want to remove the loader simply run jQuery('#contact').ajaxLoader('disable');
 *  Or jQuery('#contact').ajaxLoader('showClass', <className>, <timeout>); for automatic disable after <timeout> ms with overwrite the current class.
 */

/**
 * Expanded plug-in. Gave a plug-in line with the standard of writing plugins for jQuery.
 * @author yugeon
 * @modifed: 2010-10-22
 */
(function ($) {

    var config = {
        className:    'jquery-ajax-loader',
        fadeDuration: 'slow',
        opacity:      0.6
    };

    var methods = {
        init: function (params) {
            var options = $.extend({}, config, params);
            return this.each(function () {
                var $this = $(this);
                var offset = $this.offset();
                $this.data('elem', $('<div>').css({
                    position:    'absolute',
                    left:        offset.left + 'px',
                    top:         offset.top + 'px',
                    width:       $this.outerWidth() + 'px',
                    height:      $this.outerHeight() + 'px'
                }).appendTo('body').hide()
                );
                $this.data('options', options);
                methods.show.apply(this);
            });
        },
        show: function () {
            var $this = $(this);
            var options = $this.data('options');
            $this.data('elem').addClass(options.className).fadeTo(options.fadeDuration, options.opacity);
        },
        showClass: function (className, timeout) {
            timeout = timeout || 2000;
            return this.each(function () {
                var $this = $(this);
                var options = $this.data('options');
                var $el = $this.data('elem');
                $el.removeClass().addClass(className).show();
                setTimeout(function () {
                        $el.fadeOut(options.fadeDurations).remove();
                    }, timeout);
            });
        },
        disable: function () {
            return this.each(function () {
                var $this = $(this);
                var options = $this.data('options');
                $this.data('elem').fadeOut(options.fadeDuration);
            });
        }
    };

    $.fn.ajaxLoader = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on jQuery.ajaxLoader');
            return this;
        }
    };
})(jQuery);
