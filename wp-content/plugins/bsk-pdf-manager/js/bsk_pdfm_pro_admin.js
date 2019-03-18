jQuery(document).ready( function($) {
	
	$("#bsk_pdf_manager_categories_id").change( function() {
		var cat_id = $(this).val();
		var new_action = $("#bsk-pdf-manager-pdfs-form-id").attr('action') + '&cat=' + cat_id;
		
		$("#bsk-pdf-manager-pdfs-form-id").attr('action', new_action);
		
		$("#bsk-pdf-manager-pdfs-form-id").submit();
	});
	
	$("#doaction").click( function() {
		var cat_id = $("#bsk_pdf_manager_categories_id").val();
		var new_action = $("#bsk-pdf-manager-pdfs-form-id").attr('action') + '&cat=' + cat_id;
		
		$("#bsk-pdf-manager-pdfs-form-id").attr('action', new_action);
		
		return true;
	});
	
    /*
     category
     *
     */
    $("#cat_password_id").keyup( function(){
        //only number & letters
        this.value = this.value.replace(/[^0-9a-zA-Z]/g, '');
    });
    
	$("#bsk_pdf_manager_category_save").click( function() {
		var cat_title = $("#cat_title_id").val();
		if ($.trim(cat_title) == ''){
			alert('Category title can not be NULL.');
			
			$("#cat_title_id").focus();
			return false;
		}
		
		$("#bsk_pdf_manager_categories_form_id").submit();
	});
	/*
      pdf
      *
      */
    $("#bsk_pdf_edit_cat_ID").change( function() {
        var cat_id = $(this).val();
        if( cat_id < 1 ){
            return;
        }
        //add new cat id
        var exist_cat_ids = $("#bsk_pdf_edit_cat_ids_ID").val();
        var exist_cat_ids_array = new Array;
        var already_set = false;
        if( exist_cat_ids.length > 0 ){
            exist_cat_ids_array = exist_cat_ids.split(',');
            if( exist_cat_ids_array.length > 0 ){
                for( var i = 0; i < exist_cat_ids_array.length; i++ ){
                    if( exist_cat_ids_array[i] == cat_id ){
                        already_set = true;
                        break;
                    }
                }
            }
        }
        if( already_set == true ){
            return;
        }
        if( exist_cat_ids_array.length > 0 ){
            //only support one category
        }else{
            exist_cat_ids_array.push( cat_id );
            var cat_title = $("#bsk_pdf_edit_cat_ID option:selected").text();
            cat_title = $.trim(cat_title);
            var delete_icon = $("#bsk_pdf_edit_delete_cat_icon_ID").val();
            var html = '<span style="display: inline-block;padding-right:10px;"><a href="javascript:void(0);" class="bsk-pdf-edit-delete-cat-anchor" data-catid="' + cat_id + '"><img src="' + delete_icon + '" style="width:12px;height:12px;" /></a>&nbsp;' + cat_title + '</span>';
            $("#bsk_pdf_edit_selected_cat_container_ID").append( html );
            $("#bsk_pdf_edit_cat_ids_ID").val( exist_cat_ids_array.join(',') );
        }
    });
    
    $("#bsk_pdf_edit_selected_cat_container_ID").on("click", ".bsk-pdf-edit-delete-cat-anchor", function(){
        var cat_id = $(this).data('catid');
        
        var exist_cat_ids = $("#bsk_pdf_edit_cat_ids_ID").val();
        var exist_cat_ids_array = new Array;
        var new_cat_ids_array = new Array;
        if( exist_cat_ids.length > 0 ){
            exist_cat_ids_array = exist_cat_ids.split(',');
            if( exist_cat_ids_array.length > 0 ){
                for( var i = 0; i < exist_cat_ids_array.length; i++ ){
                    if( exist_cat_ids_array[i] == cat_id ){
                        continue;
                    }
                    new_cat_ids_array.push( exist_cat_ids_array[i] );
                }
            }
        }
        var new_str = new_cat_ids_array.join(',');
        $("#bsk_pdf_edit_cat_ids_ID").val( new_str );
        
        $(this).parent().remove();
    });
    
    /*
      * last modified
      */
    $("#pdf_date_use_file_last_modify_ID").click( function() {
        if( $(this).is(":checked") ){
            $("#pdf_date_id").datepicker( "option", "disabled", true );
        }else{
             $("#pdf_date_id").datepicker( "option", "disabled", false );
        }
    });
    
    $("#bsk_pdf_file_id").change( function( event ){
        if( event.target.files.length < 1 ){
            //uncheck
            $("#pdf_date_use_file_last_modify_ID").prop( "checked", false );
            //set last moifided to null
            $("#bsk_pdfm_lastmodified_text_ID").html( "" );
            $("#bsk_pdfm_lastmodified_val_ID").val( "" );
            //enable datepicker
            $("#pdf_date_id").datepicker( "option", "disabled", false );
            
            return;
        }
        
        //set last modified
        var date = new Date( event.target.files[0].lastModified );
        var date_full = dateFormat( date, 'yyyy-mm-dd HH:MM:ss' );
        var date_new = dateFormat( date, 'yyyy-mm-dd' );
        $("#bsk_pdfm_lastmodified_text_ID").html( date_new );
        $("#bsk_pdfm_lastmodified_val_ID").val( date_full );
    });
    
	$("#bsk_pdf_manager_pdf_save_form").click( function() {
		//check category
		var category = new Array();
		var category_str = $("#bsk_pdf_edit_cat_ids_ID").val();
        category_str = $.trim( category_str );
        if( category_str ){
            category = category_str.split(',');
        }
        if( category.length < 1 ){
            alert( 'Please select at least one category.' );
            return false;
        }
        
		//check title
		var pdf_title = $("#bsk_pdf_manager_pdf_titile_id").val();
		
        if ($.trim(pdf_title) == ''){
            alert('PDF title can not be NULL.');
            $("#bsk_pdf_manager_pdf_titile_id").focus();
            return false;
        }
		
		//php or wordpress uploader
		var is_by_wp_uploader = $("#bsk_pdf_manager_pdf_use_media_uploader_ID").is(':checked');
		
		if( is_by_wp_uploader ){
			if( $("#bsk_pdf_upload_attachment_id_ID").val() < 1 ){
				alert('No file selected');
				$("#bsk_pdf_file_id").focus();
				return false;
			}
		}else{
			//check file
            var old_file = $("#bsk_pdf_manager_pdf_file_old_id").length > 0 && $("#bsk_pdf_manager_pdf_file_old_id").val() ? true : false;
			var file_name = $("#bsk_pdf_file_id").val();
            file_name = $.trim(file_name);
            if (file_name == "" && !old_file ){
                alert('Please select a file to upload.');
                $("#bsk_pdf_file_id").focus();
                return false;
            }
		}
		
		$("#bsk-pdf-manager-pdfs-form-id").submit();
	});
	
	$(".bsk-date").datepicker({
        dateFormat : 'yy-mm-dd'
    });
	
	var uploader_frame;
	
	$('#bsk_pdf_manager_set_featured_image_anchor_ID').click(function( event ){
		event.preventDefault();
		
		if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}
		 
		uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Set featured image",
			button: { text: 'Set featured image' },
			multiple: false
		});
		// open
		uploader_frame.on('open',function() {
			var attachment_id = $("#bsk_pdf_manager_thumbnail_id_ID").val();
			if( attachment_id < 1 ){
				return;
			}
			// set selection
			// set selection
			var selection = uploader_frame.state().get('selection'),
			attachment = wp.media.model.Attachment.get( attachment_id );
			attachment.fetch();
			selection.reset( attachment ? [ attachment ] : [] );
		});
			
		uploader_frame.on( 'select', function() {
			attachment = uploader_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			//alert(attachment.url);
			//alert( attachment.id );
			$("#bsk_pdf_manager_set_featured_image_anchor_ID").html( 'Only support in Pro version' );
		});
		
		uploader_frame.on( 'close', function() {
		});
		 
		// Finally, open the modal
		uploader_frame.open();
	});

	/*Settings*/
	//var uploader_frame;
	
	$('#bsk_pdf_manager_set_default_featured_image_anchor_ID').click(function( event ){
		event.preventDefault();
		
		var uploader_frame;
		/*if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}
		 */
		uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Set default featured image",
			button: { text: 'Set default featured image' },
			multiple: false
		});
		// open
		uploader_frame.on('open',function() {
			var attachment_id = $("#bsk_pdf_manager_default_thumbnail_id_ID").val();
			if( attachment_id < 1 ){
				return;
			}
			// set selection
			var selection	=	wp.media.frame.state().get('selection'),
				 attachment	=	wp.media.attachment( attachment_id );

			// to fetch or not to fetch
			if( $.isEmptyObject(attachment.changed) )
			{
				attachment.fetch();
			}
			selection.add( attachment );
		});
			
		uploader_frame.on( 'select', function() {
			attachment = uploader_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			//alert(attachment.url);
			//alert( attachment.id );
			bsk_pdf_manager_set_default_thumbnail( attachment.id );
		});
		
		uploader_frame.on( 'close', function() {
		});
		 
		// Finally, open the modal
		uploader_frame.open();
	});
	
	function bsk_pdf_manager_set_default_thumbnail( thumbnail_id_to_set ){
		$("#bsk_pdf_manager_set_default_featured_image_ajax_loader_ID").css("display", "inline-block");
		var nonce_val = $("#bsk_pdf_manager_settings_page_ajax_nonce_ID").val();
        var size_val = $("#bsk_pdf_manager_default_thumbnail_size_ID").val();
        var size_dimission = '';
        if( size_val ){
            size_dimission = $("#bsk_pdfm_size_dimission_" + size_val + "_ID" ).val();
        }
		var data = 
				{ 
				  action: 'bsk_pdf_manager_settings_get_default_featured_image', 
				  nonce: nonce_val,
				  thumbnail_id: thumbnail_id_to_set,
                  size: size_val
				};
				
		$.post(ajaxurl, data, function(response) {
			$("#bsk_pdf_manager_set_default_featured_image_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false;
			}
            if( size_dimission ){
                var size_dimission_array = size_dimission.split('_');
                $("#postimagediv .inside").css("width", size_dimission_array[0]);
            }
			$("#bsk_pdf_manager_default_thumbnail_id_ID").val( thumbnail_id_to_set );
			$("#bsk_pdf_manager_set_default_featured_image_anchor_ID").html(response);
            $("#bsk_pdf_manager_set_default_featured_image_anchor_ID").blur();
			$("#bsk_pdf_manger_default_image_icon_container_ID").css("display", "none");
			$("#bsk_pdf_manager_remove_default_featured_image_anchor_ID").css("display", "inline-block");
		});
	}
	
	$("#bsk_pdf_manager_remove_default_featured_image_anchor_ID").click(function(){
		$("#bsk_pdf_manager_default_thumbnail_id_ID").val( "" );
		$("#bsk_pdf_manager_set_default_featured_image_anchor_ID").html( "Change default featured image" );
		$("#bsk_pdf_manger_default_image_icon_container_ID").css("display", "block");
		$("#bsk_pdf_manager_remove_default_featured_image_anchor_ID").css("display", "none");
	});
	
	$("#bsk_pdf_manager_pdfs_categories_change_cancel_id").click(function(){
		$("#bsk_pdf_manager_action_id").val("");
		$("#bsk_pdf_manager_pdfs_change_category_form_id").submit();
	});
	
	$("#bsk_pdf_manager_pdfs_categories_change_submit_id").click(function(){
        
        //check category
		var category = new Array();
		$('input[name="bsk_pdf_manager_pdfs_categories_to_manager[]"]:checked').each(function() {
		   category.push($(this).val());
		});
		if( category.length < 1 ){
            $("#bsk_pdfm_batch_update_category_choose_error_message_ID").css("display", "block");
			$("#bsk_pdfm_batch_update_category_choose_error_message_ID").html( 'Please check at least one category' );
			return false;
		}
        
		$("#bsk_pdf_manager_action_id").val("bulk_change_pdf_category");
		$("#bsk_pdf_manager_pdfs_change_category_form_id").submit();
	});
    
    $(".bsk-pdfm-bactch-update-category-checkbox").click(function(){
        var category = new Array();
        $('input[name="bsk_pdf_manager_pdfs_categories_to_manager[]"]:checked').each(function() {
		   category.push($(this).val());
		});
		if( category.length < 1 ){
            $("#bsk_pdfm_batch_update_category_choose_error_message_ID").css("display", "block");
			$("#bsk_pdfm_batch_update_category_choose_error_message_ID").html( 'Please check at least one category' );
		}else{
            $("#bsk_pdfm_batch_update_category_choose_error_message_ID").css("display", "none");
        }
    });
	
    /*
     * Add by FTP
     */
	$("#bsk_pdf_manager_add_by_ftp_save_button_ID").click(function(){
		//check selected files
		var selected_files = new Array();
		$('input[name="bsk_pdf_manager_ftp_files[]"]:checked').each(function() {
		   selected_files.push($(this).val());
		});
		if( selected_files.length < 1 ){
			alert( 'Please check at least one file' );
			return false;
		}
		
		//check category
		var category = new Array();
		$('input[name="bsk_pdf_manager_ftp_categories[]"]:checked').each(function() {
		   category.push($(this).val());
		});
		if( category.length < 1 ){
			alert( 'Please check at least one category' );
			return false;
		}
		
		$("#bsk-pdf-manager-add-by-ftp-form-id").submit();
	});
	
	
	/* featured image setting */
	$("#bsk_pdf_manager_enable_featured_image_ID").click(function(){
		var is_checked = $(this).prop('checked');
		if ( is_checked ) {
			$("#bsk_pdf_manager_featured_image_settings_containder_ID").css( "display", "block" );
		} else {
			$("#bsk_pdf_manager_featured_image_settings_containder_ID").css( "display", "none" );
		}
	});
	
	$("#bsk_pdf_manager_default_thumbnail_size_ID").change(function(){
		var selected_size = $(this).val();
		if( selected_size == 'full' ){
			$( "#bsk_pdf_manager_dft_size_width_span_ID" ).html( '' );
			$( "#bsk_pdf_manager_dft_size_height_span_ID" ).html( '' );
			$( "#bsk_pdf_manager_dft_size_crop_span_ID" ).html( '' );
			
			return;
		}
		var hidden_value = $( "#bsk_pdfm_size_dimission_" + selected_size + "_ID" ).val();
		var size_values_array = hidden_value.split( '_' );
		
		if( size_values_array.length > 0 ){
			$( "#bsk_pdf_manager_dft_size_width_span_ID" ).html( "Width: " + size_values_array[0] + "px" );
		}
		if( size_values_array.length > 1 ){
			$( "#bsk_pdf_manager_dft_size_height_span_ID" ).html( "Height: " + size_values_array[1] + "px" );
		}
		if( size_values_array.length > 2 ){
			$( "#bsk_pdf_manager_dft_size_crop_span_ID" ).html( "Crop: " + size_values_array[2] );
		}
	});
	
	$("#bsk_pdf_manager_settings_featured_image_tab_save_form_ID").click(function(){
		var register_size_name_1 = $("#bsk_pdf_manager_register_image_size_name_1_ID").val();
		var register_size_width_1 = $("#bsk_pdf_manager_register_image_size_width_1_ID").val();
		var register_size_height_1 = $("#bsk_pdf_manager_register_image_size_height_1_ID").val();
		
		register_size_name_1 = $.trim( register_size_name_1 );
		if( register_size_name_1 ){
			if( register_size_width_1 < 1 ){
				alert( "Invalid width" );
				$("#bsk_pdf_manager_register_image_size_width_1_ID").focus();
				return false;
			}
			if( register_size_height_1 < 1 ){
				alert( "Invalid height" );
				$("#bsk_pdf_manager_register_image_size_height_1_ID").focus();
				return false;
			}
		}else if( register_size_width_1 || register_size_height_1 ){
			alert( "Please enter name" );
			$("#bsk_pdf_manager_register_image_size_name_1_ID").focus();
			return false;
		}
		
		var register_size_name_2 = $("#bsk_pdf_manager_register_image_size_name_2_ID").val();
		var register_size_width_2 = $("#bsk_pdf_manager_register_image_size_width_2_ID").val();
		var register_size_height_2 = $("#bsk_pdf_manager_register_image_size_height_2_ID").val();
		register_size_name_2 = $.trim( register_size_name_2 );
		if( register_size_name_2 ){
			if( register_size_name_2 == register_size_name_1 ){
				alert( "Same size name is not allowed" );
				$("#bsk_pdf_manager_register_image_size_name_2_ID").focus();
				return false;
			}
			if( register_size_width_2 < 1 ){
				alert( "Invalid width" );
				$("#bsk_pdf_manager_register_image_size_width_2_ID").focus();
				return false;
			}
			if( register_size_height_2 < 1 ){
				alert( "Invalid height" );
				$("#bsk_pdf_manager_register_image_size_height_2_ID").focus();
				return false;
			}
		}else if( register_size_width_2 || register_size_height_2 ){
			alert( "Please enter name" );
			$("#bsk_pdf_manager_register_image_size_name_2_ID").focus();
			return false;
		}
		
		var registered_sizes_name = $("#bsk_pdf_manager_registered_size_names_id").val();
		registered_sizes_name_array = registered_sizes_name.split(',');
		
		if( register_size_name_1 && $.inArray(register_size_name_1, registered_sizes_name_array) > -1 ){
			alert( "The size name exist already" );
			$("#bsk_pdf_manager_register_image_size_name_1_ID").focus();
			return false;
		}
		
		if( register_size_name_2 && $.inArray(register_size_name_2, registered_sizes_name_array) > -1 ){
			alert( "The size name exist already" );
			$("#bsk_pdf_manager_register_image_size_name_2_ID").focus();
			return false;
		}

		$("#bsk_pdfm_featured_image_settings_form_ID").submit();
	});
	
	/* general settings */
	$("#bsk_pdf_manager_settings_general_tab_save_form_ID").click(function(){
		$("#bsk_pdfm_general_settings_form_ID").submit();
	});
	
	/* multi-column settings */
	$("#bsk_pdf_manager_settings_styles_save_form_ID").click(function(){
		$("#bsk_pdfm_styles_form_ID").submit();
	});
	
	$("#bsk_pdfm_multi_column_layout_enable_ID").click(function(){
		var is_checked = $(this).is(':checked');
		
		if( is_checked ){
			$("#bks_pdfm_multi_column_enabled_settings_container_ID").css("display", "block");
		}else{
			$("#bks_pdfm_multi_column_enabled_settings_container_ID").css("display", "none");
		}
	});
	
	/* settings tab switch */
	$("#bsk_pdfm_setings_wrap_ID .nav-tab-wrapper a").click(function(){
		//alert( $(this).index() );
		$('#bsk_pdfm_setings_wrap_ID section').hide();
		$('#bsk_pdfm_setings_wrap_ID section').eq($(this).index()).show();
		
		$(".nav-tab").removeClass( "nav-tab-active" );
		$(this).addClass( "nav-tab-active" );
		
		return false;
	});
	//settings target tab
	if( $("#bsk_pdfm_settings_target_tab_ID").length > 0 ){
		var target = $("#bsk_pdfm_settings_target_tab_ID").val();
		if( target ){
			$("#bsk_pdfm_setings_tab-" + target).click();
		}
	}
	/* help tab switch */
	$("#bsk_pdfm_help_wrap_ID .nav-tab-wrapper a").click(function(){
		//alert( $(this).index() );
		$('#bsk_pdfm_help_wrap_ID section').hide();
		$('#bsk_pdfm_help_wrap_ID section').eq($(this).index()).show();
		
		$(".nav-tab").removeClass( "nav-tab-active" );
		$(this).addClass( "nav-tab-active" );
		
		return false;
	});
    $("#bsk_pdfm_help_tab-quick-start").click();
    
	/*
	 * Upload pdf use wordpress Media uploader
	 */
	$("#bsk_pdf_manager_pdf_use_media_uploader_ID").click(function(){
		var is_checked = $(this).is(':checked');
		
		if( is_checked ){
			$("#bsk_pdfm_php_uploader_container_ID").css("display", "none");
			$("#bsk_pdfm_wordpress_uploader_container_ID").css("display", "block");
            $("#pdf_date_description_4_media_library_ID").css("display", "block");
            //hide use file last modified section
            $("#bsk_pdfm_lastmodified_section_ID").css("display", "none");
		}else{
			$("#bsk_pdfm_php_uploader_container_ID").css("display", "block");
			$("#bsk_pdfm_wordpress_uploader_container_ID").css("display", "none");
            $("#pdf_date_description_4_media_library_ID").css("display", "none");
            //show use file last modified section
            $("#bsk_pdfm_lastmodified_section_ID").css("display", "inline-block");
		}
	});
	
	$("#bsk_pdf_manager_upload_pdf_anchor_ID").on("click", function( event ){
        var supported_extension_and_mime = bsk_pdfm_admin.extension_and_mime;
        
		var uploader_frame;
		/*if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}*/
		 
		uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Set PDF Document",
			button: { text: 'Set PDF Document' },
			multiple: false
		});
		// open
		uploader_frame.on('open',function() {
			var attachment_id = $("#bsk_pdf_upload_attachment_id_ID").val();
			if( attachment_id < 1 ){
				return;
			}
			// set selection
			// set selection
			var selection = uploader_frame.state().get('selection'),
			attachment = wp.media.model.Attachment.get( attachment_id );
			attachment.fetch();
			selection.reset( attachment ? [ attachment ] : [] );
		});
			
		uploader_frame.on( 'select', function() {
			$("#bsk_pdf_manager_upload_pdf_anchor_ID").html( "Only support in Pro version" );
		    $("#bsk_pdf_manager_upload_pdf_anchor_ID").toggleClass( 'button-secondary' );
		});
		
		uploader_frame.on( 'close', function() {
		});
		 
		// Finally, open the modal
		uploader_frame.open();
	});
	
    
    /* *
      * Settings page
      */
    $("#bsk_pdfm_organise_by_month_year_ID").click(function(){
        var is_checked = $(this).is(":checked");
        if( is_checked ){
            $("#bsk_pdfm_organise_by_month_year_hint_text_ID").css("display", "none");
        }else{
            $("#bsk_pdfm_organise_by_month_year_hint_text_ID").css("display", "block");
        }
    });
    
    $("#bsk_pdfm_set_upload_folder_ID").click(function(){
        var is_checked = $(this).is(":checked");
        if( is_checked ){
            $("#bsk_pdfm_set_upload_folder_hint_text_ID").css("display", "block");
            $("#bsk_pdfm_set_upload_folder_input_ID").css("display", "block");
            $("#bsk_pdf_upload_folder_tree").css("display", "block");
            if( $("#bsk_pdf_upload_folder_tree_root_label_ID").length > 0 ){
                var node_root = $("#bsk_pdf_upload_folder_tree").jstree().get_node( 'j1_1' );
                $("#bsk_pdf_upload_folder_tree").jstree('rename_node', node_root, $("#bsk_pdf_upload_folder_tree_root_label_ID").val() );
            }
        }else{
            $("#bsk_pdfm_set_upload_folder_hint_text_ID").css("display", "none");
            $("#bsk_pdfm_set_upload_folder_input_ID").css("display", "none");
            $("#bsk_pdf_upload_folder_tree").css("display", "none");
        }
    });
    
    $("#bsk_pdf_upload_folder_tree").on("changed.jstree", function (e, data) {
        if(data.selected.length) {
            if( $("#bsk_pdf_upload_folder_tree_root_relative_path").length > 0 && data.selected[0] == "j1_1" ){
                //for multiple site not Super Admin
                $("#bsk_pdfm_set_upload_folder_path_ID").html( $("#bsk_pdf_upload_folder_tree_root_relative_path").val() );
                $("#bsk_pdfm_set_upload_folder_path_val_ID").val( $("#bsk_pdf_upload_folder_tree_root_relative_path").val() );
            }else{
                $("#bsk_pdfm_set_upload_folder_path_ID").html( data.instance.get_node(data.selected[0]).li_attr.relative_path );
                $("#bsk_pdfm_set_upload_folder_path_val_ID").val( data.instance.get_node(data.selected[0]).li_attr.relative_path );
            }
        }
    });
    $("#bsk_pdf_upload_folder_tree").jstree(
        {
        'core': {
                    'check_callback': true,
                    /// rest of the options...
                 }
        }
    );

    $("#bsk_pdfm_set_upload_folder_sub_ID").keyup( function(){
        //only number & letters
        this.value = this.value.replace(/[^a-zA-z0-9\-]/g, '');
    });
    
    //credit setting
    $("#bsk_pdf_manaer_credit_link_enable_ID").click(function(){
        var is_checked = $(this).is(":checked");
        if( is_checked ){
            $("#bsk_pdf_manager_credit_text_ID").css( "display", "block" );
            $("#bsk_pdf_manager_credit_example_ID").css( "display", "block" );
        }else{
            $("#bsk_pdf_manager_credit_text_ID").css( "display", "none" );
            $("#bsk_pdf_manager_credit_example_ID").css( "display", "none" );
        }
    });
    
    $("#bsk_pdf_manaer_credit_link_text_ID").keyup(function(){
        var link_text = $(this).val();
        link_text = $.trim( link_text );
        if( link_text == "" ){
            link_text = 'PDFs powered by PDF Manager Pro';
        }
        $("#bsk_pdfm_credit_link_ID").html( link_text );
    });
    
    
    /*
      * widget
      */
    
    //control featured image size & PDF title
    $("#wpbody-content").on("click", ".bsk-pdfm-widget-show-thumbnail", function(){
		if( $(this).is(":checked") ){
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-size-p").css( "display", "block" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-with-pdf-title-p").css( "display", "block" );
		}else{
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-size-p").css( "display", "none" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-with-pdf-title-p").css( "display", "none" );
		}
	});
    
    //control PDFs ID input
    $("#wpbody-content").on("click", ".bsk-pdfm-show-all-or-specific-pdfs", function(){
		if( $(this).val() == 'SPECIFIC' ){
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-specific-ids-input-p").css( "display", "block" );
            
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by > option").show();
		}else{
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-specific-ids-input-p").css( "display", "none" );
            //get order by
            if( $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by").val() == 'IDS_SEQUENCE' ){
                $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by > option").each(function(){
                    if( $(this).val() == 'IDS_SEQUENCE' ){
                        $(this).hide();
                    }
                });
                $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by").val( "title" );
            }
		}
	});
    
    $("#wpbody-content").on("keyup", ".bsk-pdfm-ids-input", function(){
        //only number and .
        this.value = this.value.replace(/[^0-9\,]/g, '');
        //first must be number
        this.value = this.value.replace(/^\,/g, '');
        //.. is not allowed
        this.value = this.value.replace(/\,{2,}/g, ',');
    });
    
    $("#wpbody-content").on("focusout", ".bsk-pdfm-ids-input", function(){
        //if the last one is . then remove it number and .
        this.value = this.value.replace(/\,$/g, ''); 
    });
    
    //control date format string & date before title option
    $("#wpbody-content").on("click", ".bsk-pdfm-widget-show-date", function(){
		if( $(this).is(":checked") ){
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-format-p").css( "display", "block" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-before-title-p").css( "display", "block" );
		}else{
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-format-p").css( "display", "none" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-before-title-p").css( "display", "none" );
		}
	});
    
});
