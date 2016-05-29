<?php
if ( !post_password_required() ) {	// && !is_attachment() ) {
	
	if ( is_singular() && strpos(room_last_state(), 'trx_')===false ) {

		if ( is_attachment() ) {
			?>
			<div class="post_featured post_attachment">
				<?php
				get_template_part('templates/pinit');
				echo wp_get_attachment_image( get_the_ID(), room_get_thumb_size('full') );
				?>
				
				<nav id="image-navigation" class="navigation image-navigation">
					<div class="nav-previous"><?php previous_image_link( false, '' ); ?></div>
					<div class="nav-next"><?php next_image_link( false, '' ); ?></div>
				</nav><!-- .image-navigation -->
			
			</div><!-- .post_featured -->
			
			<?php
			if ( has_excerpt() ) {
				?><div class="entry-caption"><?php the_excerpt(); ?></div><!-- .entry-caption --><?php
			}

		} else if ( has_post_thumbnail() ) {
			?>
			<div class="post_featured">
				<?php
				get_template_part('templates/pinit');
				$image_atts = array('alt' => get_the_title());
				if ( room_param_is_on(room_get_theme_option('seo_ready')) )
					$image_atts['itemprop'] = 'image';
				the_post_thumbnail('big', $image_atts);
				?>
			</div><!-- .post_featured -->
			<?php
		}

	} else {

		$post_format = str_replace('post-format-', '', get_post_format());
		$args = room_template_get_args('post_featured');
		$post_info = isset($args['post_info']) ? $args['post_info'] : '';
		if ( has_post_thumbnail() ) {
			?><div class="post_featured">
				<?php 
				if (!in_array($post_format, array('audio', 'video')) && (!isset($args['pin_it']) || $args['pin_it'])) get_template_part('templates/pinit');
				$thumb_size = isset($args['thumb_size']) ? $args['thumb_size'] : room_get_thumb_size('big');
				?>
				<a href="<?php the_permalink(); ?>" aria-hidden="true"><?php the_post_thumbnail( $thumb_size, array( 'alt' => get_the_title() ) ); ?></a>
				<?php
				// Post formats over thumb
				if ($post_format == 'audio') {
					$audio = room_get_post_audio('', false);
					if (empty($audio))
						$audio = room_get_post_iframe('', false);
					if (!empty($audio)) {
						?><div class="post_audio"><?php echo trim($audio); ?></div><?php
					}
				}
				echo trim($post_info);
				?>
			</div><?php

		} else if ($post_format == 'gallery') {
			if ( ($output = room_build_slider_layout(array('thumb_size' => isset($args['thumb_size']) ? $args['thumb_size'] : room_get_thumb_size('big')))) != '' ) {
				room_enqueue_slider();
				?><div class="post_featured"><!-- <a href="<?php the_permalink(); ?>" aria-hidden="true"> -->
					<?php echo trim($output); ?>
				<!-- </a> --><?php
				echo trim($post_info);
				?></div><?php
			}

		} else if ($post_format == 'image') {
			$image = room_get_post_image();
			if (!empty($image)) {
				?><div class="post_featured"><a href="<?php the_permalink(); ?>" aria-hidden="true"><img src="<?php echo esc_url(room_clear_thumb_sizes($image)); ?>" alt="<?php echo get_the_title(); ?>"></a><?php echo trim($post_info); ?></div><?php
			}

		} else if ($post_format == 'video') {
			$video = room_get_post_video('', false);
			if (empty($video))
				$video = room_get_post_iframe('', false);
			if (!empty($video)) {
				?><div class="post_featured"><?php echo trim($video); ?></a><?php echo trim($post_info); ?></div><?php
			}

		} else if ($post_format == 'audio') {
			$audio = room_get_post_audio('', false);
			if (empty($audio))
				$audio = room_get_post_iframe('', false);
			if (!empty($audio)) {
				if (false && function_exists('wp_read_audio_metadata')) {
					$src = room_get_post_audio($audio);
					$uploads = wp_upload_dir();
					if (strpos($src, $uploads['baseurl'])!==false) {
						$metadata = wp_read_audio_metadata( $src );
						//dco($metadata);
					}
				}
				?><div class="post_featured"><?php echo trim($audio); ?></a><?php echo trim($post_info); ?></div><?php
			}
		}
		
	}
}
?>