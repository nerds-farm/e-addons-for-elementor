jQuery(window).on('elementor/frontend/init', () => {
    class WidgetQueryCarouselHandlerClass extends elementorModules.frontend.handlers.Base {

        getDefaultSettings() {
            // e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
            return {
                selectors: {
                    container: '.e-add-posts-container',
                    containerCarousel: '.e-add-posts-container.e-add-skin-carousel',
                    containerWrapper: '.e-add-posts-wrapper',
                    items: '.e-add-post-item',
                },
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $scope: this.$element,
                $id_scope: this.getID(),
                $container: this.$element.find(selectors.container),
                $containerCarousel: this.$element.find(selectors.containerCarousel),
                $containerWrapper: this.$element.find(selectors.containerWrapper),
                $items: this.$element.find(selectors.items),
                $animationReveal: null,
                $eaddPostsSwiper: null
            };
        }

        bindEvents() {

            this.skinPrefix = this.$element.data('widget_type').split('.').pop() + '_';
            this.isCarouselEnabled = false;
            this.elementSettings = this.getElementSettings();
            //this.scope = this.elements.$scope;
            this.postId = this.elements.$scope.find('.e-add-carousel-controls').data('post-id');

            let scope = this.elements.$scope,
                    id_scope = this.elements.$id_scope,
                    widgetType = this.getWidgetType(),
                    eaddPostsSwiper = null;



            if (eaddPostsSwiper)
                eaddPostsSwiper.destroy();
            eaddPostsSwiper = new Swiper(this.elements.$containerCarousel[0], this.carouselOptions(id_scope, this.elementSettings));
            this.elements.$eaddPostsSwiper = eaddPostsSwiper;

            this.elements.$scope.data('eaddPostsSwiper', eaddPostsSwiper);
            // ---------------------------------------------
            // Funzione di callback eseguita quando avvengono le mutazioni
            /*var eAddns_MutationObserverCallback = function(mutationsList, observer) {
             for(var mutation of mutationsList) {
             if (mutation.type == 'attributes') {
             if (mutation.attributeName === 'class') {
             if (this.isCarouselEnabled) {
             eaddPostsSwiper.update();

             }
             }
             }
             }
             };
             observe_eAddns_element(scope[0], eAddns_MutationObserverCallback);*/


        }
        /*
         onInit(){
         //alert('init');
         }
         */

        onElementChange(propertyName) {

            if (this.skinPrefix + 'ratio_image' === propertyName ||
                    this.skinPrefix + 'dualslider_distribution_vertical' === propertyName ||
                    this.skinPrefix + 'dualslider_height_container' === propertyName ||
                    this.skinPrefix + 'height_container' === propertyName
                    ) {
                this.elements.$eaddPostsSwiper.update();

                //console.log(propertyName);
            }
        }

        carouselOptions(id_scope, elementSettings) {
            //@p qui vado a restituire l'oggettoo per configurare loo swiper ;-)
            var self = this;
            var eaddSwiperOptions = {
                // Optional parameters
                direction: String(elementSettings[this.skinPrefix + 'direction_slider']) || 'horizontal', //vertical
                //
                //initialSlide: slideInitNum,
                //
                reverseDirection: Boolean(elementSettings[this.skinPrefix + 'reverseDirection']),
                //
                speed: Number(elementSettings[this.skinPrefix + 'speed_slider']) || 300,
                // setWrapperSize: false, // Enabled this option and plugin will set width/height on swiper wrapper equal to total size of all slides. Mostly should be used as compatibility fallback option for browser that don't support flexbox layout well
                // virtualTranslate: false, // Enabled this option and swiper will be operated as usual except it will not move, real translate values on wrapper will not be set. Useful when you may need to create custom slide transition
                autoHeight: Boolean(elementSettings[this.skinPrefix + 'autoHeight']), //false, // Set to true and slider wrapper will adopt its height to the height of the currently active slide
                //roundLengths: Boolean( elementSettings[this.skinPrefix+'roundLengths'] ) || false, //false, // Set to true to round values of slides width and height to prevent blurry texts on usual resolution screens (if you have such)
                // nested : Boolean( elementSettings[this.skinPrefix+'nested ), //false, // Set to true on nested Swiper for correct touch events interception. Use only on nested swipers that use same direction as the parent one
                // uniqueNavElements: true, // If enabled (by default) and navigation elements' parameters passed as a string (like ".pagination") then Swiper will look for such elements through child elements first. Applies for pagination, prev/next buttons and scrollbar elements
                //
                //effect: 'cube', "slide", "fade", "cube", "coverflow" or "flip"
                effect: elementSettings[this.skinPrefix + 'effects'] || 'slide',
                cubeEffect: {
                    shadow: Boolean(elementSettings[this.skinPrefix + 'cube_shadow']),
                    slideShadows: Boolean(elementSettings[this.skinPrefix + 'slideShadows']),
                    //shadowOffset: 20,
                    //shadowScale: 0.94,
                },
                coverflowEffect: {
                    rotate: 50,
                    stretch: Number(elementSettings[this.skinPrefix + 'coverflow_stretch']) || 0,
                    depth: 100,
                    modifier: Number(elementSettings[this.skinPrefix + 'coverflow_modifier']) || 1,
                    slideShadows: Boolean(elementSettings[this.skinPrefix + 'slideShadows']),
                },
                flipEffect: {
                    rotate: 30,
                    slideShadows: Boolean(elementSettings[this.skinPrefix + 'slideShadows']),
                    limitRotation: true,
                },
                fadeEffect: {
                    crossFade: Boolean(elementSettings[this.skinPrefix + 'crossFade'])
                },
                // PARALLAX (è da implementare)
                //paralax: true,

                // LAZY-LOADING (è da implementare)
                //lazy: true,
                /*lazy {
                 loadPrevNext: false, //    Set to "true" to enable lazy loading for the closest slides images (for previous and next slide images)
                 loadPrevNextAmount: 1, //  Amount of next/prev slides to preload lazy images in. Can't be less than slidesPerView
                 loadOnTransitionStart: false, //   By default, Swiper will load lazy images after transition to this slide, so you may enable this parameter if you need it to start loading of new image in the beginning of transition
                 elementClass: 'swiper-lazy', //    CSS class name of lazy element
                 loadingClass: 'swiper-lazy-loading', //    CSS class name of lazy loading element
                 loadedClass: 'swiper-lazy-loaded', //  CSS class name of lazy loaded element
                 preloaderClass: 'swiper-lazy-preloader', //    CSS class name of lazy preloader
                 },*/

                // ZOOM (è da implementare)
                /*zoom {
                 maxRatio:  3, // Maximum image zoom multiplier
                 minRatio: 1, //    Minimal image zoom multiplier
                 toggle: true, //   Enable/disable zoom-in by slide's double tap
                 containerClass:    'swiper-zoom-container', // CSS class name of zoom container
                 zoomedSlideClass: 'swiper-slide-zoomed' // CSS class name of zoomed in container
                 },*/
                slidesPerView: Number(elementSettings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(elementSettings[this.skinPrefix + 'slidesPerGroup']) || 1, // Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1
                slidesPerColumn: Number(elementSettings[this.skinPrefix + 'slidesColumn']) || 1, // 1, // Number of slides per column, for multirow layout

                spaceBetween: Number(elementSettings[this.skinPrefix + 'spaceBetween']) || 0, // 30,
                slidesOffsetBefore: Number(elementSettings[this.skinPrefix + 'slidesOffsetBefore']) || 0, //   Add (in px) additional slide offset in the beginning of the container (before all slides)
                slidesOffsetAfter: Number(elementSettings[this.skinPrefix + 'slidesOffsetAfter']) || 0, //    Add (in px) additional slide offset in the end of the container (after all slides)

                slidesPerColumnFill: String(elementSettings[this.skinPrefix + 'slidesPerColumnFill']) || 'row', //Could be 'column' or 'row'. Defines how slides should fill rows, by column or by row

                centerInsufficientSlides: true,

                //watchOverflow: Boolean( elementSettings[this.skinPrefix+'watchOverflow'] ),
                centeredSlides: Boolean(elementSettings[this.skinPrefix + 'centeredSlides']),
                centeredSlidesBounds: Boolean(elementSettings[this.skinPrefix + 'centeredSlidesBounds']),
                //
                grabCursor: Boolean(elementSettings[this.skinPrefix + 'grabCursor']), //true,

                //------------------- Freemode
                freeMode: Boolean(elementSettings[this.skinPrefix + 'freeMode']),
                freeModeMomentum: Boolean(elementSettings[this.skinPrefix + 'freeModeMomentum']),
                freeModeMomentumRatio: Number(elementSettings[this.skinPrefix + 'freeModeMomentumRatio']) || 1,
                freeModeMomentumVelocityRatio: Number(elementSettings[this.skinPrefix + 'freeModeMomentumVelocityRatio']) || 1,
                freeModeMomentumBounce: Boolean(elementSettings[this.skinPrefix + 'freeModeMomentumBounce']),
                freeModeMomentumBounceRatio: Number(elementSettings[this.skinPrefix + 'speed']) || 1,
                freeModeMinimumVelocity: Number(elementSettings[this.skinPrefix + 'speed']) || 0.02,
                freeModeSticky: Boolean(elementSettings[this.skinPrefix + 'freeModeSticky']),

                loop: Boolean(elementSettings[this.skinPrefix + 'loop']), // true,
                //loopFillGroupWithBlank: true,

                // ----------------------------
                // HASH (è da implementare)
                /*hashNavigation: {
                 //watchState   //default: false    Set to true to enable also navigation through slides (when hashnav is enabled) by browser history or by setting directly hash on document location
                 replaceState: true,    // default: false //    Works in addition to hashnav to replace current url state with the new one instead of adding it to history
                 },*/
                // HISTORY (è da implementare)
                //history: false,
                /*history: {
                 replaceState: false, //    Works in addition to hashnav or history to replace current url state with the new one instead of adding it to history
                 key: 'slides' //   Url key for slides
                 },*/
                // CONTROLLER (è da implementare)
                //controller: false,
                /*controller: {
                 control:   [Swiper Instance]   undefined   Pass here another Swiper instance or array with Swiper instances that should be controlled by this Swiper
                 inverse: false, // Set to true and controlling will be in inverse direction
                 by: 'slide', // Can be 'slide' or 'container'. Defines a way how to control another slider: slide by slide (with respect to other slider's grid) or depending on all slides/container (depending on total slider percentage)
                 },*/


                // ----------------------------

                navigation: {
                    nextEl: '.next-' + self.getID() + '-' + this.postId, //'.swiper-button-next',
                    prevEl: '.prev-' + self.getID() + '-' + this.postId, //'.swiper-button-prev',
                    //hideOnClick: false,
                    //disabledClass: 'swiper-button-disabled', //   CSS class name added to navigation button when it becomes disabled
                    //hiddenClass: 'swiper-button-hidden', //   CSS class name added to navigation button when it becomes hidden
                },
                pagination: {
                    el: '.pagination-' + self.getID() + '-' + this.postId,
                    clickable: true,
                    //hideOnClick: true,
                    type: String(elementSettings[this.skinPrefix + 'pagination_type']) || 'bullets', //"bullets", "fraction", "progressbar" or "custom"

                    //bulletElement: 'span',
                    dynamicBullets: Boolean(elementSettings[this.skinPrefix + 'dynamicBullets']),
                    //dynamicMainBullets: 1,

                    renderBullet: function (index, className) {
                        var indexLabel = !Boolean(elementSettings[this.skinPrefix + 'dynamicBullets']) && Boolean(elementSettings[this.skinPrefix + 'bullets_numbers']) ? '<span class="swiper-pagination-bullet-title">' + (index + 1) + '</span>' : '';

                        return '<span class="' + className + '">' + indexLabel + '</span>';
                        //return '<span class="' + className + '">' + (index + 1) + '</span>';
                    },
                    renderFraction: function (currentClass, totalClass) {
                        return '<span class="' + currentClass + '"></span>' +
                                '<span class="separator">' + String(elementSettings[this.skinPrefix + 'fraction_separator']) + '</span>' +
                                '<span class="' + totalClass + '"></span>';
                    },
                    renderProgressbar: function (progressbarFillClass) {
                        return '<span class="' + progressbarFillClass + '"></span>';
                    },
                    renderCustom: function (swiper, current, total) {
                        //return current + ' of ' + total;
                        /*<ul class="e-add-scrollify-pagination nav--xusni">
                         <li><a href="#87dc4a5" class="nav__item" aria-label="1"><span class="nav__item-title">01</span></a></li>
                         <li><a href="#597993a" class="nav__item" aria-label="2"><span class="nav__item-title">02</span></a></li>
                         <li><a href="#6f9669f" class="nav__item nav__item--current" aria-label="3"><span class="nav__item-title">03</span></a></li>
                         <li><a href="#b8a16d0" class="nav__item" aria-label="4"><span class="nav__item-title">04</span></a></li>
                         </ul>*/
                        /*var custom_pagination_type = String(elementSettings[this.skinPrefix+'custom_pagination_type']);
                         var list = '<ul class="e-add-carousel-custom-pagination nav--'+custom_pagination_type+'">';
                         for(i = 1; i <= total; i++){
                         //list += current+' ';
                         var current_item = '';
                         if(i == current) current_item = ' nav__item--current';
                         list += '<li class="nav__item'+current_item+'" aria-label="'+i+'"></li>';
                         }
                         list += '</ul>';
                         return list;*/
                    }


                    // bulletClass::    'swiper-pagination-bullet', //  CSS class name of single pagination bullet
                    // bulletActiveClass:   'swiper-pagination-bullet-active', //   CSS class name of currently active pagination bullet
                    // modifierClass:   'swiper-pagination-', //    The beginning of the modifier CSS class name that will be added to pagination depending on parameters
                    // currentClass:    'swiper-pagination-current', // CSS class name of the element with currently active index in "fraction" pagination
                    // totalClass:  'swiper-pagination-total', //   CSS class name of the element with total number of "snaps" in "fraction" pagination
                    // hiddenClass:     'swiper-pagination-hidden', //  CSS class name of pagination when it becomes inactive
                    // progressbarFillClass:    'swiper-pagination-progressbar-fill', //    CSS class name of pagination progressbar fill element
                    // clickableClass:  'swiper-pagination-clickable', //   CSS class name set to pagination when it is clickable
                },
                // watchSlidesProgress:  Boolean( elementSettings[this.skinPrefix+'watchSlidesProgress ), //false, // Enable this feature to calculate each slides progress
                // watchSlidesVisibility:  Boolean( elementSettings[this.skinPrefix+'watchSlidesVisibility ), // false, // watchSlidesProgress should be enabled. Enable this option and slides that are in viewport will have additional visible class
                scrollbar: {
                    el: '.swiper-scrollbar', //    null    String with CSS selector or HTML element of the container with scrollbar.
                    hide: Boolean(elementSettings[this.skinPrefix + 'scrollbar_hide']), // boolean  true    Hide scrollbar automatically after user interaction
                    draggable: Boolean(elementSettings[this.skinPrefix + 'scrollbar_draggable']), //true, // Set to true to enable make scrollbar draggable that allows you to control slider position
                    snapOnRelease: true, // Set to true to snap slider position to slides when you release scrollbar
                    //dragSize: 'auto', //     string/number   Size of scrollbar draggable element in px
                },
                mousewheel: Boolean(elementSettings[this.skinPrefix + 'mousewheelControl']), // true,
                /*mousewheel: {
                 forceToAxis: false //   Set to true to force mousewheel swipes to axis. So in horizontal mode mousewheel will work only with horizontal mousewheel scrolling, and only with vertical scrolling in vertical mode.
                 releaseOnEdges: false // Set to true and swiper will release mousewheel event and allow page scrolling when swiper is on edge positions (in the beginning or in the end)
                 invert: false // Set to true to invert sliding direction
                 sensitivity: 1, // Multiplier of mousewheel data, allows to tweak mouse wheel sensitivity
                 eventsTarged: 'container' // String with CSS selector or HTML element of the container accepting mousewheel events. By default it is swiper-container
                 },*/
                //keyboard: Boolean( elementSettings[this.skinPrefix+'keyboardControl ),

                keyboard: {
                    enabled: Boolean(elementSettings[this.skinPrefix + 'keyboardControl']),
                    //onlyInViewport: false,
                },
                //     },
                //updateOnWindowResize: true,
                //setWrapperSize: true,
                on: {
                    init: function () {
                        this.isCarouselEnabled = true;
                        jQuery('body').attr('data-carousel-' + self.getID(), this.realIndex);

                    },
                    slideChange: function (e) {
                        jQuery('body').attr('data-carousel-' + self.getID(), this.realIndex);
                    },
                }
            };

            if (this.skinPrefix == 'dualslider_') {
                eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, {thumbs: {
                        swiper: this.elements.$scope.data('thumbscarousel'),
                    }});
            }
            if (elementSettings[this.skinPrefix + 'useAutoplay']) {

                //default
                eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, {autoplay: true});

                //
                var autoplayDelay = Number(elementSettings[this.skinPrefix + 'autoplay']);
                //console.log( autoplayDelay );
                if (!autoplayDelay) {
                    //delay: Number(elementSettings[this.skinPrefix+'autoplay) || 3000, // 2500, // Delay between transitions (in ms). If this parameter is not specified, auto play will be disabled
                    autoplayDelay = 3000;
                } else {
                    autoplayDelay = Number(elementSettings[this.skinPrefix + 'autoplay']);
                }
                eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, {autoplay: {delay: autoplayDelay, disableOnInteraction: Boolean(elementSettings[this.skinPrefix + 'autoplayDisableOnInteraction']), stopOnLastSlide: Boolean(elementSettings[this.skinPrefix + 'autoplayStopOnLast'])}});

            }
            //@p il responsive per i valori:
            var elementorBreakpoints = elementorFrontend.config.breakpoints;
            var responsivePoints = eaddSwiperOptions.breakpoints = {};
            responsivePoints[elementorBreakpoints.lg] = {
                slidesPerView: Number(elementSettings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(elementSettings[this.skinPrefix + 'slidesPerGroup']) || 1,
                spaceBetween: Number(elementSettings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesPerColumn: Number(elementSettings[this.skinPrefix + 'slidesColumn']) || 1,
                spaceBetween: Number(elementSettings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesOffsetBefore: Number(elementSettings[this.skinPrefix + 'slidesOffsetBefore']) || 0,
                slidesOffsetAfter: Number(elementSettings[this.skinPrefix + 'slidesOffsetAfter']) || 0,
            };
            responsivePoints[elementorBreakpoints.md] = {
                slidesPerView: Number(elementSettings[this.skinPrefix + 'slidesPerView_tablet']) || Number(elementSettings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(elementSettings[this.skinPrefix + 'slidesPerGroup_tablet']) || Number(elementSettings[this.skinPrefix + 'slidesPerGroup']) || 1,
                spaceBetween: Number(elementSettings[this.skinPrefix + 'spaceBetween_tablet']) || Number(elementSettings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesPerColumn: Number(elementSettings[this.skinPrefix + 'slidesColumn_tablet']) || Number(elementSettings[this.skinPrefix + 'slidesColumn']) || 1,
                spaceBetween: Number(elementSettings[this.skinPrefix + 'spaceBetween_tablet']) || 0,
                slidesOffsetBefore: Number(elementSettings[this.skinPrefix + 'slidesOffsetBefore_tablet']) || 0,
                slidesOffsetAfter: Number(elementSettings[this.skinPrefix + 'slidesOffsetAfter_tablet']) || 0,
            };
            responsivePoints[elementorBreakpoints.xs] = {
                slidesPerView: Number(elementSettings[this.skinPrefix + 'slidesPerView_mobile']) || Number(elementSettings[this.skinPrefix + 'slidesPerView_tablet']) || Number(elementSettings[this.skinPrefix + 'slidesPerView']) || 'auto',
                slidesPerGroup: Number(elementSettings[this.skinPrefix + 'slidesPerGroup_mobile']) || Number(elementSettings[this.skinPrefix + 'slidesPerGroup_tablet']) || Number(elementSettings[this.skinPrefix + 'slidesPerGroup']) || 1,
                spaceBetween: Number(elementSettings[this.skinPrefix + 'spaceBetween_mobile']) || Number(elementSettings[this.skinPrefix + 'spaceBetween_tablet']) || Number(elementSettings[this.skinPrefix + 'spaceBetween']) || 0,
                slidesPerColumn: Number(elementSettings[this.skinPrefix + 'slidesColumn_mobile']) || Number(elementSettings[this.skinPrefix + 'slidesColumn_tablet']) || Number(elementSettings[this.skinPrefix + 'slidesColumn']) || 1,
                spaceBetween: Number(elementSettings[this.skinPrefix + 'spaceBetween_mobile']) || 0,
                slidesOffsetBefore: Number(elementSettings[this.skinPrefix + 'slidesOffsetBefore_mobile']) || 0,
                slidesOffsetAfter: Number(elementSettings[this.skinPrefix + 'slidesOffsetAfter_mobile']) || 0,
            };
            eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, responsivePoints);

            return eaddSwiperOptions;
        }
    }

    const Widget_EADD_Query_carousel_Handler = ($element) => {

        elementorFrontend.elementsHandler.addHandler(WidgetQueryCarouselHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.carousel', Widget_EADD_Query_carousel_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.carousel', Widget_EADD_Query_carousel_Handler);
});