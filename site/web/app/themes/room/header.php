<?php
/**
 * The Header: logo and main menu
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1<?php if (room_get_theme_option('responsive_layouts') == 'yes') echo ', maximum-scale=1'; ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php
	if ( !function_exists('has_site_icon') || !has_site_icon() ) {
		if (($favicon=room_get_theme_option('favicon'))!='') {
			?><link rel="shortcut icon" type="image/x-icon" href="<?php echo esc_url($favicon); ?>"><?php
		}
	}
	if (floatval(get_bloginfo('version')) < "4.1") {
		?><title><?php wp_title( '|', true, 'right' ); ?></title><?php
	}
	if (($preloader=room_get_theme_option('page_preloader'))!='') {
		?>
	   	<style type="text/css">
   		<!--
			#page_preloader { background-image:url(<?php echo esc_url($preloader); ?>); background-position:center; background-repeat:no-repeat; position:fixed; z-index:1000000; left:0; top:0; right:0; bottom:0; opacity: 0.8; }
	   	-->
   		</style>
   		<?php
   	}
	?>
	<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php
	$blog_mode = room_detect_blog_mode();
	$body_style = room_get_theme_option('body_style');
	$expand_content = room_get_theme_option('expand_content');
	$sidebar_position = room_get_theme_option('sidebar_position');
	$color_scheme = room_get_theme_option('color_scheme');

	$bebas = '';
	if (count($theme_fonts = room_storage_get('theme_fonts')) > 0) {
		foreach ($theme_fonts as $tag => $font) {
			if (!empty($font['css'])) {
				if (preg_match("/bebas_neuebold/", $font['family'])) {
					$color_scheme = room_get_theme_option('color_scheme');
					$bebas = 'babas ';
					break;
				}
			}
		}
	}
	body_class(
		$bebas
		. 'body_style_' . esc_attr($body_style)
		. ' blog_mode_' . esc_attr($blog_mode)
		. esc_attr(in_array($blog_mode, array('post', 'page')) ? ' is_single' : ' is_stream')
		. ' scheme_' . esc_attr($color_scheme)
		. (room_get_theme_option('page_preloader')!='' ? ' preloader' : '')
		. (room_sidebar_present() ? ' sidebar_show sidebar_' . esc_attr($sidebar_position) : ' sidebar_hide' . esc_attr(room_param_is_on($expand_content) ? ' expand_content' : ''))
	);
	?>
>
	<?php 
	// Page preloader
	if ($preloader!='') {
		?><div id="page_preloader"></div><?php
	}
	?>

	<?php do_action( 'before' ); ?>

	<div class="body_wrap">

		<div class="page_wrap">

			<?php
			get_template_part( 'templates/'.room_get_theme_option("header_style"));
			get_template_part( 'templates/header-title');
			?>

			<div class="page_content_wrap<?php echo !empty($top_bg) ? ' no_top_padding' : ''; ?>">

				<?php if ($body_style!='fullscreen') { ?>
				<div class="content_wrap">
				<?php } ?>

					<?php
					// Widgets area above page content
					room_create_widgets_area('widgets_above_page');
					?>				

					<div class="content">
						<?php
						// Widgets area inside page content
						room_create_widgets_area('widgets_above_content');
						?>				
