<?php
/**
 * The template for displaying "Header 1"
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Site logo

$logo_image = '';
$logo_height = (int)room_get_theme_option('logo_height');
if (/*room_get_theme_option("retina_ready")=='retina' && */(int) room_get_value_gpc('room_retina', 0) > 0)
	$logo_image = room_get_theme_option( 'logo_retina' );
if (empty($logo_image))
	$logo_image = room_get_theme_option( 'logo' );
$logo_text   = room_prepare_macros( '' . get_bloginfo( 'name' ) . '' );
if (!empty($logo_image) || !empty($logo_text)) {
	$header_image = get_header_image();
	$header_css = $header_image!=''
			? ' style="background: url('.esc_url($header_image).') repeat center"'
			: '';
?>
<div class="top_panel_logo_wrap" <?php echo trim($header_css); ?>>
	<div class="content_wrap_top top_panel_logo logo_wrap">
		<div class="logo">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php
				if (!empty($logo_image)) {
					$attr = room_getimagesize($logo_image);
					echo '<img '.($logo_height > 5 ? 'style="max-height:'.esc_attr($logo_height).'px;" ' : '').'src="'.esc_url($logo_image).'" class="logo_main" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>' ;
				} else {
					echo !empty($logo_text)
						? '<div class="logo_text">'.trim($logo_text).'</div>'
						: '';
				}
			?></a>
		</div>
		<div class="search_and_socials">
			<?php
			// Social icons
			if ( ($output = room_get_socials_links()) != '') {
				?><div class="socials_wrap"><?php echo trim($output); ?></div><?php
			}
			?>
			<div class="top_panel_buttons">
				<?php
				// Search field
				get_template_part( 'templates/main-cart' );
				// Cart
				get_template_part( 'templates/search-field' );
				?>
			</div>
		</div>
	</div>
</div>
<?php
}

// Navigation panel
get_template_part( 'templates/header-navi' );

// Header widgets area
get_template_part( 'templates/header-widgets' );
?>