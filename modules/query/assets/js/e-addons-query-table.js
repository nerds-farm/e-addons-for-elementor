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
                    table = this.elements.$table;

            this.elementSettings = this.getElementSettings();


            let buttons = [];
            if (Boolean(this.elementSettings['table_buttons'])) {
                buttons = [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ];
            }

            let lang = [];
            if (Boolean(this.elementSettings['table_searching'])) {
                lang = {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            }

            table.DataTable({
                order: [],

                dom: 'Bfrtip',
                buttons: buttons,
                info: Boolean(this.elementSettings['table_info']),
                fixedHeader: Boolean(this.elementSettings['table_fixed_header']),
                responsive: Boolean(this.elementSettings['table_responsive']),

                searching: Boolean(this.elementSettings['table_searching']),
                language: lang,
                ordering: Boolean(this.elementSettings['table_ordering']),

                paging: false,
            });

        }

        bindEvents() {
            this.skinPrefix = this.$element.data('widget_type').split('.').pop() + '_';
            this.elementSettings = this.getElementSettings();

            let scope = this.elements.$scope,
                    id_scope = this.elements.$id_scope;

            if (this.elementSettings['table_datatables']) {
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