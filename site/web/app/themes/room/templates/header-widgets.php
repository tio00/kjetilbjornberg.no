<?php
/**
 * The template for displaying Header widgets area
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Header sidebar
$header_name = room_get_theme_option('header_widgets');
$header_present = !room_param_is_off($header_name) && is_active_sidebar($header_name);
if ($header_present) { 
	room_storage_set('current_sidebar', 'header');
	$header_wide = room_get_theme_option('header_wide');
	ob_start();
	do_action( 'before_sidebar' );
	if ( !dynamic_sidebar($header_name) ) {
		// Put here html if user no set widgets in sidebar
	}
	do_action( 'after_sidebar' );
	$out = ob_get_contents();
	ob_end_clean();
	$out = preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out);
	$need_columns = strpos($out, 'columns_wrap')===false;
	if ($need_columns) {
		$columns = max(0, (int) room_get_theme_option('header_columns'));
		if ($columns == 0) $columns = min(6, max(1, substr_count($out, '<aside ')));
		if ($columns > 1)
			$out = preg_replace("/class=\"widget /", "class=\"column-1_".esc_attr($columns).' widget ', $out);
		else
			$need_columns = false;
	}
	?>
	<div class="header_wrap widget_area<?php echo !empty($header_wide) ? ' header_fullwidth' : ' header_boxed'; ?>">
		<div class="header_wrap_inner widget_area_inner">
			<?php 
			if (!$header_wide) { 
				?><div class="content_wrap"><?php
			}
			if ($need_columns) {
				?><div class="columns_wrap"><?php
			}
			echo trim(chop($out));
			if ($need_columns) {
				?></div>	<!-- /.columns_wrap --><?php
			}
			if (!$header_wide) {
				?></div>	<!-- /.content_wrap --><?php
			}
			?>
		</div>	<!-- /.header_wrap_inner -->
	</div>	<!-- /.header_wrap -->
<?php
}
?>