jQuery(document).ready(function(){
	
	$(document).ajaxStop($.unblockUI); 
	
	jQuery('.submit-type').click(function(){ 
		var form = jQuery(this).closest('.form-tabbed'); 
		var disabledElms = form.find(':input:disabled');
		disabledElms.attr('disabled', false);
		params = form.serializeArray(); 
		jQuery.ajax({
			url: form.attr('action'),
			type: 'post',
			dataType: 'json',
			data: params,
			beforeSend: function(){
				jQuery.blockUI({ message: $('#block-ui-message')});
				clearMsgs();
			},
			success: function(data, textStatus, jqXHR){
				if(data.success == true){
					if(data.redirect_uri != 'undefined'){
						window.location = data.redirect_uri;
					}
				} else if (data.success == false){ 
					console.log(data.errors);
					buildErrors(data.errors);
					buildSuccessStatus();
					jQuery('.field-error').fadeIn('slow').fadeOut().fadeIn('slow');
					jQuery('#messages').append(jQuery('#message-form-invalid').html());
					$("html, body").animate({scrollTop:0}, "slow");
				}
			},
			error: function(jqXHR, textStatus, errorThrown){ 
				jQuery('#messages').append('<div class="msg error"><p>' + textStatus + '</p><a class="close">×</a></div>');
				$("html, body").animate({scrollTop:0}, "slow");
			}
		});
		disabledElms.attr('disabled', true);
		return false;
	});
});

function buildErrors(errors){
	var tab = false;
	
	if(!jQuery.isPlainObject(errors)){
		return false;
	}
	
	jQuery.each(errors, function(k,v){ 
		if(tab === false && jQuery.isPlainObject(v)){
			tab = 'tab-' + k;
			jQuery('[href="#'+ tab +'"]').click();
		}
		
		buildErrorMsgs(k,v);
		buildErrors(v);
		
	});
	
	
}

function buildErrorMsgs(key, errors)
{  
	var html = '';
	if(jQuery.isArray(errors)){
		jQuery.each(errors, function(k,v){
			html += '<p class="field-error" style="display:none;">' + v + '</p>';
		});
	}
	
	jQuery('#' + key).closest('.field').addClass('error').find('.controls').append(html);

}

function buildSuccessStatus()
{
	jQuery('.field').not('.error').addClass('success');
}

function clearMsgs()
{
	jQuery('.field').removeClass('error').removeClass('success');
	jQuery('.field-error').remove();
	jQuery('#messages').empty();
	
}


