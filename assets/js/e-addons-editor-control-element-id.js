jQuery(window).on('load', function () {
    var events = false;
    var input = false;
    function eSetElementId(e) {
        //console.log(e);
        var preview_iframe = jQuery("iframe#elementor-preview-iframe").contents();
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (jQuery(this).data('id')) {
            //console.log('click element: '+jQuery(this).data('id'));
            //console.log(input);
            var selectedElement = elementor.getCurrentElement();
            //console.log(selectedElement);
            let cid = selectedElement.model.cid;
            input.val(jQuery(this).data('id'));
            elementorFrontend.config.elements.data[cid].attributes[input.data('setting')] = jQuery(this).data('id');
            preview_iframe.off('mousedown', '.elementor-element', eSetElementId);
            input = false;
        }
        return false;
    }
    var elementIdItemView = elementor.modules.controls.BaseData.extend({

        onReady: function () {

            this.$el.find('.elementor-control-element-id-target').click(function () {
                console.log('click target');
                var preview_iframe = jQuery("iframe#elementor-preview-iframe").contents();
                if (input) {
                    //console.log('remove target');
                    input = false;
                    preview_iframe.off('mousedown', '.elementor-element', eSetElementId);
                } else {
                    //console.log('add target');
                    input = jQuery(this).siblings('input[type="text"]');
                    //console.log(preview_iframe.find('.elementor-element').length);
                    preview_iframe.on('mousedown', '.elementor-element', eSetElementId);
                }

            });

        },
        setElementId: function (input) {
            //console.log('click element: '+jQuery(this).data('id'));
            return false;
        }
    });

    elementor.addControlView('element-id', elementIdItemView);

});