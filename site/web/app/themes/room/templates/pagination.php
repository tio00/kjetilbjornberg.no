<?php
// Pagination
if (room_get_theme_option('blog_pagination')=='pages') {
	the_posts_pagination( array(
		'mid_size'  => 2,
		'prev_text' => esc_html__( '<', 'room' ),
		'next_text' => esc_html__( '>', 'room' ),
		'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'room' ) . ' </span>',
	) );
} else {
	if(!function_exists('blog_pagination_show')){
		function blog_pagination_show(){
			global $wp_query;
			?>
			<div class="nav-links-old">
				<span class="nav-prev"><?php previous_posts_link( '<span class="icon-left"></span> ' . esc_html__('Previous posts', 'room') ); ?></span>
				<span class="nav-next"><?php next_posts_link( esc_html__('Next posts', 'room') . ' <span class="icon-right"></span>', $wp_query->max_num_pages ); ?></span>
			</div>
		<?php
		}
	}
	blog_pagination_show();
}
?>