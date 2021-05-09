jQuery(window).on('elementor/frontend/init', () => {
		
	class WidgetQueryJustifiedGridHandlerClass extends elementorModules.frontend.handlers.Base {
		getDefaultSettings() {
			// e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
			return {
				selectors: {
					container: '.e-add-posts-container',
					containerGrid: '.e-add-posts-container.e-add-skin-justifiedgrid',
					
					containerWrapper: '.e-add-wrapper-justifiedgrid',
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
				$containerGrid: this.$element.find(selectors.containerGrid),
				$containerWrapper: this.$element.find(selectors.containerWrapper),
				
				$items: this.$element.find(selectors.items),
				
				//$isMasonryEnabled = false
				$justifiedObject: null,
				$animationReveal: null
			};
		}

		bindEvents() {
			let scope = this.elements.$scope,
				id_scope = this.elements.$id_scope,
				elementSettings = this.getElementSettings(),
				justified_rowHeight = Number(elementSettings[EADD_skinPrefix+'justified_rowHeight']['size']) || 270,
				justified_margin = Number(elementSettings[EADD_skinPrefix+'justified_margin']['size']) || 0,
				justified_lastRow = elementSettings[EADD_skinPrefix+'justified_lastRow'] || 'justify';
			
			// -------------------------------------------
			if( elementSettings[EADD_skinPrefix+'scrollreveal_effect_type'] ){
				var isLive = elementSettings[EADD_skinPrefix+'scrollreveal_live'] ? false : true;
				this.elements.$animationReveal = new eadd_animationReveal( this.elements.$container, isLive );
			}
			
			this.elements.$containerWrapper.justifiedGallery({
				rowHeight : justified_rowHeight, //170
				lastRow : justified_lastRow, //'justify' 'nojustify' 'left' 'justify' 'nojustify' 'left' 'center''right''hide'
				margins : justified_margin, //0
				captions: false,
				cssAnimation: false,
				selector: '.e-add-item-justifiedgrid',
				imgSelector: '> .e-add-post-block > .e-add-image-area > .e-add-item > .e-add-post-image > .e-add-img > img, > .e-add-post-block > .e-add-image-area > .e-add-item > .e-add-post-image > .e-add-img  > a > img'
			}).on('jg.complete', function (e) {
				//console.log(e);
			});;
			
		}
		/*
		onInit(){
			//alert('init');
		}
		*/
		
		onElementChange(propertyName){
			//console.log(propertyName);
			let elementSettings = this.getElementSettings();

			/*
			if (EADD_skinPrefix+'grid_type' === propertyName) {
				if(  elementSettings[propertyName] != 'masonry' && this.elements.$masonryObject ){
					this.elements.$masonryObject.removeMasonry();
				}
			}

			if ( EADD_skinPrefix+'columns_grid' === propertyName ||
				EADD_skinPrefix+'row_gap' === propertyName && this.elements.$masonryObject ) {
				if(this.elements.$masonryObject)
					this.elements.$masonryObject.layoutMasonry();
			}*/
		}

	}
	
    const Widget_EADD_Query_justifiedgrid_Handler = ($element) => {
		
        elementorFrontend.elementsHandler.addHandler(WidgetQueryJustifiedGridHandlerClass, {
            $element,
        });
	};
	
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.justifiedgrid', Widget_EADD_Query_justifiedgrid_Handler);
});