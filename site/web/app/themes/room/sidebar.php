<?php
/**
 * The Sidebar containing the main widget areas.
 */

$sidebar_position = room_get_theme_option('sidebar_position');
if (room_sidebar_present()) {
	$sidebar_name = room_get_theme_option('sidebar_widgets');
	room_storage_set('current_sidebar', 'sidebar');
	?>
	<div class="sidebar <?php echo esc_attr($sidebar_position); ?> widget_area" role="complementary">
		<?php
		ob_start();
		do_action( 'before_sidebar' );
		if ( !dynamic_sidebar($sidebar_name) ) {
			// Put here html if user no set widgets in sidebar
		}
		do_action( 'after_sidebar' );
		$out = ob_get_contents();
		ob_end_clean();
		echo trim(chop(preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out)));
		?>
	</div> <!-- /.sidebar -->
	<?php
}
?>