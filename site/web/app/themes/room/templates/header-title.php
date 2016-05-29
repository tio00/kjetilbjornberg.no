<?php
/**
 * The template for displaying Page title and Breadcrumbs
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Get template parameters
$args = room_template_get_args('header_title');
$show_page_title = !isset($args['show_page_title']) || $args['show_page_title'];

// Page (category, tag, archive, author) title
if ( $show_page_title && !is_front_page() && (is_page() || is_category() || is_tag() || is_year() || is_month() || is_day() || is_author() || is_search()) ) {
	// Uncomment the next row if you want display category's image as bachground for the page title area
	$top_bg = ''; //room_get_category_image();
	$top_icon = room_get_category_icon();
	?>
	<div class="top_panel_title<?php echo !empty($top_bg) ? ' top_panel_bg' : ''; ?>"<?php echo !empty($top_bg) ? ' style="background-image:url('.esc_url($top_bg).');"' : ''; ?>>
		<div class="content_wrap">
			<div class="page_title">
				<h1 class="page_caption"><?php
					if (!empty($top_icon)) {
						?><img src="<?php echo esc_url($top_icon); ?>" alt=""><?php
					}
					room_show_blog_title(); 
				?></h1>
				<?php if ( is_category() || is_tag() ) the_archive_description( '<div class="page_description">', '</div>' ); ?>
			</div>
			<div class="breadcrumbs"><?php room_show_breadcrumbs(); ?></div>
		</div>
	</div>
	<?php
}