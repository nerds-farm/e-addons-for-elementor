jQuery(window).on('elementor/frontend/init', () => {

class WidgetQueryDualSliderHandlerClass extends elementorModules.frontend.handlers.Base {
    
    getDefaultSettings() {
        // e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
        return {
            selectors: {
                container: '.e-add-posts-container',
                containerCarousel: '.e-add-posts-container.e-add-skin-dualslider',
                thumbsCarousel: '.e-add-dualslider-gallery-thumbs',
                dualsliderCarousel: '.e-add-skin-dualslider',
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

            $thumbsCarousel: this.$element.find(selectors.thumbsCarousel),
            $dualsliderCarousel: this.$element.find(selectors.dualsliderCarousel),

            $containerWrapper: this.$element.find(selectors.containerWrapper),
            
            $items: this.$element.find(selectors.items),
            
            $animationReveal: null,
            $galleryThumbs: null
        };
    }

    bindEvents() {
        let scope = this.elements.$scope,
            id_scope = this.elements.$id_scope,
            elementSettings = this.getElementSettings(),
            widgetType = this.getWidgetType(),
            galleryThumbs = null;

        this.isThumbsCarouselEnabled = false;
        this.adapteHeight();

        if(galleryThumbs) galleryThumbs.destroy();
        galleryThumbs = new Swiper( this.elements.$thumbsCarousel[0], this.thumbCarouselOptions( id_scope, elementSettings ) );
        this.elements.$galleryThumbs = galleryThumbs;
        this.elements.$scope.data('thumbscarousel', galleryThumbs);

        //alert(widgetType+'.carousel');
        //Widget_EADD_Query_carousel_Handler();
        //elementorFrontend.elementsHandler.runReadyTrigger(jQUery(widgetType+'.carousel'));

        elementorFrontend.hooks.doAction('frontend/element_ready/'+widgetType+'.carousel', scope);
        

        //da fare.... l'evento di resizng per getire l'altezza delle tumbnail in caso di left-right
        this.initEvents();


         // ---------------------------------------------
        // Funzione di callback eseguita quando avvengono le mutazioni
        /*var eAddns_MutationObserverCallback = function(mutationsList, observer) {
            for(var mutation of mutationsList) {
                if (mutation.type == 'attributes') {
                if (mutation.attributeName === 'class') {
                        if (this.isThumbsCarouselEnabled) {
                            galleryThumbs.update();
                            this.adapteHeight();
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
    initEvents(){
		// on risize adapting blocks and elements
		window.addEventListener("resize", (event) => {
			this.adapteHeight();
		});
		
	}
    onElementChange(propertyName){
        
        if (    EADD_skinPrefix+'ratio_image' === propertyName || 
                EADD_skinPrefix+'dualslider_distribution_vertical' === propertyName || 
                EADD_skinPrefix+'dualslider_height_container' === propertyName
            ) {
            this.elements.$galleryThumbs.update();
            this.adapteHeight();
            console.log(propertyName);
        }
    }
    adapteHeight(){
        let elementSettings = this.getElementSettings();
        if(elementSettings[EADD_skinPrefix+'dualslider_style'] == 'row-reverse' || elementSettings[EADD_skinPrefix+'dualslider_style'] == 'row'){
            this.elements.$thumbsCarousel.height( this.elements.$dualsliderCarousel.height() );
        }
    }
    thumbCarouselOptions( id_scope, elementSettings ){ 
        
        let slidesPerView = Number(elementSettings[EADD_skinPrefix+'thumbnails_slidesPerView']),
            elementorBreakpoints = elementorFrontend.config.breakpoints,
            directionThumbSlider = 'horizontal';

        if(elementSettings[EADD_skinPrefix+'dualslider_style'] == 'row-reverse' || elementSettings[EADD_skinPrefix+'dualslider_style'] == 'row'){
            directionThumbSlider = 'vertical';
        }else if(elementSettings[EADD_skinPrefix+'dualslider_style'] == 'column-reverse' || elementSettings[EADD_skinPrefix+'dualslider_style'] == 'column'){
            directionThumbSlider = 'horizontal';
        }

        var eaddSwiperOptions = {
            spaceBetween: Number(elementSettings[EADD_skinPrefix+'dualslider_gap']) || 0,
            slidesPerView: slidesPerView || 'auto',
            //freeMode: true,
            
            autoHeight: true,
            //watchOverflow: true,
            
            direction:  directionThumbSlider,

            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            
            navigation: {
                nextEl: '.next-' + id_scope, //'.swiper-button-next',
                prevEl: '.prev-' + id_scope, //'.swiper-button-prev',
                //hideOnClick: false,
                //disabledClass: 'swiper-button-disabled', //   CSS class name added to navigation button when it becomes disabled
                //hiddenClass: 'swiper-button-hidden', //   CSS class name added to navigation button when it becomes hidden
            },

            // centeredSlides: true,
            loop: true,
            
            // loopedSlides: 4

            on: {
                init: function () {
                    this.isThumbsCarouselEnabled = true;    
                },
                
            }
        };
        
        var responsivePoints = eaddSwiperOptions.breakpoints = {};
        responsivePoints[elementorBreakpoints.lg] = {
            slidesPerView: Number(elementSettings[EADD_skinPrefix+'thumbnails_slidesPerView']) || 'auto',
            spaceBetween: Number(elementSettings[EADD_skinPrefix+'dualslider_gap']) || 0,
        };
        responsivePoints[elementorBreakpoints.md] = {
            slidesPerView: Number(elementSettings[EADD_skinPrefix+'thumbnails_slidesPerView_tablet']) || Number(elementSettings[EADD_skinPrefix+'thumbnails_slidesPerView']) || 'auto',
            spaceBetween: Number(elementSettings[EADD_skinPrefix+'dualslider_gap_tablet']) || Number(elementSettings[EADD_skinPrefix+'dualslider_gap']) || 0,
        };
        responsivePoints[elementorBreakpoints.xs] = {
            slidesPerView: Number(elementSettings[EADD_skinPrefix+'thumbnails_slidesPerView_mobile']) || Number(elementSettings[EADD_skinPrefix+'thumbnails_slidesPerView_tablet']) || Number(elementSettings[EADD_skinPrefix+'thumbnails_slidesPerView']) || 'auto',
            spaceBetween: Number(elementSettings[EADD_skinPrefix+'dualslider_gap_mobile']) || Number(elementSettings[EADD_skinPrefix+'dualslider_gap_tablet']) || Number(elementSettings[EADD_skinPrefix+'spaceBetween']) || 0,
        };
        eaddSwiperOptions = jQuery.extend(eaddSwiperOptions, responsivePoints);

        return eaddSwiperOptions;
    }
}

    const Widget_EADD_Query_dualslider_Handler = ($element) => {
       
        elementorFrontend.elementsHandler.addHandler(WidgetQueryDualSliderHandlerClass, {
            $element,
        });

    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.dualslider', Widget_EADD_Query_dualslider_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.dualslider', Widget_EADD_Query_dualslider_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.dualslider', Widget_EADD_Query_dualslider_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.dualslider', Widget_EADD_Query_dualslider_Handler);
});