<?php
/**
 * The default template for displaying content of the single post, page or attachment
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post_item_single post_type_'.esc_attr(get_post_type()) 
												. ' post_format_'.esc_attr(str_replace('post-format-', '', get_post_format())) 
												. (room_param_is_on(room_get_theme_option('seo_ready')) ? ' itemscope' : '')
												); ?>
									<?php if (room_param_is_on(room_get_theme_option('seo_ready'))) echo ' itemscope itemtype="http://schema.org/'.esc_attr(is_single() ? 'BlogPosting' : 'Article').'"'; ?>>

	<?php
	if ( is_sticky() && is_home() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}


	if ( !in_array(get_post_format(), array('link', 'aside', 'status', 'quote')) ) {
		?>
		<div class="post_header entry-header">

			<?php the_title( '<h1 class="post_title entry-title"' . (room_param_is_on(room_get_theme_option('seo_ready')) ? ' itemprop="headline"' : '') . '>', '</h1>' ); ?>

			<?php if ( is_single() && !is_attachment() ) {
				$post_author_id   = get_the_author_meta('ID');
				$post_author_name = get_the_author_meta('display_name');
				$post_author_url  = get_author_posts_url($post_author_id, '');
				?>
				<div class="post_info">
					<span class="post_info_item post_info_posted_by">
						<?php echo esc_html__('By ', 'room')
							. ('<a href="' . esc_url($post_author_url) . '" class="post_info_author">')
							. ($post_author_name)
							. ('</a>');
						?>
					</span>
					<span class="post_info_item date updated"<?php if (room_param_is_on(room_get_theme_option('seo_ready'))) echo ' itemprop="datePublished"'; ?>><?php echo get_the_date(); ?></span>
					<div class="post_categories"><?php printf('<span class="cats_label">%s</span>', esc_html__('in ', 'room')); the_category( '' ); ?></div>

					<?php
						// Post counters
						room_template_set_args( 'post_counters', array(
								'class' => 'post_counters_single',
								'share' => false,
								'counters' => room_get_theme_setting('post_counters')
							)
						);
						get_template_part( 'templates/post-counters' );
					?>

				</div>
			<?php } ?>



		</div><!-- .entry-header -->
		<?php
	}

	//get_template_part( 'templates/post-featured' );
	get_template_part( 'templates/post-featured-hot-spot' );
	?>

	<div class="post_content entry-content"<?php if (room_param_is_on(room_get_theme_option('seo_ready'))) echo ' itemprop="articleBody"'; ?>>
		<?php
			the_content( );

			wp_link_pages( array(
				'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'room' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'room' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );
		?>
	</div><!-- .entry-content -->

	<div class="post_footer entry-footer clearfix">
		<?php get_template_part( 'templates/post-footer' ); ?>
	</div><!-- .entry-footer -->

	<?php
		// Author bio.
		if ( is_single() && !is_attachment() && get_the_author_meta( 'description' ) ) {	// && is_multi_author()
			get_template_part( 'templates/author-bio' );
		}
	?>

</article>
