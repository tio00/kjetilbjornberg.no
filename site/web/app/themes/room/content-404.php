<article <?php post_class( 'post_item_single post_item_404' ); ?>>
	<div class="post_content">
		<h1 class="page_title"><?php esc_html_e( '404', 'room' ); ?></h1>
		<h2 class="page_subtitle"><?php esc_html_e('The requested page cannot be found', 'room'); ?></h2>
		<p class="page_description"><?php echo wp_kses_data( sprintf( __("Can't find what you need? Take a moment and do a search below or start from <a href='%s'>our homepage</a>.", 'room'), esc_url(home_url('/')) ) ); ?></p>
		<div class="page_search"><?php get_template_part( 'templates/search-field' ); ?></div>
	</div>
</article>
