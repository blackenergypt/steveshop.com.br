jQuery(document).ready(function() {
    function openFancybox() {
        setTimeout(function() {
            jQuery('#popuplink').trigger('click');
        }, 500);
    };
    var visited = jQuery.cookie('visited');
    if (visited == 'yes') {
        // second page load, cookie active
    } else {
        openFancybox(); // first page load, launch fancybox
    }
    jQuery.cookie('visited', 'yes', {
        expires: 365 // the number of days cookie  will be effective
    });
    jQuery("#popuplink").fancybox({ modal: true, maxWidth: 400, overlay: { closeClick: true } });
});