/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    mage
 * @package     mage
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
/*jshint browser:true jquery:true*/
(function($){
    $.widget("mage.loader", {
        options: {
            icon: '',
            texts: {
                loaderText: $.mage.__('Please wait...'),
                imgAlt: $.mage.__('Loading...')
            },
            template: '<div class="loading-mask" data-role="loader">' +
                         '<div class="loader">'+
                            '<img {{if texts.imgAlt}}alt="${texts.imgAlt}"{{/if}} src="${icon}">'+
                            '<p>{{if texts.loaderText}}${texts.loaderText}{{/if}}</p>' +
                         '</div>' +
                      '</div>'
        },

        /**
         * Loader creation
         * @protected
         */
        _create: function() {
            this._render();
            this._bind();
        },

        /**
         * Loader initialisation
         * @private
         */
        _init: function() {
            if (this.options.showOnInit) {
                this.show();
            }
        },

        /**
         * Bind on ajax complete event
         * @protected
         */
        _bind: function() {
            this.element.on('ajaxComplete ajaxError processStop', function(e) {
                e.stopImmediatePropagation();
                $($(e.currentTarget).is(document) ? 'body' : e.currentTarget).loader('hide');
            });
            this._on({
                'show.loader': 'show',
                'hide.loader': 'hide',
                'contentUpdated.loader': '_contentUpdated'
            });
        },

        /**
         * Verify loader present after content updated
         *
         * @param event
         * @private
         */
        _contentUpdated: function(event) {
            if (!this.element.find('[data-role="loader"]').length) {
                this._render();
            }
        },

        /**
         * Show loader
         */
        show: function() {
            if (!this.element.find('[data-role="loader"]').length) {
                this._render();
            }
            this.loader.show();
        },

        /**
         * Hide loader
         */
        hide: function() {
            if (this.loader) {
                this.loader.hide();
            }
        },

        /**
         * Render loader
         * @protected
         */
        _render: function() {
            this.loader = $.tmpl(this.options.template, this.options)
                .css(this._getCssObj());

            this.element.prepend(this.loader);
        },

        /**
         * Prepare object with css properties for loader
         * @protected
         */
        _getCssObj: function() {
            var isBodyElement = this.element.is('body'),
                width = isBodyElement ? $(window).width() : this.element.outerWidth(),
                height = isBodyElement ? $(window).height() : this.element.outerHeight(),
                position = isBodyElement ? 'fixed' : 'relative';
            return {
                height: height + 'px',
                width: width + 'px',
                position: position,
                'margin-bottom': '-' + height + 'px'
            };
        },

        /**
         * Destroy loader
         */
        destroy: function() {
            this.loader.remove();
            this.element.off('ajaxComplete ajaxError processStop');
            return $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);
