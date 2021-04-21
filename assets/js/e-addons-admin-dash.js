jQuery(document).ready(function () {

    jQuery(document).on('click', '.my_e_addon_activate, .my_e_addon_deactivate', function () {
        jQuery(this).children('.dashicons-insert, .dashicons-remove').addClass('dashicons-update').addClass('spin').removeClass('dashicons-insert').removeClass('dashicons-remove');
        //jQuery(this).children('.btn-txt').text('Changing...');
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            dataType: "html",
            context: jQuery(this),
            type: "GET",
            error: function () {
                console.log("error");
                window.location.href = jQuery(this).attr('href');
            },
            success: function (data, status, xhr) {
                //console.log(data);
                jQuery(this).closest('.my_e_addon').toggleClass('my_e_addon_disabled');
                jQuery(this).closest('.my_e_addon').find('.my_e_addon_activate > .dashicons-update').removeClass('dashicons-update').removeClass('spin').addClass('dashicons-insert');
                jQuery(this).closest('.my_e_addon').find('.my_e_addon_deactivate > .dashicons-update').removeClass('dashicons-update').removeClass('spin').addClass('dashicons-remove');
            },
        });
        return false;
    });

});