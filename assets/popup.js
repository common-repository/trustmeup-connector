jQuery(document).ready(function(){
	jQuery('.tmu-popup-close-btn').click(function(){
		jQuery('#tmu-popup').remove();
		jQuery.ajax({
		    type: "post",
		    dataType: "json",
		    url: ajax.url,
		    data : {action: "tmu_set_popup_close_cookie", popup_close: 1},
		    success: function(msg){
		        console.log(msg);
		    }
		});
	})
})