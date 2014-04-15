<?php
/**
 * Template Name: BETA Location
 *
 * @package Forge Saas
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php $current_map;
		// Set which map is the current map
		if($_GET['map_type']){
			if($_GET['map_type'] == "metrotown"){
				$current_map = "metrotown";
			}elseif ($_GET['map_type'] == "vancouver"){
				$current_map = "vancouver";
			}		
		}else{
			$current_map = "metrotown";
		}

		?>

		<div id="tax-nav">
			<div class="row">
				<div class="large-12 columns">
					<ul>
						<li>
							<a href="?map_type=metrotown" class="<?php echo ($current_map == "metrotown" ? "current" : "" ) ?>">Metrotown</a>
						</li>
						<li>
							<a href="?map_type=vancouver" class="<?php echo ($current_map == "vancouver" ? "current" : "" ) ?>">Metro Vancouver</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<?php $metrotown_map = wp_get_attachment_image_src( get_field('local_map'), 'full' ); ?>
		<?php $vancouver_map  = wp_get_attachment_image_src( get_field('city_map'), 'full' ); ?>
		
		<?php
			$nonce = wp_create_nonce("set_locations");
		?>
		<!-- Home Intro -->
		<div id="location-map" class="intro-section">
			
			<div class="map-wrap">
				<img src="<?php echo ($current_map == "vancouver" ?  $vancouver_map[0] : $metrotown_map[0] ); ?>" class="image-map" alt="Location Map">
			
				<?php if($current_map == "metrotown"): ?><!-- 
					<div class="map-locations">
						<h2>LOCATIONS TO PLACE</h2> -->
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
							$locations = new WP_Query($args); 
							?>
							<?php while ( $locations->have_posts() ) : $locations->the_post(); ?>
							
								<?php $left_position =  get_post_meta( $post->ID, "_left_value", true); ?>
								<?php $top_position =  get_post_meta( $post->ID, "_top_value", true); ?>

								<?php $position_class = ""; 

									if( $left_position > 84 ){
										$position_class .= "far-left ";
									}
									if( $top_position < 15 ){
										$position_class .= "far-top ";
									}
								?>
								<!-- 	<h1>left: <?php echo $left_position; ?></h1>
									<h1>top: <?php echo $top_position; ?></h1> -->
								<div class="beta-single-location single-location <?php echo $position_class; echo $term->slug; ?> count-<?php echo $term_count; ?>"
									data-left-value="<?php echo $left_position ?>" data-top-value="<?php echo $top_position ?>" data-location-id="<?php echo $post->ID; ?>"
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

				<?php elseif($current_map == "vancouver" ): ?>
					<div class="map-locations">
						<a href="?map_type=metrotown" class="to-metrotown">
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div id="location-info">
			<div class="row">
				<div class="large-12 column">
					<h1><?php the_field('locations_title'); ?></h1>

					<div class="text-wrap">
						<?php the_field('locations_content'); ?>
					</div>

					<div class="locations">
						<?php 
						$args = array('orderby'       => 'id', 'order'         => 'ASC');
						$location_terms = get_terms('location-types', $args); ?>
						<ul class="large-block-grid-3">
							<!-- COLUMN ONE -->
							<li>
								<ul class="right-list">
									<?php for($i = 0; $i < 3; $i++): ?>
									<li>
										<h4><?php echo $location_terms[$i]->name; ?></h4>
										<ul class="location-list">
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
													'terms' => $location_terms[$i]->slug,
													'operator' => 'IN'
												)
											)

										); 
										$locations = new WP_Query($args); 
										?>
										<?php $count = 1; ?>
										<?php while ( $locations->have_posts() ) : $locations->the_post(); ?>
											<li>
												<span class="location-count <?php echo $location_terms[$i]->slug; ?>"><?php echo $count++; ?></span>
												<span class="title"><?php the_title(); ?></span>
											</li>
											

										<?php endwhile; wp_reset_query();// end of the loop. ?>
										</ul>

									</li>
									<?php endfor; ?>
								</ul>
							</li>




							<!-- COLUMN TWO -->
							<li>
								<ul class="right-list">
									<?php for($i = 3; $i < 6; $i++): ?>
									<li>
										<h4><?php echo $location_terms[$i]->name; ?></h4>
										<ul class="location-list">
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
													'terms' => $location_terms[$i]->slug,
													'operator' => 'IN'
												)
											)

										); 
										$locations = new WP_Query($args); 
										?>
										<?php $count = 1; ?>
										<?php while ( $locations->have_posts() ) : $locations->the_post(); ?>
											<li>
												<span class="location-count <?php echo $location_terms[$i]->slug; ?>"><?php echo $count++; ?></span>
												
												<span class="title"><?php the_title(); ?></span>
											</li>
											

										<?php endwhile; wp_reset_query();// end of the loop. ?>
										</ul>

									</li>
									<?php endfor; ?>

								</ul>
							</li>



							<!-- COLUMN THREE -->
							<li>
								<ul>

									<?php for($i = 6; $i < 7; $i++): ?>
									<li>
										<h4><?php echo $location_terms[$i]->name; ?></h4>
										<ul class="location-list">
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
													'terms' => $location_terms[$i]->slug,
													'operator' => 'IN'
												)
											)

										); 
										$locations = new WP_Query($args); 
										?>
										<?php $count = 1; ?>
										<?php while ( $locations->have_posts() ) : $locations->the_post(); ?>
											<li>
												<span class="location-count <?php echo $location_terms[$i]->slug; ?>"><?php echo $count++; ?></span>
												
												<span class="title"><?php the_title(); ?></span>
											</li>
											

										<?php endwhile; wp_reset_query();// end of the loop. ?>
										</ul>

									</li>
									<?php endfor;?>
									<?php if(get_field('retailers_content')): ?>
										<li>
											<?php if(get_field('retailers_title')){ echo "<h4>" . get_field('retailers_title'). "</h4>"; } ?>
											<p>
												<?php the_field('retailers_content'); ?>
											</p>
										</li>
									<?php endif; ?>

								</ul>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>

	<?php endwhile; // end of the loop. ?>

		
<?php get_footer(); ?>
