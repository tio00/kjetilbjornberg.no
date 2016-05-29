<?php
/**
 * The template for displaying main menu
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
?>
<div class="top_panel_fixed_wrap"></div>

<header class="top_panel_wrap">
	<div class="menu_main_wrap clearfix">
		<div class="content_wrap_full">
			<a href="#" class="menu_main_responsive_button" data-title="<?php esc_attr_e('Navigate to ...', 'room'); ?>"></a>
			<nav class="menu_main_nav_area">
				<?php
				$room_menu_main = room_get_nav_menu('menu_main');
				if (empty($room_menu_main)) $room_menu_main = room_get_nav_menu();
				echo trim($room_menu_main);
				?>
			</nav>
		</div>
	</div>
</header>
