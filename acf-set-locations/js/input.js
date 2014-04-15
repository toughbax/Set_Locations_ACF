
jQuery(document).ready(function($){

	//get varaible

	$location_count = $('.single-location').length;
	var $total_width = $('#acf-local_map').width();
	var $per_row = Math.round($total_width / 30);
	var $rows = Math.ceil($location_count / $per_row);

	var $count = 0;
	var $current_row = 0;

	// add top margin for number of rows needed.
	$('#location-map').css('margin-top', $rows * 30 + "px" );


	$('.single-location').each(function(){

		// position locations waiting to be set. 
		var $this = $(this);
		if($count > $per_row -1 ){
			$count = 0;
			$current_row++;
		}
		if(!$this.attr('data-left-value') && !$this.attr('data-top-value')){
			$this.css('top',-30 * $rows + ($current_row * 30 ) );
			$this.css('left', 30 * $count++);
		}
	});
 	

	$('.single-location').draggable({
		containment: ".image-map",

		stop: function( event, ui ) {

			// get px values and convert to a % value for Left and Top
			//left
			var $left = $(this).css("left");
			var $int_left = parseInt($left);
			var $map_width = $('.image-map').width();
			var $percentage_left = ($int_left / $map_width) * 100 ;
			var $rounded_left = Math.round($percentage_left * 100)/100;
			//top
			var $top = $(this).css("top");
			var $int_top = parseInt($top);
			var $map_height = $('.image-map').height();	
			var $percentage_top = ($int_top / $map_height) * 100 ;
			var $rounded_top = Math.round($percentage_top * 100)/100;


			// add class if to close to the top or the right side
			if($rounded_top < 15){
				$(this).addClass('far-top');
			}else{
				$(this).removeClass('far-top');
			}
			if($rounded_left > 85 ){
				$(this).addClass('far-left');
			}else{
				$(this).removeClass('far-left');
			}

			//apply % based position
			$(this).css("left", $rounded_left +"%");
			$(this).css("top", $rounded_top +"%");

			// set data attributes for saving
			$(this).attr('data-left-value',$rounded_left);
			$(this).attr('data-top-value',$rounded_top);
		}
	});



















$('#reset-map').on('click',function(e){
		e.preventDefault();

		var $admin_url = $(this).attr("data-admin-url");
		var nonce = $(this).attr("data-nonce");

		$.ajax({
	        type: 'POST',
	        url: $admin_url,
	        data: {
	        	"action" : "fs_acf_reset_locations", 
		        'nonce': nonce, 
		    },
	        dataType: 'html',
	        success: function(data){
	            if (data) {				 
	            	var $count = 0;
					var $current_row = 0;

					$('#location-map').css('margin-top', $rows * 30 + "px" );

					$('.single-location').each(function(){

						var $this = $(this);

						if($count > $per_row -1 ){
							$count = 0;
							$current_row++;
						}

						if(!$this.attr('data-left-value') && !$this.attr('data-top-value')){
							$this.css('top',-30 * $rows + ($current_row * 30 ) );
							$this.css('left', 30 * $count++);
						}
					});

	            } else {
	            }
	        }
        });	
	});


	$('#save-map').on('click',function(e){
		e.preventDefault();

		var $admin_url = $(this).attr("data-admin-url");
		var nonce = $(this).attr("data-nonce");

		var $values = new Array();

		$('.single-location').each(function(){

			var $this = $(this);
			var $location_id = $(this).attr('data-location-id');
			var $top_value = $(this).attr('data-top-value');
			var $left_value = $(this).attr('data-left-value');

			var location = {id: $location_id, top_value: $top_value, left_value: $left_value };
			$values.push( location );

		});

		$.ajax({
	        type: 'POST',
	        url: $admin_url,
	        data: {
	        	"action" : "fs_acf_set_locations", 
		        'nonce': nonce, 
		        'values' : $values,
		    },
	        dataType: 'html',
	        success: function(data){
	            if (data) {				
	            	// location.reload(); 
	            } else {
	            }
	        }
        });	
	});

});

