<?php
/**
 * The template for displaying Author bios
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
?>

<div class="author_info author vcard"<?php if (room_param_is_on(room_get_theme_option('seo_ready'))) echo ' itemprop="author" itemscope itemtype="http://schema.org/Person"'; ?>>
	<div class="wrap_in">
		<div class="author_avatar"<?php if (room_param_is_on(room_get_theme_option('seo_ready'))) echo ' itemprop="image"'; ?>>
			<?php
			$mult = room_get_retina_multiplier();
			echo get_avatar( get_the_author_meta( 'user_email' ), 56*$mult );
			?>
		</div><!-- .author_avatar -->

		<div class="author_description">
			<h3 class="author_title"<?php if (room_param_is_on(room_get_theme_option('seo_ready'))) echo ' itemprop="name"'; ?>><span class="fn"><?php echo get_the_author(); ?></span></h3>

			<div class="author_bio"<?php if (room_param_is_on(room_get_theme_option('seo_ready'))) echo ' itemprop="description"'; ?>>
				<?php the_author_meta( 'description' ); ?>
				<a class="author_link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
					<?php printf( esc_html__( 'View all posts by %s', 'room' ), get_the_author() ); ?>
				</a>
			</div><!-- .author_bio -->

		</div><!-- .author_description -->
	</div>
</div><!-- .author_info -->
