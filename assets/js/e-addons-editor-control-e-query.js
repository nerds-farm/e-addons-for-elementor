/******************************************************************************/
/* Credits to Select2 for the original code                                   */
/******************************************************************************/
jQuery(window).on('load', function () {

    // e-query Control
    let ControlEQuery = elementor.modules.controls.Select2.extend({
        cache: null,
        isTitlesReceived: false,
        getSelect2Placeholder: function getSelect2Placeholder() {
            return {
                id: '',
                text: this.model.get('placeholder'),
            };
        },
        getSelect2DefaultOptions: function getSelect2DefaultOptions() {
            let equery = this;
            return jQuery.extend(elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply(this, arguments), {
                allowClear: true,
                placeholder: this.getSelect2Placeholder(),
                dir: elementorCommon.config.isRTL ? 'rtl' : 'ltr',
                minimumInputLength: 1,
                ajax: {
                    transport: function transport(params, success, failure) {
                        let data = {
                            q: params.data.q,
                            query_type: equery.model.get('query_type'),
                            object_type: equery.model.get('object_type'),
                        };
                        //console.log(data);
                        return elementorCommon.ajax.addRequest('e_query_control_search', {
                            data: data,
                            success: success,
                            error: failure,
                        });
                    },
                    data: function data(params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    cache: true
                },
                escapeMarkup: function escapeMarkup(markup) {
                    return markup;
                },
            });
        },
        getValueTitles: function getValueTitles() {
            var equery = this,
                    ids = this.getControlValue(),
                    query_type = this.model.get('query_type'),
                    object_type = this.model.get('object_type');
            if (!ids || !query_type)
                return;
            if (!_.isArray(ids)) {
                ids = [ids];
            }
            /*console.log('e_query_control_options');
             console.log(query_type);
             console.log(object_type);
             console.log(equery.cid + query_type);*/
            elementorCommon.ajax.loadObjects({
                action: 'e_query_control_options',
                ids: ids,
                data: {
                    query_type: query_type,
                    object_type: object_type,
                    unique_id: '' + equery.cid + query_type,
                },
                success: function success(data) {
                    //console.log('success');
                    equery.isTitlesReceived = true;
                    equery.model.set('options', data);
                    equery.render();
                },
                error: function error(data) {
                    console.log('error');
                    console.log(data);
                },
                before: function before() {
                    equery.addSpinner();
                },
            });
        },
        addSpinner: function addSpinner() {
            this.ui.select.prop('disabled', true);
            this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner e-control-spinner">&nbsp;<i class="eicon-spinner eicon-animation-spin"></i>&nbsp;</span>');
        },
        updateQueryBtn: function updateQueryBtn() {
            var e_query = this.ui.select;
            let ethis = jQuery(e_query);
            /*let q_type = ethis.data('query_type'),
             o_type = ethis.data('object_type');*/
            let q_type = this.model.get('query_type'),
                    o_type = this.model.get('object_type');
            ethis.siblings('.e-addons-elementor-control-quick-edit').remove();
            let add_url = '#',
                    edit_url = '#',
                    base_url = ElementorConfig.home_url;

            let q_type_single = q_type.slice(0, -1).toUpperCase();
            if (o_type == 'elementor_library') {
                q_type_single = 'TEMPLATE';
            }

            if (ethis.val() && (!jQuery.isArray(ethis.val()) || (jQuery.isArray(ethis.val()) && ethis.val().length == 1))) {
                switch (q_type) {
                    case 'posts':
                        if (!o_type || o_type != 'type') {
                            edit_url = base_url + '/wp-admin/post.php?post=' + ethis.val();
                            if (o_type == 'elementor_library') {
                                edit_url += '&action=elementor';
                            } else {
                                edit_url += '&action=edit';
                            }
                        }
                        break;
                    case 'users':
                        if (!o_type || o_type != 'role') {
                            edit_url = base_url + '/wp-admin/user-edit.php?user_id=' + ethis.val();
                        }
                        break;
                    case 'terms':
                        if (o_type) {
                            edit_url = base_url + '/wp-admin/term.php?tag_ID=' + ethis.val();
                            edit_url += '&taxonomy=' + o_type;
                            if (o_type == 'nav_menu') {
                                edit_url = base_url + '/nav-menus.php?action=edit&menu=' + ethis.val();
                            }
                        }

                        break;
                }
                if (edit_url != '#') {
                    ethis.parent().append('<div class="elementor-control-unit-1 e-addons-elementor-control-quick-edit tooltip-target" data-tooltip="Edit ' + q_type_single + '"><a href="' + edit_url + '" target="_blank" class="e-addons-quick-edit-btn"><i class="eicon-pencil"></i></a></div>');
                }
            } else {
                switch (q_type) {
                    case 'posts':
                        if (!o_type || o_type != 'type') {
                            add_url = base_url + '/wp-admin/post-new.php';
                            if (o_type) {
                                add_url += '?post_type=' + o_type;
                                if (o_type == 'elementor_library') {
                                    add_url = base_url + '/wp-admin/edit.php?post_type=' + o_type + '#add_new';
                                }
                            }
                        }
                        break;
                    case 'users':
                        if (!o_type || o_type != 'role') {
                            add_url = base_url + '/wp-admin/user-new.php';
                        }
                        break;
                    case 'terms':
                        add_url = base_url + '/wp-admin/edit-tags.php';
                        if (o_type) {
                            edit_url += '&taxonomy=' + o_type;
                        }
                        if (o_type == 'nav_menu') {
                            add_url = base_url + '/wp-admin/nav-menus.php';
                        }
                        break;
                }
                if (add_url != '#') {
                    ethis.parent().prepend('<div class="elementor-control-unit-1 tooltip-target e-addons-elementor-control-quick-edit" data-tooltip="Add New ' + q_type_single + '"><a href="' + add_url + '" target="_blank" class="e-addons-quick-edit-btn"><i class="eicon-plus"></i></a></div>');
                }
            }
            let sel2 = ethis.siblings('.select2-container');
            if (add_url != '#' || edit_url != '#') {
                sel2.addClass('e-quick-btn');
            } else {
                sel2.removeClass('e-quick-btn');
            }

            /*console.log(ethis); //.closest('.elementor-control-field').find('.elementor-control-dynamic-switcher').length);
             if (ethis.siblings('.elementor-control-dynamic-switcher').length > 1) {
             console.log(ethis);
             }*/
        },
        onReady: function onReady() {
            setTimeout(elementor.modules.controls.Select2.prototype.onReady.bind(this));
            if (this.ui.select) {
                var equery = this,
                        ids = this.getControlValue(),
                        query_type = this.model.get('query_type'),
                        object_type = this.model.get('object_type');
                jQuery(this.ui.select).data('query_type', query_type);
                if (object_type) {
                    jQuery(this.ui.select).data('object_type', object_type);
                }

                if (this.$el.hasClass('elementor-control-file')) {
                    this.$el.find('.select2').on('click', function () {
                        let value = equery.ui.select.val();
                        if (value && !jQuery('.select2-search__field').val()) {
                            jQuery('.select2-search__field').val(value).change();
                        }
                    });
                } else {
                    this.ui.select.on('change', function () {
                        equery.updateQueryBtn();
                    });
                    this.updateQueryBtn();
                }
            }
            if (!this.isTitlesReceived) {
                this.getValueTitles();
            }
        },
        /*
         getSelect2Options: function () {
         return jQuery.extend(this.getSelect2DefaultOptions(), this.model.get('select2options'))
         },
         applySavedValue: function () {
         elementor.modules.controls.BaseData.prototype.applySavedValue.apply(this, arguments),
         this.ui.select.data('select2') ? this.ui.select.trigger('change') : (this.ui.select.select2(this.getSelect2Options()), this.model.get('sortable') && this.initSortable())
         },
         initSortable: function initSortable() {
         var e = this.$el.find('ul.select2-selection__rendered'),
         t = this;
         e.sortable({
         containment: 'parent',
         update: function () {
         t._orderSortedOption(e),
         t.container.settings.setExternalChange(t.model.get('name'), t.ui.select.val()),
         t.model.set('options', t.ui.select.val())
         }
         })
         },
         _orderSortedOption: function _orderSortedOption(t) {
         var n = this;
         t.children('li[title]').each(function (t, o) {
         var i = n.ui.select.children('option').filter(function () {
         return e(this).html() == o.title
         });
         n._moveOptionToEnd(i)
         })
         },
         _moveOptionToEnd: function _moveOptionToEnd(e) {
         var t = e.parent();
         e.detach(),
         t.append(e)
         },*/
        onBeforeDestroy: function onBeforeDestroy() {
            if (this.ui.select.data('select2')) {
                this.ui.select.select2('destroy');
            }
            this.$el.remove();
        },
    });

    // Add Control Handlers
    elementor.addControlView('e-query', ControlEQuery);

});