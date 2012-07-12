jQuery(document).ready(function() {

	if(jQuery('#neutron_admin_form_category_add_slug').length > 0){
		jQuery('#neutron_admin_form_category_add_externalUri-div').hide();
	}
	
	jQuery('#neutron_admin_form_category_add_type').change(function(){
		var val = jQuery(this).val();
		
		if(val == 'neutron.plugin.external'){
			jQuery('#neutron_admin_form_category_add_slug').val('');
			jQuery('#neutron_admin_form_category_add_externalUri-div').show();
			jQuery('#neutron_admin_form_category_add_slug-div').hide();
		} else {
			jQuery('#neutron_admin_form_category_add_externalUri').val('');
			jQuery('#neutron_admin_form_category_add_externalUri-div').hide();
			jQuery('#neutron_admin_form_category_add_slug-div').show();
		}
	});
});