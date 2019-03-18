jQuery(document).ready( function($) {
	$(".bsk-pdf-manager-pdfs-select").change(function(){
		var target = $(this).attr("attr_target");
		var url = $(this).val();
		
		target = target == '_blank' ? target : '_self';
		if( url ){
			window.open( url, target);
		}
	});
	
	if( $(".bsk-pdfm-selector-pdfs-dropdown").length > 0 ){
		$(".bsk-pdfm-selector-pdfs-dropdown").on("change", function(){
			var target = $(this).attr("attr_target");
			var url = $(this).val();
			
			target = target == '_blank' ? target : '_self';
			if( url ){
				window.open( url, target);
			}
		});
	}
	
}); //end of jQuery(document).ready( function($) {
