

		LG.slider = {
			data : <?php echo json_encode( $slides_array ); ?>
		}

		LG.slider.keyHandler = function(e) {
			if( e.keyCode == 13 ) {
				e.stopPropagation();
				e.preventDefault();
				
				e.target.click();
			}
		}

		LG.slider.change = function(e) {

			if ( e.target.id == '' ) return;

			Array.prototype.forEach.call( document.querySelectorAll( ".slider-controls li" ), function( node ) {
				node.className = '';
			});

			if ( e.target !== e.currentTarget ) {
				var n = e.target.id.slice( -1 );

				document.getElementById( e.target.id ).className += ' active'; 

				document.querySelector( '.slide' ).style.backgroundImage = "<?php echo $gradients; ?> url('" + LG.slider.data[n].image + "')";
				document.querySelector( '.slide .text h1' ).innerHTML = LG.slider.data[n].title;
				document.querySelector( '.slide .text p' ).innerHTML = LG.slider.data[n].text;

			}
		}

		LG.slider.data.theControls = document.querySelector( ".slider-controls" );
		LG.slider.data.theControls.addEventListener( "click", LG.slider.change, false);
		 
