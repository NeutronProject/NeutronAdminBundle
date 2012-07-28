jQuery(document).ready(function(){
	
	jQuery('input[value="EDIT"]').filter(':checked').each(function(k,v){
		var viewElm = jQuery(this).closest('.field').find('input[value="VIEW"]');
		viewElm.attr('checked', true).attr('disabled', true);
		
		jQuery.uniform.update("#" + viewElm.attr('id'));
	});
	
	jQuery('.neutron-acl').click(function(e){
		val = jQuery(this).val();
		if(val == 'EDIT'){ 
			var viewElm = jQuery(this).closest('.field').find('input[value="VIEW"]');
			if(jQuery(this).is(':checked')){
				viewElm.attr('checked', true).attr('disabled', true);
			} else {
				viewElm.attr('disabled', false);
			}
			
			jQuery.uniform.update("#" + viewElm.attr('id'));
		}
	});
});