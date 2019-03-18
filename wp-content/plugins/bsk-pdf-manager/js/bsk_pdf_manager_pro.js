jQuery(document).ready( function($) {
    
    /*
      * PDFs Dropdown
      */
    $(".bsk-pdfm-output-container").on("change", ".bsk-pdfm-pdfs-dropdown", function(){
        var target = $(this).data("target");
		var url = $(this).val();

        target = target == '_blank' ? '_blank' : '_self';
		if( url ){
			window.open( url, target);
		}
    });
    
});
