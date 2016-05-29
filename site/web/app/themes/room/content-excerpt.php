<?php
/**
 * The default template for displaying content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
$post_format = get_post_format();
$post_format = empty($post_format) ? 'standard' : str_replace('post-format-', '', $post_format);
$animation = room_get_theme_option('blog_animation');
$blog_style = room_get_theme_option('blog_style');
?>

<article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_excerpt post_format_'.esc_attr($post_format) ); ?>
	<?php echo (!room_param_is_off($animation) ? ' data-animation="'.esc_attr(room_get_animation_classes($animation)).'"' : ''); ?>
	>

	<?php
	if ( is_sticky() && is_home() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}


	if ( !in_array($post_format, array('link', 'aside', 'status', 'quote')) ) {
		?>
		<div class="post_header entry-header">
			<?php the_title( sprintf( '<h2 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			<div class="post_categories"><?php the_category( ' ' ); ?></div>
		</div><!-- .entry-header -->
		<?php
	}

	if ($blog_style == 'excerpt') {
		get_template_part('templates/post-featured-hot-spot');
	} else {
		get_template_part('templates/post-featured');
	}

	?>

	<div class="post_content entry-content">
		<?php
			if (room_get_theme_option('blog_content') == 'excerpt') {
					$show_learn_more = !in_array($post_format, array('link', 'aside', 'status', 'quote'));
					$show_learn_more = false;
					if (has_excerpt()) {
						the_excerpt();
					} else if (strpos(get_the_content('!--more'), '!--more')!==false) {
						the_content(esc_html__('Learn more', 'room'));
						$show_learn_more = false;
					} else {
						the_excerpt();
					}
					if ($show_learn_more) {
						?><p><a class="more-link"
						        href="<?php echo esc_url(get_permalink()); ?>"><?php esc_html_e('Learn more', 'room'); ?></a>
						</p><?php
					}
			} else {
				the_content( esc_html__( 'Learn more', 'room' ) );
				wp_link_pages( array(
					'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'room' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'room' ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				) );
			}

		?>
	</div><!-- .entry-content -->

	<div class="post_footer entry-footer">
		<?php get_template_part( 'templates/post-footer' ); ?>
	</div><!-- .entry-footer -->

</article>
