var e_model_cid = e_model_cid || false;

jQuery(window).on('load', function () {
    elementor.hooks.addAction('panel/open_editor/section', function (panel, model, view) {
        e_model_cid = model.cid;
    });
    elementor.hooks.addAction('panel/open_editor/column', function (panel, model, view) {
        e_model_cid = model.cid;
    });
    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        e_model_cid = model.cid;
    });


    var formFieldsItemView = elementor.modules.controls.BaseData.extend({
        onReady: function () {

            //console.log('form_fields');

            var selectedElement = (typeof elementor.getCurrentElement === 'function') ? elementor.getCurrentElement() : false;
            if (selectedElement) {
                var cid = selectedElement.model.cid;
            } else {
                var cid = e_model_cid; //jQuery('.elementor-navigator__item.elementor-editing').parent().data('model-cid');
            }
            console.log(cid);

            if (elementorFrontend.config.elements.data[cid]) {
                var settings = elementorFrontend.config.elements.data[cid].attributes;
                var fields = settings['form_fields'];
                //console.log(settings);

                var options = '<option value="">No field</option>';
                if (fields) {
                    jQuery(fields.models).each(function (index, element) {
                        var field_label = '[' + element.attributes.custom_id + '] (' + element.attributes.field_type + ')';
                        if (element.attributes.field_label) {
                            if (element.attributes.field_label.length > 20) {
                                field_label = element.attributes.field_label.substr(0, 20) + '… ' + field_label;
                            } else {
                                field_label = element.attributes.field_label + ' ' + field_label;
                            }
                        }
                        options += '<option value="' + element.attributes.custom_id + '">' + field_label + '</option>';
                    });
                }

                // single field
                var select = this.$el.find('select');
                var data_setting = select.data('setting');
                //var self = this;

                setTimeout(() => {
                    //console.log(this);
                    if (this.options.container && this.options.container.type == 'repeater') {
                        // in repeater
                        //
                        var index = this._parent._index;
                        var repeter = this.options.container.model.attributes.name;
                        //console.log(index);
                        var ids = settings[repeter].models[index].attributes[data_setting];
                    } else {
                        var ids = settings[data_setting];
                    }
                    var is_select2 = false;

                    if (select.hasClass("select2-hidden-accessible")) {
                        select.select2('destroy');
                        is_select2 = true;
                    }
                    select.html(options);
                    /*if (custom_id_input) {
                     // remove itself
                     select.find("option[value='" + custom_id_input.val() + "']").remove();
                     }*/
                    select.val(ids);
                    if (is_select2 || select.hasClass('elementor-select2')) { //select.prop('multiple')) {
                        //console.log('select2');
                        select.select2();
                    }
                }, 100);
            }


        },
    });

    elementor.addControlView('form_fields', formFieldsItemView);
});

