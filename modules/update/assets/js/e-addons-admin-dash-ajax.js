
var eProAddon = '';
function installProCookieCallback(json){
    console.log(json);
    if (Object.keys(json).length === 0) {
        // user not logged in
        alert('Please login at e-addons.com with your profile for automatic installation');
        window.location.href = eProAddon.attr('href');
        return false;
    }
    for (var cookie in json) {
        if (json.hasOwnProperty(cookie)) {
            var proAddonUrl = eProAddon.attr('href').replace('?', '?cookie=' + json[cookie] + '&');
            //alert(proAddonUrl);
            jQuery.ajax({
                url: window.location.href,
                dataType: "html",
                type: "POST",
                data: {"url": proAddonUrl, "action": 'add'},
                error: function () {
                    console.log("error");
                    window.location.href = eProAddon.attr('href');
                },
                success: function (data, status, xhr) {
                    //console.log(proAddonUrl);
                    //console.log(data);
                    jQuery('#e_addons_form').html(jQuery(data).find('#e_addons_form').html());
                    eProAddon.closest('.my_e_addon').fadeOut();
                    jQuery('#adminmenu .dashicons-warning.e-count ').fadeOut();
                },
            });
        }
    }
}

jQuery(document).ready(function () {
    jQuery(document).on('click', '.my_e_addon_install.e_addon_free', function () {
        jQuery(this).find('.dashicons-download').addClass('dashicons-update').addClass('spin').removeClass('dashicons-download');
        jQuery(this).find('.btn-txt').text('INSTALLING...please wait');
        jQuery(this).css('pointer-events', 'none');
        jQuery.ajax({
            url: window.location.href,
            dataType: "html",
            context: jQuery(this),
            type: "POST",
            data: {"url": jQuery(this).attr('href'), "action": 'add'},
            error: function () {
                console.log("error");
                window.location.href = jQuery(this).attr('href');
            },
            success: function (data, status, xhr) {
                //console.log(data);
                jQuery('#e_addons_form').html(jQuery(data).find('#e_addons_form').html());
                jQuery(this).closest('.my_e_addon').fadeOut();
                jQuery('#adminmenu .dashicons-warning.e-count ').fadeOut();
            },
        });
        return false;
    });
    
    
    
    jQuery(document).on('click', '.my_e_addon_install.e_addon_pro', function () {
        eProAddon = jQuery(this);
        jQuery(this).find('.dashicons-download').addClass('dashicons-update').addClass('spin').removeClass('dashicons-download');
        jQuery(this).find('.btn-txt').text('INSTALLING PRO...please wait');
        jQuery(this).css('pointer-events', 'none');
        jQuery.ajax({
            url: 'https://e-addons.com/edd/cookie.php',
            dataType: "jsonp",
            jsonp: "installProCookieCallback",
            error: function (data) {
                console.log("error");
                alert('To proceed with the quick installation for your bought PRO addons, please log in with your account on e-addons.com shop');
                //window.location.href = jQuery(this).attr('href');
                window.open("https://e-addons.com/your-account/");
                eProAddon.find('.dashicons-update').addClass('dashicons-download').removeClass('spin').removeClass('dashicons-update');
                eProAddon.find('.btn-txt').text('INSTALL PRO e-ADDON');
                eProAddon.css('pointer-events', 'auto');
            },
            success: function (data) {
                console.log(data);     
                //var cookie = jQuery.parseJSON( data );
            },
        });
        return false;
    });

    jQuery(document).on('click', '.my_e_addon_version_update', function () {
        jQuery(this).find('.dashicons-update').addClass('spin');
        jQuery(this).find('.btn-txt').text('UPDATING...');   
        jQuery(this).css('pointer-events', 'none');
        jQuery.ajax({
            url: jQuery(this).data('update'),
            dataType: "html",
            context: jQuery(this),
            type: "GET",
            error: function () {
                console.log("error");
                window.location.href = jQuery(this).data('update');
            },
            success: function (data, status, xhr) {
                //console.log(data);
                jQuery.ajax({
                    url: window.location.href,
                    dataType: "html",
                    context: jQuery(this),
                    type: "GET",
                    //data: {"url": jQuery(this).attr('href'), "action": 'update', "addon": jQuery(this).data('addon')},
                    error: function () {
                        console.log("error");
                        window.location.href = jQuery(this).attr('href');
                    },
                    success: function (data, status, xhr) {
                        //console.log(data);
                        var plugin = false;
                        if (!jQuery(this).closest('.my_e_addon').hasClass('my_e_addon_disabled')) {
                            plugin = jQuery(this).data('addon');
                            console.log(plugin);
                        }
                        jQuery('#e_addons_form').html(jQuery(data).find('#e_addons_form').html());
                        if (plugin) {
                            if (jQuery('#my_e_addons__'+plugin).hasClass('my_e_addon_disabled')) {
                                jQuery('#my_e_addons__'+plugin+' .my_e_addon_activate').trigger('click');
                            }
                        }
                    },
                });
            },
        });
        
        
        return false;
    });
    
    jQuery(document).on('click', '.my_e_addon_license_close, .my_e_addon_license_closed', function () {   
        if (!jQuery(this).hasClass('my_e_addon_license_closed_no')) {
            jQuery(this).closest('.my_e_addon').find('.my_e_addon_license').toggle();
            jQuery(this).closest('.my_e_addon').find('.my_e_addon_license_closed').toggle();
            return false;
        }        
    });
    
    jQuery(document).on('click', '.my_notice_eaddons_update', function () {        
        //console.log(jQuery(this).attr('href')+' .my_e_addon_version_update');
        
        jQuery(this).children('.dashicons').addClass('spin');
        jQuery(jQuery(this).attr('href')+' .my_e_addon_version_update').trigger('click');
        
        /*if (jQuery(jQuery(this).attr('href')).length) {
            if (jQuery(jQuery(this).attr('href')).offset().top) {
                jQuery('html, body').animate({
                    scrollTop: jQuery(jQuery(this).attr('href')).offset().top - jQuery('#wpadminbar').height()
                }, 500);
            }
        }*/
        jQuery(this).closest('.notice').delay(2000).fadeOut();
        return false;
    });
    
});
