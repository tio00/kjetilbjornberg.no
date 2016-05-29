<?php if ( !function_exists('room_show_related_posts') ) {
	function room_show_related_posts($args=array())	{
		$post_categories_ids = array();
		$post_cats = get_the_category(get_the_ID());
		if (is_array($post_cats) && !empty($post_cats)) {
			foreach ($post_cats as $cat) {
				$post_categories_ids[] = $cat->cat_ID;
			}
		}

		$args = array(
			'numberposts' => 3,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
			'post__not_in' => array(get_the_id()),
			'category__in' => $post_categories_ids
		);

		$recent_posts = wp_get_recent_posts($args, OBJECT);
		if (is_array($recent_posts) && count($recent_posts) > 0) {
			?>
			<section class="related_wrap">
				<h3 class="related_wrap_title"><?php esc_html_e('You May Also Like', 'room'); ?></h3>

				<div class="columns_wrap">
					<?php
					global $post;
					foreach ($recent_posts as $post) {
						setup_postdata($post);
						// Thumb image
						$thumb_image = has_post_thumbnail()
							? wp_get_attachment_image(get_post_thumbnail_id(), 'room-thumb-small')
							: '<img src="' . esc_url(room_get_file_url('images/no_img.jpg')) . '" alt="">';
						$link = get_permalink();
						?><div class="column-1_3"><div class="related_item post_item">
							<?php
							if (!empty($thumb_image)) {
								?>
								<div class="post_featured">
									<a href="<?php echo esc_url($link); ?>"><?php echo trim($thumb_image); ?></a>
								</div>
							<?php } ?>
							<div class="post_header entry-header">
								<h5 class="post_title entry-title"><a
										href="<?php echo esc_url($link); ?>"><?php echo the_title(); ?></a></h5>
							</div>
							<div class="post_footer entry-footer">
								<?php
								if (in_array(get_post_type(), array('post', 'attachment'))) {
									?><span class="post_date"><a
										href="<?php echo esc_url($link); ?>"><?php echo get_the_date(); ?></a>
									</span><?php
								}
								?>
								<span class="post_counters">
								<?php
								// Comments
								if (have_comments() || comments_open()) {
									$post_comments = get_comments_number();
									?>
									<a href="<?php echo esc_url(get_comments_link()); ?>"
									   class="post_counters_item post_counters_comments icon-comment-new"><span
											class="post_counters_number"><?php echo intval($post_comments); ?></span></a>
								<?php
								}
								?>							
							</span>
							</div>
							<!-- .entry-footer -->
						</div></div><?php }	wp_reset_postdata(); ?></div></section><?php }}}room_show_related_posts();?>