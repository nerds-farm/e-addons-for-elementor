jQuery(window).on('elementor/frontend/init', () => {

    class WidgetQueryTableHandlerClass extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            // e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
            return {
                selectors: {
                    table: 'table',
                },
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');

            return {
                $scope: this.$element,
                $id_scope: this.getID(),
                $table: this.$element.find(selectors.table),
            };
        }

        initDataTables() {
            let scope = this.elements.$scope,
                    table = this.elements.$table,
                    elementSettings = this.getElementSettings();

                    
            let buttons = [];
            if (Boolean(elementSettings['table_buttons'])) {
                buttons = [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ];
            }
            
            let lang = [];
            if (Boolean(elementSettings['table_searching'])) {
                lang = {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            }
            
            table.DataTable({
                order: [],

                dom: 'Bfrtip',
                buttons: buttons,
                info: Boolean(elementSettings['table_info']),
                fixedHeader: Boolean(elementSettings['table_fixed_header']),
                responsive: Boolean(elementSettings['table_responsive']),
                
                searching: Boolean(elementSettings['table_searching']),
                language: lang,
                ordering: Boolean(elementSettings['table_ordering']),
                
                paging: false,
            });
            
        }

        bindEvents() {
            let scope = this.elements.$scope,
                    id_scope = this.elements.$id_scope,
                    elementSettings = this.getElementSettings();

            if (elementSettings['table_datatables']) {
                this.initDataTables()
            }

        }

    }

    const Widget_EADD_Query_table_Handler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(WidgetQueryTableHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.table', Widget_EADD_Query_table_Handler);
    //elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.table', Widget_EADD_Query_table_Handler);
});