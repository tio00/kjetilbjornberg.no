<?php
if ( is_singular() || is_attachment() ) {
	if ( !is_front_page() ) {
	
		// Single post, page or attachment
		if ( is_single() && !is_attachment() ) {
			?><div class="post_taxes">
				<div class="post_tags"><?php the_tags( '', '' ); ?></div>
			</div>
			<?php
		
			// Post counters and share
			room_template_set_args( 'post_counters', array(
				'class' => 'post_counters_single',
				'share' => true,
				'counters' => ''
				)
			);
			get_template_part( 'templates/post-counters' );
		}
	}
		
} else {
	$blog_style = room_get_theme_option('blog_style');
	// Post item in the blog streampage
	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		if(!$blog_style == 'grid_2'){
		$post_author_id   = get_the_author_meta('ID');
		$post_author_name = get_the_author_meta('display_name');
		$post_author_url  = get_author_posts_url($post_author_id, '');
		?>
		<span class="posted_by">
			<?php echo esc_html__('By ', 'room')
				. ('<a href="' . esc_url($post_author_url) . '" class="post_info_author">')
				. ($post_author_name)
				. ('</a>');
			?>
		</span>
		<?php } ?>
		<span class="post_date"><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo get_the_date(); ?></a></span><?php
	}
	
	// Post counters and share
	room_template_set_args( 'post_counters', array(
		'class' => 'post_counters_blog',
		'share' => false,
		'counters' => 'comments'
		)
	);
	get_template_part( 'templates/post-counters' );

}
?>