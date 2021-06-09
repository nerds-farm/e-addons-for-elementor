jQuery(window).on('load', function () {
    var fileItemView = elementor.modules.controls.BaseData.extend({

        onReady: function () {

            var file_input_id = this.$el.find('.e-selected-file').attr('id');
            var multiple = this.$el.find('.e-selected-file').data('multiple');
            //console.log(multiple);
            this.$el.find('.e-select-file').click(function () {
                var frame = wp.media({
                    title: 'Upload File',
                    button: {
                        text: multiple ? 'Get Medias' : 'Get Media',
                    },
                    multiple: multiple
                });

                frame.on('select', function () {
                    var attachments = frame.state().get('selection').toJSON();
                    console.log(attachments);
                    let data = attachments.map((obj) => obj.id);
                    console.log(data);
                    jQuery("#" + file_input_id).val(data);
                    //jQuery( "#" + file_input_id ).val( attachment.url );
                    jQuery("#" + file_input_id).trigger("input");
                });

                frame.on('open', function () {
                    var selection = frame.state().get('selection');
                    var ids_value = jQuery('#' + file_input_id).val();
                    if (ids_value.length > 0) {
                        var ids = ids_value.split(',');
                        ids.forEach(function (id) {
                            attachment = wp.media.attachment(id);
                            attachment.fetch();
                            selection.add(attachment ? [attachment] : []);
                        });
                    }
                });

                frame.open();
            });


        },
    });

    elementor.addControlView('file', fileItemView);

});