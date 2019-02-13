/*! jQuery & Zepto Lazy v1.7.10 - http://jquery.eisbehr.de/lazy - MIT&GPL-2.0 license - Copyright 2012-2018 Daniel 'Eisbehr' Kern */
!function(t,e){"use strict";function r(r,a,i,u,l){function f(){L=t.devicePixelRatio>1,i=c(i),a.delay>=0&&setTimeout(function(){s(!0)},a.delay),(a.delay<0||a.combined)&&(u.e=v(a.throttle,function(t){"resize"===t.type&&(w=B=-1),s(t.all)}),u.a=function(t){t=c(t),i.push.apply(i,t)},u.g=function(){return i=n(i).filter(function(){return!n(this).data(a.loadedName)})},u.f=function(t){for(var e=0;e<t.length;e++){var r=i.filter(function(){return this===t[e]});r.length&&s(!1,r)}},s(),n(a.appendScroll).on("scroll."+l+" resize."+l,u.e))}function c(t){var i=a.defaultImage,o=a.placeholder,u=a.imageBase,l=a.srcsetAttribute,f=a.loaderAttribute,c=a._f||{};t=n(t).filter(function(){var t=n(this),r=m(this);return!t.data(a.handledName)&&(t.attr(a.attribute)||t.attr(l)||t.attr(f)||c[r]!==e)}).data("plugin_"+a.name,r);for(var s=0,d=t.length;s<d;s++){var A=n(t[s]),g=m(t[s]),h=A.attr(a.imageBaseAttribute)||u;g===N&&h&&A.attr(l)&&A.attr(l,b(A.attr(l),h)),c[g]===e||A.attr(f)||A.attr(f,c[g]),g===N&&i&&!A.attr(E)?A.attr(E,i):g===N||!o||A.css(O)&&"none"!==A.css(O)||A.css(O,"url('"+o+"')")}return t}function s(t,e){if(!i.length)return void(a.autoDestroy&&r.destroy());for(var o=e||i,u=!1,l=a.imageBase||"",f=a.srcsetAttribute,c=a.handledName,s=0;s<o.length;s++)if(t||e||A(o[s])){var g=n(o[s]),h=m(o[s]),b=g.attr(a.attribute),v=g.attr(a.imageBaseAttribute)||l,p=g.attr(a.loaderAttribute);g.data(c)||a.visibleOnly&&!g.is(":visible")||!((b||g.attr(f))&&(h===N&&(v+b!==g.attr(E)||g.attr(f)!==g.attr(F))||h!==N&&v+b!==g.css(O))||p)||(u=!0,g.data(c,!0),d(g,h,v,p))}u&&(i=n(i).filter(function(){return!n(this).data(c)}))}function d(t,e,r,i){++z;var o=function(){y("onError",t),p(),o=n.noop};y("beforeLoad",t);var u=a.attribute,l=a.srcsetAttribute,f=a.sizesAttribute,c=a.retinaAttribute,s=a.removeAttribute,d=a.loadedName,A=t.attr(c);if(i){var g=function(){s&&t.removeAttr(a.loaderAttribute),t.data(d,!0),y(T,t),setTimeout(p,1),g=n.noop};t.off(I).one(I,o).one(D,g),y(i,t,function(e){e?(t.off(D),g()):(t.off(I),o())})||t.trigger(I)}else{var h=n(new Image);h.one(I,o).one(D,function(){t.hide(),e===N?t.attr(C,h.attr(C)).attr(F,h.attr(F)).attr(E,h.attr(E)):t.css(O,"url('"+h.attr(E)+"')"),t[a.effect](a.effectTime),s&&(t.removeAttr(u+" "+l+" "+c+" "+a.imageBaseAttribute),f!==C&&t.removeAttr(f)),t.data(d,!0),y(T,t),h.remove(),p()});var m=(L&&A?A:t.attr(u))||"";h.attr(C,t.attr(f)).attr(F,t.attr(l)).attr(E,m?r+m:null),h.complete&&h.trigger(D)}}function A(t){var e=t.getBoundingClientRect(),r=a.scrollDirection,n=a.threshold,i=h()+n>e.top&&-n<e.bottom,o=g()+n>e.left&&-n<e.right;return"vertical"===r?i:"horizontal"===r?o:i&&o}function g(){return w>=0?w:w=n(t).width()}function h(){return B>=0?B:B=n(t).height()}function m(t){return t.tagName.toLowerCase()}function b(t,e){if(e){var r=t.split(",");t="";for(var a=0,n=r.length;a<n;a++)t+=e+r[a].trim()+(a!==n-1?",":"")}return t}function v(t,e){var n,i=0;return function(o,u){function l(){i=+new Date,e.call(r,o)}var f=+new Date-i;n&&clearTimeout(n),f>t||!a.enableThrottle||u?l():n=setTimeout(l,t-f)}}function p(){--z,i.length||z||y("onFinishedAll")}function y(t,e,n){return!!(t=a[t])&&(t.apply(r,[].slice.call(arguments,1)),!0)}var z=0,w=-1,B=-1,L=!1,T="afterLoad",D="load",I="error",N="img",E="src",F="srcset",C="sizes",O="background-image";"event"===a.bind||o?f():n(t).on(D+"."+l,f)}function a(a,o){var u=this,l=n.extend({},u.config,o),f={},c=l.name+"-"+ ++i;return u.config=function(t,r){return r===e?l[t]:(l[t]=r,u)},u.addItems=function(t){return f.a&&f.a("string"===n.type(t)?n(t):t),u},u.getItems=function(){return f.g?f.g():{}},u.update=function(t){return f.e&&f.e({},!t),u},u.force=function(t){return f.f&&f.f("string"===n.type(t)?n(t):t),u},u.loadAll=function(){return f.e&&f.e({all:!0},!0),u},u.destroy=function(){return n(l.appendScroll).off("."+c,f.e),n(t).off("."+c),f={},e},r(u,l,a,f,c),l.chainable?a:u}var n=t.jQuery||t.Zepto,i=0,o=!1;n.fn.Lazy=n.fn.lazy=function(t){return new a(this,t)},n.Lazy=n.lazy=function(t,r,i){if(n.isFunction(r)&&(i=r,r=[]),n.isFunction(i)){t=n.isArray(t)?t:[t],r=n.isArray(r)?r:[r];for(var o=a.prototype.config,u=o._f||(o._f={}),l=0,f=t.length;l<f;l++)(o[t[l]]===e||n.isFunction(o[t[l]]))&&(o[t[l]]=i);for(var c=0,s=r.length;c<s;c++)u[r[c]]=t[0]}},a.prototype.config={name:"lazy",chainable:!0,autoDestroy:!0,bind:"load",threshold:500,visibleOnly:!1,appendScroll:t,scrollDirection:"both",imageBase:null,defaultImage:"data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==",placeholder:null,delay:-1,combined:!1,attribute:"data-src",srcsetAttribute:"data-srcset",sizesAttribute:"data-sizes",retinaAttribute:"data-retina",loaderAttribute:"data-loader",imageBaseAttribute:"data-imagebase",removeAttribute:!0,handledName:"handled",loadedName:"loaded",effect:"show",effectTime:0,enableThrottle:!0,throttle:250,beforeLoad:e,afterLoad:e,onError:e,onFinishedAll:e},n(t).on("load",function(){o=!0})}(window);
/*!
 * jQuery & Zepto Lazy - AJAX Plugin - v1.4
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    // load data by ajax request and pass them to elements inner html, like:
    // <div data-loader="ajax" data-src="url.html" data-method="post" data-type="html"></div>
    $.lazy('ajax', function(element, response) {
        ajaxRequest(this, element, response, element.attr('data-method'));
    });

    // load data by ajax get request and pass them to elements inner html, like:
    // <div data-loader="get" data-src="url.html" data-type="html"></div>
    $.lazy('get', function(element, response) {
        ajaxRequest(this, element, response, 'GET');
    });

    // load data by ajax post request and pass them to elements inner html, like:
    // <div data-loader="post" data-src="url.html" data-type="html"></div>
    $.lazy('post', function(element, response) {
        ajaxRequest(this, element, response, 'POST');
    });

    // load data by ajax put request and pass them to elements inner html, like:
    // <div data-loader="put" data-src="url.html" data-type="html"></div>
    $.lazy('put', function(element, response) {
        ajaxRequest(this, element, response, 'PUT');
    });

    /**
     * execute ajax request and handle response
     * @param {object} instance
     * @param {jQuery|object} element
     * @param {function} response
     * @param {string} [method]
     */
    function ajaxRequest(instance, element, response, method) {
        method = method ? method.toUpperCase() : 'GET';

        var data;
        if ((method === 'POST' || method === 'PUT') && instance.config('ajaxCreateData')) {
            data = instance.config('ajaxCreateData').apply(instance, [element]);
        }

        $.ajax({
            url: element.attr('data-src'),
            type: method === 'POST' || method === 'PUT' ? method : 'GET',
            data: data,
            dataType: element.attr('data-type') || 'html',

            /**
             * success callback
             * @access private
             * @param {*} content
             * @return {void}
             */
            success: function(content) {
                // set responded data to element's inner html
                element.html(content);

                // use response function for Zepto
                response(true);

                // remove attributes
                if (instance.config('removeAttribute')) {
                    element.removeAttr('data-src data-method data-type');
                }
            },

            /**
             * error callback
             * @access private
             * @return {void}
             */
            error: function() {
                // pass error state to lazy
                // use response function for Zepto
                response(false);
            }
        });
    }
})(window.jQuery || window.Zepto);

/*!
 * jQuery & Zepto Lazy - AV Plugin - v1.4
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    // loads audio and video tags including tracks by two ways, like:
    // <audio>
    //   <data-src src="audio.ogg" type="video/ogg"></data-src>
    //   <data-src src="audio.mp3" type="video/mp3"></data-src>
    // </audio>
    // <video data-poster="poster.jpg">
    //   <data-src src="video.ogv" type="video/ogv"></data-src>
    //   <data-src src="video.webm" type="video/webm"></data-src>
    //   <data-src src="video.mp4" type="video/mp4"></data-src>
    //   <data-track kind="captions" src="captions.vtt" srclang="en"></data-track>
    //   <data-track kind="descriptions" src="descriptions.vtt" srclang="en"></data-track>
    //   <data-track kind="subtitles" src="subtitles.vtt" srclang="de"></data-track>
    // </video>
    //
    // or:
    // <audio data-src="audio.ogg|video/ogg,video.mp3|video/mp3"></video>
    // <video data-poster="poster.jpg" data-src="video.ogv|video/ogv,video.webm|video/webm,video.mp4|video/mp4">
    //   <data-track kind="captions" src="captions.vtt" srclang="en"></data-track>
    //   <data-track kind="descriptions" src="descriptions.vtt" srclang="en"></data-track>
    //   <data-track kind="subtitles" src="subtitles.vtt" srclang="de"></data-track>
    // </video>
    $.lazy(['av', 'audio', 'video'], ['audio', 'video'], function(element, response) {
        var elementTagName = element[0].tagName.toLowerCase();

        if (elementTagName === 'audio' || elementTagName === 'video') {
            var srcAttr = 'data-src',
                sources = element.find(srcAttr),
                tracks = element.find('data-track'),
                sourcesInError = 0,

            // create on error callback for sources
            onError = function() {
                if (++sourcesInError === sources.length) {
                    response(false);
                }
            },

            // create callback to handle a source or track entry
            handleSource = function() {
                var source = $(this),
                    type = source[0].tagName.toLowerCase(),
                    attributes = source.prop('attributes'),
                    target = $(type === srcAttr ? '<source>' : '<track>');

                if (type === srcAttr) {
                    target.one('error', onError);
                }

                $.each(attributes, function(index, attribute) {
                    target.attr(attribute.name, attribute.value);
                });

                source.replaceWith(target);
            };

            // create event for successfull load
            element.one('loadedmetadata', function() {
                response(true);
            })

            // remove default callbacks to ignore loading poster image
            .off('load error')

            // load poster image
            .attr('poster', element.attr('data-poster'));

            // load by child tags
            if (sources.length) {
                sources.each(handleSource);
            }

            // load by attribute
            else if (element.attr(srcAttr)) {
                // split for every entry by comma
                $.each(element.attr(srcAttr).split(','), function(index, value) {
                    // split again for file and file type
                    var parts = value.split('|');

                    // create a source entry
                    element.append($('<source>')
                           .one('error', onError)
                           .attr({src: parts[0].trim(), type: parts[1].trim()}));
                });

                // remove now obsolete attribute
                if (this.config('removeAttribute')) {
                    element.removeAttr(srcAttr);
                }
            }

            else {
                // pass error state
                // use response function for Zepto
                response(false);
            }

            // load optional tracks
            if (tracks.length) {
                tracks.each(handleSource);
            }
        }

        else {
            // pass error state
            // use response function for Zepto
            response(false);
        }
    });
})(window.jQuery || window.Zepto);

/*!
 * jQuery & Zepto Lazy - iFrame Plugin - v1.5
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    // load iframe content, like:
    // <iframe data-src="iframe.html"></iframe>
    //
    // enable content error check with:
    // <iframe data-src="iframe.html" data-error-detect="true"></iframe>
    $.lazy(['frame', 'iframe'], 'iframe', function(element, response) {
        var instance = this;

        if (element[0].tagName.toLowerCase() === 'iframe') {
            var srcAttr = 'data-src',
                errorDetectAttr = 'data-error-detect',
                errorDetect = element.attr(errorDetectAttr);

            // default way, just replace the 'src' attribute
            if (errorDetect !== 'true' && errorDetect !== '1') {
                // set iframe source
                element.attr('src', element.attr(srcAttr));

                // remove attributes
                if (instance.config('removeAttribute')) {
                    element.removeAttr(srcAttr + ' ' + errorDetectAttr);
                }
            }

            // extended way, even check if the document is available
            else {
                $.ajax({
                    url: element.attr(srcAttr),
                    dataType: 'html',
                    crossDomain: true,
                    xhrFields: {withCredentials: true},

                    /**
                     * success callback
                     * @access private
                     * @param {*} content
                     * @return {void}
                     */
                    success: function(content) {
                        // set responded data to element's inner html
                        element.html(content)

                        // change iframe src
                        .attr('src', element.attr(srcAttr));

                        // remove attributes
                        if (instance.config('removeAttribute')) {
                            element.removeAttr(srcAttr + ' ' + errorDetectAttr);
                        }
                    },

                    /**
                     * error callback
                     * @access private
                     * @return {void}
                     */
                    error: function() {
                        // pass error state to lazy
                        // use response function for Zepto
                        response(false);
                    }
                });
            }
        }

        else {
            // pass error state to lazy
            // use response function for Zepto
            response(false);
        }
    });
})(window.jQuery || window.Zepto);

/*!
 * jQuery & Zepto Lazy - NOOP Plugin - v1.2
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    // will do nothing, used to disable elements or for development
    // use like:
    // <div data-loader="noop"></div>

    // does not do anything, just a 'no-operation' helper ;)
    $.lazy('noop', function() {});

    // does nothing, but response a successfull loading
    $.lazy('noop-success', function(element, response) {
        // use response function for Zepto
        response(true);
    });

    // does nothing, but response a failed loading
    $.lazy('noop-error', function(element, response) {
        // use response function for Zepto
        response(false);
    });
})(window.jQuery || window.Zepto);

/*!
 * jQuery & Zepto Lazy - Picture Plugin - v1.3
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    var srcAttr = 'data-src',
        srcsetAttr = 'data-srcset',
        mediaAttr = 'data-media',
        sizesAttr = 'data-sizes',
        typeAttr = 'data-type';

    // loads picture elements like:
    // <picture>
    //   <data-src srcset="1x.jpg 1x, 2x.jpg 2x, 3x.jpg 3x" media="(min-width: 600px)" type="image/jpeg"></data-src>
    //   <data-src srcset="1x.jpg 1x, 2x.jpg 2x, 3x.jpg 3x" media="(min-width: 400px)" type="image/jpeg"></data-src>
    //   <data-img src="default.jpg" >
    // </picture>
    //
    // or:
    // <picture data-src="default.jpg">
    //   <data-src srcset="1x.jpg 1x, 2x.jpg 2x, 3x.jpg 3x" media="(min-width: 600px)" type="image/jpeg"></data-src>
    //   <data-src srcset="1x.jpg 1x, 2x.jpg 2x, 3x.jpg 3x" media="(min-width: 400px)" type="image/jpeg"></data-src>
    // </picture>
    //
    // or just with attributes in one line:
    // <picture data-src="default.jpg" data-srcset="1x.jpg 1x, 2x.jpg 2x, 3x.jpg 3x" data-media="(min-width: 600px)" data-sizes="" data-type="image/jpeg" />
    $.lazy(['pic', 'picture'], ['picture'], function(element, response) {
        var elementTagName = element[0].tagName.toLowerCase();

        if (elementTagName === 'picture') {
            var sources = element.find(srcAttr),
                image = element.find('data-img'),
                imageBase = this.config('imageBase') || '';

            // handle as child elements
            if (sources.length) {
                sources.each(function() {
                    renameElementTag($(this), 'source', imageBase);
                });

                // create img tag from child
                if (image.length === 1) {
                    image = renameElementTag(image, 'img', imageBase);

                    // bind event callbacks to new image tag
                    image.on('load', function() {
                        response(true);
                    }).on('error', function() {
                        response(false);
                    });

                    image.attr('src', image.attr(srcAttr));

                    if (this.config('removeAttribute')) {
                        image.removeAttr(srcAttr);
                    }
                }

                // create img tag from attribute
                else if (element.attr(srcAttr)) {
                    // create image tag
                    createImageObject(element, imageBase + element.attr(srcAttr), response);

                    if (this.config('removeAttribute')) {
                        element.removeAttr(srcAttr);
                    }
                }

                // pass error state
                else {
                    // use response function for Zepto
                    response(false);
                }
            }

            // handle as attributes
            else if( element.attr(srcsetAttr) ) {
                // create source elements before img tag
                $('<source>').attr({
                    media: element.attr(mediaAttr),
                    sizes: element.attr(sizesAttr),
                    type: element.attr(typeAttr),
                    srcset: getCorrectedSrcSet(element.attr(srcsetAttr), imageBase)
                })
                .appendTo(element);

                // create image tag
                createImageObject(element, imageBase + element.attr(srcAttr), response);

                // remove attributes from parent picture element
                if (this.config('removeAttribute')) {
                    element.removeAttr(srcAttr + ' ' + srcsetAttr + ' ' + mediaAttr + ' ' + sizesAttr + ' ' + typeAttr);
                }
            }

            // pass error state
            else {
                // use response function for Zepto
                response(false);
            }
        }

        else {
            // pass error state
            // use response function for Zepto
            response(false);
        }
    });

    /**
     * create a new child element and copy attributes
     * @param {jQuery|object} element
     * @param {string} toType
     * @param {string} imageBase
     * @return {jQuery|object}
     */
    function renameElementTag(element, toType, imageBase) {
        var attributes = element.prop('attributes'),
            target = $('<' + toType + '>');

        $.each(attributes, function(index, attribute) {
            // build srcset with image base
            if (attribute.name === 'srcset' || attribute.name === srcAttr) {
                attribute.value = getCorrectedSrcSet(attribute.value, imageBase);
            }

            target.attr(attribute.name, attribute.value);
        });

        element.replaceWith(target);
        return target;
    }

    /**
     * create a new image element inside parent element
     * @param {jQuery|object} parent
     * @param {string} src
     * @param {function} response
     * @return void
     */
    function createImageObject(parent, src, response) {
        // create image tag
        var imageObj = $('<img>')

        // create image tag an bind callbacks for correct response
        .one('load', function() {
            response(true);
        })
        .one('error', function() {
            response(false);
        })

        // set into picture element
        .appendTo(parent)

        // set src attribute at last to prevent early kick-in
        .attr('src', src);

        // call after load even on cached image
        imageObj.complete && imageObj.load(); // jshint ignore : line
    }

    /**
     * prepend image base to all srcset entries
     * @param {string} srcset
     * @param {string} imageBase
     * @returns {string}
     */
    function getCorrectedSrcSet(srcset, imageBase) {
        if (imageBase) {
            // trim, remove unnecessary spaces and split entries
            var entries = srcset.split(',');
            srcset = '';

            for (var i = 0, l = entries.length; i < l; i++) {
                srcset += imageBase + entries[i].trim() + (i !== l - 1 ? ',' : '');
            }
        }

        return srcset;
    }
})(window.jQuery || window.Zepto);

/*!
 * jQuery & Zepto Lazy - Script Plugin - v1.2
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    // loads javascript files for script tags, like:
    // <script data-src="file.js" type="text/javascript"></script>
    $.lazy(['js', 'javascript', 'script'], 'script', function(element, response) {
        if (element[0].tagName.toLowerCase() === 'script') {
            element.attr('src', element.attr('data-src'));

            // remove attribute
            if (this.config('removeAttribute')) {
                element.removeAttr('data-src');
            }
        }
        else {
            // use response function for Zepto
            response(false);
        }
    });
})(window.jQuery || window.Zepto);

/*!
 * jQuery & Zepto Lazy - Vimeo Plugin - v1.1
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    // load vimeo video iframe, like:
    // <iframe data-loader="vimeo" data-src="176894130" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    $.lazy('vimeo', function(element, response) {
        if (element[0].tagName.toLowerCase() === 'iframe') {
            // pass source to iframe
            element.attr('src', 'https://player.vimeo.com/video/' + element.attr('data-src'));

            // remove attribute
            if (this.config('removeAttribute')) {
                element.removeAttr('data-src');
            }
        }

        else {
            // pass error state
            // use response function for Zepto
            response(false);
        }
    });
})(window.jQuery || window.Zepto);

/*!
 * jQuery & Zepto Lazy - YouTube Plugin - v1.5
 * http://jquery.eisbehr.de/lazy/
 *
 * Copyright 2012 - 2018, Daniel 'Eisbehr' Kern
 *
 * Dual licensed under the MIT and GPL-2.0 licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
;(function($) {
    // load youtube video iframe, like:
    // <iframe data-loader="yt" data-src="1AYGnw6MwFM" data-nocookie="1" width="560" height="315" frameborder="0" allowfullscreen></iframe>
    $.lazy(['yt', 'youtube'], function(element, response) {
        if (element[0].tagName.toLowerCase() === 'iframe') {
            // pass source to iframe
            var noCookie = /1|true/.test(element.attr('data-nocookie'));
            element.attr('src', 'https://www.youtube' + (noCookie ? '-nocookie' : '') + '.com/embed/' + element.attr('data-src') + '?rel=0&amp;showinfo=0');

            // remove attribute
            if (this.config('removeAttribute')) {
                element.removeAttr('data-src');
            }
        }

        else {
            // pass error state
            response(false);
        }
    });
})(window.jQuery || window.Zepto);