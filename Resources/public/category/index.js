jQuery(document).ready(function() {
	jQuery('body').bind('neutron.tree.event.error', function(event){
		jQuery.jGrowl(event.message);
	});
});