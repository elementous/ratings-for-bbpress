jQuery( document ).ready(function() {
    jQuery( "#reset-rating-stats-template" ).on( "click", function() {
		jQuery("#rating-stats-template").html('User Rating: %BBP_USER_AVERAGE%');
	});
});