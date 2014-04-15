<?php

class acf_field_set_locations extends acf_field
{
	
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'set_locations';
		$this->label = __("Set Locations",'acf');
		$this->category = __("Content",'acf');
		// $this->defaults = array(
		// 	'save_format'	=>	'object',
		// 	'preview_size'	=>	'thumbnail',
		// 	'library'		=>	'all'
		// );
		// $this->l10n = array(
		// 	'select'		=>	__("Select Image",'acf'),
		// 	'edit'			=>	__("Edit Image",'acf'),
		// 	'update'		=>	__("Update Image",'acf'),
		// 	'uploadedTo'	=>	__("uploaded to this post",'acf'),
		// );



		
		
		// do not delete!
    	parent::__construct();


    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.1.1'
		);

	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// register acf scripts
		wp_register_script( 'acf-input-set-locations', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version']);
		wp_register_script( 'acf-ajax-set-locations', $this->settings['dir'] . 'js/ajax.js', array('acf-input'), $this->settings['version']);
		wp_register_style( 'acf-input-set-locations', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] ); 
		
		
		// scripts
		wp_enqueue_script(array(
			'acf-input-set-locations',	
			'acf-ajax-set-locations',	
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-set-locations',	
		));
		
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// vars
		$o = array(
			'class'		=>	'',
			'url'		=>	'',
		);
		
		if( $field['value'] && is_numeric($field['value']) )
		{
			$url = wp_get_attachment_image_src($field['value'], $field['preview_size']);
			
			$o['class'] = 'active';
			$o['url'] = $url[0];
		}
		
		?>

		<?php
			$nonce = wp_create_nonce("set_locations");
		?>
		<!-- Home Intro -->
		<div id="location-map" class="intro-section">
			
			<div class="map-wrap">
				<div id="map-locations">
					<?php $location_terms = get_terms('location-types'); ?>
					<?php foreach($location_terms as $term): ?>
						<?php $term_count = 1; ?>
						<?php 
						$args = array(
							'post_type' => 'locations', 
							'posts_per_page' => -1,
							'orderby' => 'menu_order',
							'order' => 'ASC',
							'tax_query' => array(
								array(
									'taxonomy' => 'location-types',
									'field' => 'slug',
									'terms' => $term->slug,
									'operator' => 'IN'
								)
							)

						); 
						$location_count = 0;
						$locations = new WP_Query($args); 
						?>
						<?php while ( $locations->have_posts() ) : $locations->the_post(); ?>

							<?php $post_id = $locations->posts[$location_count++]->ID; ?>
						
							<?php $left_position =  get_post_meta( $post_id, "_left_value", true); ?>
							<?php $top_position =  get_post_meta( $post_id, "_top_value", true); ?>

							<?php $position_class = ""; 

								if( $left_position > 84 ){
									$position_class .= "far-left ";
								}
								if( $top_position < 15 ){
									$position_class .= "far-top ";
								}

							?>
								<!-- <h1>left: <?php echo $left_position; ?></h1>
								<h1>top: <?php echo $top_position; ?></h1> -->
							<div class="beta-single-location single-location <?php echo $position_class; echo $term->slug; ?> count-<?php echo $term_count; ?>"
								data-left-value="<?php echo $left_position ?>" data-top-value="<?php echo $top_position ?>" data-location-id="<?php echo $post_id; ?>"
								style="<?php if($left_position){echo "left:$left_position%;position:absolute;";} if($top_position){echo "top:$top_position%;position:absolute;";} ?>"
							>
								<span class="location-count <?php echo $term->slug; ?>"><?php echo $term_count++; ?></span>
								<div class="roll-over <?php echo $term->slug; ?> <?php if(get_field('display_thumbnails') == TRUE ){ echo "has-thumb"; } ?>">
									<?php if(get_field('display_thumbnails') == TRUE ): ?>
										<?php the_post_thumbnail('thumbnail'); ?>
									<?php endif; ?>
									<h3><?php the_title(); ?></h3>
								</div>
							</div>				
						<?php endwhile; // end of the loop. ?>
					<?php endforeach; wp_reset_query();?>
				</div>
				
				<img src="" id="fs_image_map" style="max-width:100%;" class="image-map" alt="Location Map">
				
				<script>
					jQuery(document).ready(function($){
						var $imagesrc = $('#acf-local_map .acf-image-image').attr('src');
						
						$('#fs_image_map').attr('src',$imagesrc);
					});
				</script>


			</div>
			<div class="row">
				<div class="large-12 columns">								
					<div>
						<a 
							href="#" 
							class="button" 
							id="save-map"
							class="save-map"
							data-nonce="<?php echo $nonce  ?>"
							data-admin-url='<?php echo admin_url( "admin-ajax.php" ) ?>'
						>
							Save Map
						</a>
						<a 
							href="#" 
							class="button" 
							id="reset-map"
							class="reset-map"
							data-nonce="<?php echo $nonce  ?>"
							data-admin-url='<?php echo admin_url( "admin-ajax.php" ) ?>'
						>
							Reset Map
						</a>
					</div>
				</div>
			</div>
		</div>



		<?php
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		
		// validate
		if( !$value )
		{
			return false;
		}
		
		
		// format
		if( $field['save_format'] == 'url' )
		{
			$value = wp_get_attachment_url( $value );
		}
		elseif( $field['save_format'] == 'object' )
		{
			$attachment = get_post( $value );
			
			
			// validate
			if( !$attachment )
			{
				return false;	
			}
			
			
			// create array to hold value data
			$src = wp_get_attachment_image_src( $attachment->ID, 'full' );
			
			$value = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'mime_type'	=> $attachment->post_mime_type,
				'url' => $src[0],
				'width' => $src[1],
				'height' => $src[2],
				'sizes' => array(),
			);
			
			
			// find all image sizes
			$image_sizes = get_intermediate_image_sizes();
			
			if( $image_sizes )
			{
				foreach( $image_sizes as $image_size )
				{
					// find src
					$src = wp_get_attachment_image_src( $attachment->ID, $image_size );
					
					// add src
					$value[ 'sizes' ][ $image_size ] = $src[0];
					$value[ 'sizes' ][ $image_size . '-width' ] = $src[1];
					$value[ 'sizes' ][ $image_size . '-height' ] = $src[2];
				}
				// foreach( $image_sizes as $image_size )
			}
			// if( $image_sizes )
			
		}
		
		return $value;
		
	}
	
	
	/*
	*  get_media_item_args
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 27/01/13
	*/
	
	function get_media_item_args( $vars )
	{
	    $vars['send'] = true;
	    return($vars);
	}
	
	
	/*
   	*  ajax_get_images
   	*
   	*  @description: 
   	*  @since: 3.5.7
   	*  @created: 13/01/13
   	*/
	
   	function ajax_get_images()
   	{
   		// vars
		$options = array(
			'nonce' => '',
			'images' => array(),
			'preview_size' => 'thumbnail'
		);
		$return = array();
		
		
		// load post options
		$options = array_merge($options, $_POST);
		
		
		// verify nonce
		if( ! wp_verify_nonce($options['nonce'], 'acf_nonce') )
		{
			die(0);
		}
		
		
		if( $options['images'] )
		{
			foreach( $options['images'] as $id )
			{
				$url = wp_get_attachment_image_src( $id, $options['preview_size'] );
				
				
				$return[] = array(
					'id' => $id,
					'url' => $url[0],
				);
			}
		}
		
		
		// return json
		echo json_encode( $return );
		die;
		
   	}
   		
	
	/*
	*  image_size_names_choose
	*
	*  @description: 
	*  @since: 3.5.7
	*  @created: 13/01/13
	*/
	
	function image_size_names_choose( $sizes )
	{
		global $_wp_additional_image_sizes;
			
		if( $_wp_additional_image_sizes )
		{
			foreach( $_wp_additional_image_sizes as $k => $v )
			{
				$title = $k;
				$title = str_replace('-', ' ', $title);
				$title = str_replace('_', ' ', $title);
				$title = ucwords( $title );
				
				$sizes[ $k ] = $title;
			}
			// foreach( $image_sizes as $image_size )
		}
		
        return $sizes;
	}
	
	
	/*
	*  wp_prepare_attachment_for_js
	*
	*  @description: This sneaky hook adds the missing sizes to each attachment in the 3.5 uploader. It would be a lot easier to add all the sizes to the 'image_size_names_choose' filter but then it will show up on the normal the_content editor
	*  @since: 3.5.7
	*  @created: 13/01/13
	*/
	
	function wp_prepare_attachment_for_js( $response, $attachment, $meta )
	{
		// only for image
		if( $response['type'] != 'image' )
		{
			return $response;
		}
		
		
		// make sure sizes exist. Perhaps they dont?
		if( !isset($meta['sizes']) )
		{
			return $response;
		}
		
		
		$attachment_url = $response['url'];
		$base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );
		
		if( isset($meta['sizes']) && is_array($meta['sizes']) )
		{
			foreach( $meta['sizes'] as $k => $v )
			{
				if( !isset($response['sizes'][ $k ]) )
				{
					$response['sizes'][ $k ] = array(
						'height'      =>  $v['height'],
						'width'       =>  $v['width'],
						'url'         => $base_url .  $v['file'],
						'orientation' => $v['height'] > $v['width'] ? 'portrait' : 'landscape',
					);
				}
			}
		}

		return $response;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// array?
		if( is_array($value) && isset($value['id']) )
		{
			$value = $value['id'];	
		}
		
		// object?
		if( is_object($value) && isset($value->ID) )
		{
			$value = $value->ID;
		}
		
		return $value;
	}
	
	
}

new acf_field_set_locations();

?>