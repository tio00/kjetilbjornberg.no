<?php
if ( !post_password_required() ) {	// && !is_attachment() ) {

	$show_hot_spot = true;
	$post_format = str_replace('post-format-', '', get_post_format());

	$hot_spot_out = '';
	$hot_spot_info_out = '';

	if(has_post_thumbnail() && $show_hot_spot && !in_array($post_format, array('audio', 'video')) || is_singular()) {
		// Hot Spot
		if ($show_hot_spot) {
			// Hot Spot 1
			$hot_spot_left = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_left', true);
			$hot_spot_top = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_top', true);
			$hot_spot_des = get_post_meta(get_the_ID(), 'room_options_hot_spot_des', true);
			$hot_spot_class = 'hot_spot_' . str_replace('.', '', mt_rand());
			if ($hot_spot_left && $hot_spot_top && $hot_spot_des) {
				$hot_spot_out .= '<span class="point hot_spot_hover" data-class="' . esc_attr($hot_spot_class) . '" style="left:' . esc_attr((int)$hot_spot_left) . '%;top:' . esc_attr((int)$hot_spot_top) . '%;">1</span>';
				$hot_spot_info_out .= '<div class="hot_spot_item hot_spot_hover" data-class="' . esc_attr($hot_spot_class) . '"><span class="point">1</span>' . trim($hot_spot_des) . '</div>';
			}
			// Hot Spot 2
			$hot_spot_left_2 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_left_2', true);
			$hot_spot_top_2 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_top_2', true);
			$hot_spot_des_2 = get_post_meta(get_the_ID(), 'room_options_hot_spot_des_2', true);
			$hot_spot_class_2 = 'hot_spot_' . str_replace('.', '', mt_rand());
			if ($hot_spot_left_2 && $hot_spot_top_2 && $hot_spot_des_2) {
				$hot_spot_out .= '<span class="point hot_spot_hover" data-class="' . esc_attr($hot_spot_class_2) . '" style="left:' . esc_attr((int)$hot_spot_left_2) . '%;top:' . esc_attr((int)$hot_spot_top_2) . '%;">2</span>';
				$hot_spot_info_out .= '<div class="hot_spot_item hot_spot_hover" data-class="' . esc_attr($hot_spot_class_2) . '"><span class="point">2</span>' . trim($hot_spot_des_2) . '</div>';
			}
			// Hot Spot 3
			$hot_spot_left_3 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_left_3', true);
			$hot_spot_top_3 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_top_3', true);
			$hot_spot_des_3 = get_post_meta(get_the_ID(), 'room_options_hot_spot_des_3', true);
			$hot_spot_class_3 = 'hot_spot_' . str_replace('.', '', mt_rand());
			if ($hot_spot_left_3 && $hot_spot_top_3 && $hot_spot_des_3) {
				$hot_spot_out .= '<span class="point hot_spot_hover" data-class="' . esc_attr($hot_spot_class_3) . '" style="left:' . esc_attr((int)$hot_spot_left_3) . '%;top:' . esc_attr((int)$hot_spot_top_3) . '%;">3</span>';
				$hot_spot_info_out .= '<div class="hot_spot_item hot_spot_hover" data-class="' . esc_attr($hot_spot_class_3) . '"><span class="point">3</span>' . trim($hot_spot_des_3) . '</div>';
			}
			// Hot Spot 4
			$hot_spot_left_4 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_left_4', true);
			$hot_spot_top_4 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_top_4', true);
			$hot_spot_des_4 = get_post_meta(get_the_ID(), 'room_options_hot_spot_des_4', true);
			$hot_spot_class_4 = 'hot_spot_' . str_replace('.', '', mt_rand());
			if ($hot_spot_left_4 && $hot_spot_top_4 && $hot_spot_des_4) {
				$hot_spot_out .= '<span class="point hot_spot_hover" data-class="' . esc_attr($hot_spot_class_4) . '" style="left:' . esc_attr((int)$hot_spot_left_4) . '%;top:' . esc_attr((int)$hot_spot_top_4) . '%;">4</span>';
				$hot_spot_info_out .= '<div class="hot_spot_item hot_spot_hover" data-class="' . esc_attr($hot_spot_class_4) . '"><span class="point">4</span>' . trim($hot_spot_des_4) . '</div>';
			}
			// Hot Spot 5
			$hot_spot_left_5 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_left_5', true);
			$hot_spot_top_5 = (int)get_post_meta(get_the_ID(), 'room_options_hot_spot_top_5', true);
			$hot_spot_des_5 = get_post_meta(get_the_ID(), 'room_options_hot_spot_des_5', true);
			$hot_spot_class_5 = 'hot_spot_' . str_replace('.', '', mt_rand());
			if ($hot_spot_left_5 && $hot_spot_top_5 && $hot_spot_des_5) {
				$hot_spot_out .= '<span class="point hot_spot_hover" data-class="' . esc_attr($hot_spot_class_5) . '" style="left:' . esc_attr((int)$hot_spot_left_5) . '%;top:' . esc_attr((int)$hot_spot_top_5) . '%;">5</span>';
				$hot_spot_info_out .= '<div class="hot_spot_item hot_spot_hover" data-class="' . esc_attr($hot_spot_class_5) . '"><span class="point">5</span>' . trim($hot_spot_des_5) . '</div>';
			}
		}
	}


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


			if ($hot_spot_out && $hot_spot_info_out && !in_array($post_format, array('audio', 'video'))) {
				?>
				<div class="post_featured width_hot_spot">
					<div class="hot_spot"><?php echo trim($hot_spot_out); ?></div>
					<?php
					$image_atts = array('alt' => get_the_title());
					if (room_param_is_on(room_get_theme_option('seo_ready')))
					$image_atts['itemprop'] = 'image';
					the_post_thumbnail('full', $image_atts);
					?>
				</div>
				<div class="hot_spot_info"><?php echo trim($hot_spot_info_out); ?></div>
				<?php
			} else { ?>
				<div class="post_featured">
					<?php
					get_template_part('templates/pinit');
					$image_atts = array('alt' => get_the_title());
					if (room_param_is_on(room_get_theme_option('seo_ready')))
						$image_atts['itemprop'] = 'image';
					the_post_thumbnail('big', $image_atts);
					?>
				</div><!-- .post_featured -->
			<?php
			}
		}

	} else {

		$args = room_template_get_args('post_featured');
		$post_info = isset($args['post_info']) ? $args['post_info'] : '';


		if ( has_post_thumbnail() ) {

			if($hot_spot_out && !in_array($post_format, array('audio', 'video'))) { ?>
				<div class="post_featured width_hot_spot">
					<div class="hot_spot"><?php echo trim($hot_spot_out); ?></div>
					<a href="<?php the_permalink(); ?>"
					   aria-hidden="true"><?php the_post_thumbnail('full', array('alt' => get_the_title())); ?></a>
				</div>
				<?php if($hot_spot_info_out){ ?>
					<div class="hot_spot_info"><?php echo trim($hot_spot_info_out); ?></div>
				<?php }
			}

			else { ?>
				<div class="post_featured">
					<?php
					if (!in_array($post_format, array('audio', 'video')) && (!isset($args['pin_it']) || $args['pin_it'])) get_template_part('templates/pinit');
					$thumb_size = isset($args['thumb_size']) ? $args['thumb_size'] : room_get_thumb_size('big');
					?>
					<a href="<?php the_permalink(); ?>"
					   aria-hidden="true"><?php the_post_thumbnail($thumb_size, array('alt' => get_the_title())); ?></a>
					<?php
					// Post formats over thumb
					if ($post_format == 'audio') {
						$audio = room_get_post_audio('', false);
						if (empty($audio))
							$audio = room_get_post_iframe('', false);
						if (!empty($audio)) {
							?>
							<div class="post_audio"><?php echo trim($audio); ?></div><?php
						}
					}
					echo trim($post_info);
					?>
				</div>
			<?php
			}

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
				?><div class="post_featured"><?php echo trim($video); ?><?php echo trim($post_info); ?></div><?php
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
					}
				}
				?><div class="post_featured"><?php echo trim($audio); ?><?php echo trim($post_info); ?></div><?php
			}
		}

	}
}
?>