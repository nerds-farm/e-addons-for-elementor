class eadd_timeline {
    constructor($target, $ob) {
        this.timelineEl = $target;

        this.timelineSectionHeight = 0;

        this.primoBlocco = null;
        this.primoBloccoPos = 0;
        this.ultimoBlocco = null;
        this.ultimoBloccoPos = 0;

        this.isTimelineEnabled = false;
        this.scrolling = false;
        this.scrtop = 0;
        this.offset = 0.8;
        this.rowspace = 10;

        this.verticalPositionContent = $ob.timelineVerticalposition;
        //oggetti
        this.blocks = null;
        this.images = null;
        this.contents = null;

        this.current = 0;

        //preparo: oggetti ed eventi
        this.verticalTimeline($target[0]);
        this.initEvents();

        // controllo lo scroll e eseguo la situazione
        this.checkTimelineScroll();
    }
    verticalTimeline(element) {

        this.blocks = element.getElementsByClassName("e-add-timeline__block");
        this.images = element.getElementsByClassName("e-add-timeline__img");
        this.contents = element.getElementsByClassName("e-add-timeline__content");

        this.primoBlocco = jQuery(this.blocks).first().find('.e-add-timeline__img');
        this.ultimoBlocco = jQuery(this.blocks).last().find('.e-add-timeline__img');

        this.hideBlocks();

    }
    initEvents() {
        // on risize adapting blocks and elements
        window.addEventListener("resize", (event) => {
            this.checkTimelineScroll();
        });
        //show timeline blocks on scrolling
        window.addEventListener("scroll", (event) => {
            if (!this.scrolling) {
                this.scrolling = true;
                (!window.requestAnimationFrame) ? setTimeout(() => {
                    this.checkTimelineScroll();
                }, 250) : window.requestAnimationFrame(() => {
                    this.checkTimelineScroll();
                });
            }
        });
    }
    checkTimelineScroll() {
        this.primoBlocco = jQuery(this.blocks).first().find('.e-add-timeline__img');
        this.ultimoBlocco = jQuery(this.blocks).last().find('.e-add-timeline__img');

        let vedemo = jQuery(window).scrollTop() - (this.timelineEl.offset().top + (jQuery(window).height() / 2));
        //this.blocks[this.current].getBoundingClientRect().top

        //this.offset = 1-(vedemo/this.timelineEl.height());
        console.log(this.offset);
        /*
         for( var i = 0; i < this.blocks.length; i++) {
         //console.log( (i+1)+' / '+this.blocks.length );
         if( (i+1) == this.blocks.length ){
         if(this.contents[i].classList.contains("e-add-timeline__content--hidden")){

         console.log( 'last' );
         }
         }
         }*/

        this.primoBloccoPos = (this.primoBlocco.offset().top - this.timelineEl.offset().top);
        if (this.primoBloccoPos <= 0)
            this.primoBloccoPos = 0;
        this.timelineEl.find('.e-add-timeline-wrapper').get(0).style.setProperty('--lineTop', (this.primoBloccoPos + 10) + 'px');

        this.ultimoBloccoPos = ((this.ultimoBlocco.offset().top - this.ultimoBlocco.position().top) - this.timelineEl.offset().top) + this.rowspace;
        this.timelineEl.find('.e-add-timeline-wrapper').get(0).style.setProperty('--lineFixed', this.ultimoBloccoPos + 'px');

        // ---------
        this.timelineSectionHeight = this.timelineEl.height(),
                this.scrtop = jQuery(window).scrollTop() - (this.timelineEl.offset().top + this.ultimoBlocco.position().top) + (jQuery(window).height() * this.offset);

        if (this.scrtop >= this.ultimoBloccoPos) {
            this.scrtop = this.ultimoBloccoPos;
        }
        this.timelineEl.find('.e-add-timeline-wrapper').get(0).style.setProperty('--lineProgress', this.scrtop + 'px');

        //soluzione -A- metto un margin sotto
        //this.timelineEl.css('margin-bottom',this.ultimoBlocco.position().top);



        this.timelineEl.css('margin-bottom', 'calc(' + ((1 - this.offset) * 100) + 'vh - ' + jQuery(this.blocks).last().find('.e-add-timeline__content').height() * (1 - (this.verticalPositionContent / 100)) + 'px)');






        //this.timelineEl.css( 'margin-bottom','calc('+(this.offset*100)+'vh - '+jQuery(this.blocks).last().find( '.e-add-timeline__content' ).height() * (1-(this.verticalPositionContent/100))+'px - '+jQuery(this.blocks).last().find( '.e-add-timeline__content' ).height()+'px) + '+this.ultimoBlocco.height()+'px' );
        //alert(jQuery(this.blocks).last().find( '.e-add-timeline__content' ).height() * (1-(this.verticalPositionContent/100)));

        //soluzione -B-
        // da provare.. l'ultimo elemento usa on'offset di 0.9 invece che 0.6


        this.showBlocks();
        // ---------
        this.scrolling = false;
    }
    hideBlocks() {

        for (var i = 0; i < this.blocks.length; i++) {
            ((i) => {
                if (this.blocks[i].getBoundingClientRect().top > window.innerHeight * this.offset) {

                    this.images[i].classList.add("e-add-timeline__img--hidden");
                    this.contents[i].classList.add("e-add-timeline__content--hidden");
                }
            })(i);
        }
    }
    showBlocks() {

        for (var i = 0; i < this.blocks.length; i++) {


            if (this.images[i].getBoundingClientRect().top <= window.innerHeight * this.offset) {
                //

                // add bounce-in animation
                if (this.contents[i].classList.contains("e-add-timeline__content--hidden")) {
                    this.current = i;

                    this.images[i].classList.add("e-add-timeline__img--bounce-in");
                    this.contents[i].classList.add("e-add-timeline__content--bounce-in");
                    this.images[i].classList.remove("e-add-timeline__img--hidden");
                    this.contents[i].classList.remove("e-add-timeline__content--hidden");
                }
                this.blocks[i].classList.add("e-add-timeline__focus");
            } else {
                this.blocks[i].classList.remove("e-add-timeline__focus");
            }

        }
    }
}
jQuery(window).on('elementor/frontend/init', () => {
    class WidgetQueryTimelineHandlerClass extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    container: '.e-add-posts-container',
                    containerTimeline: '.e-add-posts-container.e-add-skin-timeline',

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
                $containerTimeline: this.$element.find(selectors.containerTimeline),
                $containerWrapper: this.$element.find(selectors.containerWrapper),

                $items: this.$element.find(selectors.items),

                //$isMasonryEnabled = false
                $timelineObject: null,
            };
        }

        bindEvents() {
            this.skinPrefix = this.$element.data('widget_type').split('.').pop() + '_';
            this.elementSettings = this.getElementSettings();

            let scope = this.elements.$scope,
                    id_scope = this.elements.$id_scope;

            let elementsObject = {
                timelineVerticalposition: this.elementSettings[this.skinPrefix + 'timeline_verticalposition'] ? Number(this.elementSettings[this.skinPrefix + 'timeline_verticalposition']['size']) : 50,
            };

            this.elements.$containerTimeline.imagesLoaded(() => {
                setTimeout(() => {
                    this.elements.$timelineObject = new eadd_timeline(this.elements.$containerTimeline, elementsObject);
                }, 300);
            });


        }
        /*
         onInit(){
         //alert('init');
         }
         */

        onElementChange(propertyName) {

        }

    }

    const Widget_EADD_Query_timeline_Handler = ($element) => {

        elementorFrontend.elementsHandler.addHandler(WidgetQueryTimelineHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.timeline', Widget_EADD_Query_timeline_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.timeline', Widget_EADD_Query_timeline_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.timeline', Widget_EADD_Query_timeline_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.timeline', Widget_EADD_Query_timeline_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.timeline', Widget_EADD_Query_timeline_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.timeline', Widget_EADD_Query_timeline_Handler);
});