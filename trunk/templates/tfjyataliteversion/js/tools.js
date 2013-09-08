	jQuery(document).ready(function() {
						   jQuery("#main img").hover(function(){
                                                   if (jQuery(this).parent().attr('id')=='graph') { return; }
						   jQuery(this).fadeTo("slow", 0.7); 
						   },function(){
                                                   if (jQuery(this).parent().attr('id')=='graph') { return; }
						   jQuery(this).fadeTo("fast", 1.0); 
						   });
				});

jQuery(function(){
	jQuery('#gotop a').click(function(){
		 jQuery('html, body').animate({scrollTop: '0px'}, 800);
		 return false;
	});
});

