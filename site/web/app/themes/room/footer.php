<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

						// Widgets area inside page content
						room_create_widgets_area('widgets_below_content');
						?>				
					</div>	<!-- </.content> -->

					<?php
					// Show main sidebar
					get_sidebar();

					// Widgets area below page content
					room_create_widgets_area('widgets_below_page');

					$body_style = room_get_theme_option('body_style');
					if ($body_style != 'fullscreen') {
						?></div>	<!-- </.content_wrap> --><?php
					}
					?>
			</div>		<!-- </.page_content_wrap> -->
			
			<?php
			// Footer sidebar
			$footer_name = room_get_theme_option('footer_widgets');
			$footer_present = !room_param_is_off($footer_name) && is_active_sidebar($footer_name);
			if ($footer_present) { 
				room_storage_set('current_sidebar', 'footer');
				$footer_wide = room_get_theme_option('footer_wide');
				ob_start();
				do_action( 'before_sidebar' );
				if ( !dynamic_sidebar($footer_name) ) {
					// Put here html if user no set widgets in sidebar
				}
				do_action( 'after_sidebar' );
				$out = ob_get_contents();
				ob_end_clean();
				$out = preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out);
				$need_columns = strpos($out, 'columns_wrap')===false;
				if ($need_columns) {
					$columns = max(0, (int) room_get_theme_option('footer_columns'));
					if ($columns == 0) $columns = min(6, max(1, substr_count($out, '<aside ')));
					if ($columns > 1)
						$out = preg_replace("/class=\"widget /", "class=\"column-1_".esc_attr($columns).' widget ', $out);
					else
						$need_columns = false;
				}
				?>
				<footer class="footer_wrap widget_area<?php echo !empty($footer_wide) ? ' footer_fullwidth' : ''; ?>">
					<div class="footer_wrap_inner widget_area_inner">
						<?php 
						if (!$footer_wide) { 
							?><div class="content_wrap"><?php
						}
						if ($need_columns) {
							?><div class="columns_wrap"><?php
						}
						echo trim(chop($out));
						if ($need_columns) {
							?></div>	<!-- /.columns_wrap --><?php
						}
						if (!$footer_wide) {
							?></div>	<!-- /.content_wrap --><?php
						}
						?>
					</div>	<!-- /.footer_wrap_inner -->
				</footer>	<!-- /.footer_wrap -->
			<?php
			}

			// Copyright area
			?> 
			<div class="copyright_wrap">
				<div class="copyright_wrap_inner">
					<div class="content_wrap">
						<div class="columns_wrap"><div class="column-1_1 logo_area">
							<?php
							$logo_image = '';
							if (room_get_theme_option("retina_ready")=='retina' && (int) room_get_value_gpc('room_retina', 0) > 0)
								$logo_image = room_get_theme_option( 'logo_footer_retina' );
							if (empty($logo_image)) 
								$logo_image = room_get_theme_option( 'logo_footer' );
							$logo_text = room_prepare_macros( '' . get_bloginfo( 'name' ) . '' );
							if (!empty($logo_image) || !empty($logo_text)) {
								?>
								<div class="logo_wrap">
									<div class="logo">
										<a href="<?php echo esc_url(home_url('/')); ?>"><?php
											if (!empty($logo_image)) {
												$attr = room_getimagesize($logo_image);
												echo '<img src="'.esc_url($logo_image).'" class="logo_footer" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>' ;
											} else {
												echo !empty($logo_text)
													? '<div class="logo_text">'.trim($logo_text).'</div>' 
													: '';
											}
										?></a>
									</div>
								</div>
								<?php
							}
							?>
						</div><div class="column-1_1 menu_area">
								<?php echo trim(room_get_nav_menu('menu_footer', 1)); ?>
						</div></div>
					</div>
				</div>
			</div>

			<div class="copyright_text">
				<div class="content_wrap">
					<div class="copyright_text_in"><?php echo force_balance_tags(nl2br(room_get_theme_option('copyright'))); ?></div>
				</div>
			</div>
			
		</div>	<!-- /.page_wrap -->

	</div>		<!-- /.body_wrap -->

<div class="scroll_to_top"><a href="#" title="<?php esc_attr_e('Scroll to top', 'room'); ?>"><span><?php esc_attr_e('top', 'room'); ?></span></a></div>

<?php
$left_menu = room_get_theme_option( 'left_menu' );
$left_menu_link = room_get_theme_option( 'left_menu_link' );
if(!empty($left_menu) && !empty($left_menu_link)){ ?>
<div class="left_menu_link"><a href="<?php echo esc_url($left_menu_link); ?>" class="link"><span><?php echo esc_html($left_menu); ?></span></a></div>
<?php }
$right_menu = room_get_theme_option( 'right_menu' );
$right_menu_link = room_get_theme_option( 'right_menu_link' );
if(!empty($right_menu) && !empty($right_menu_link)){ ?>
	<div class="right_menu_link"><a href="<?php echo esc_url($right_menu_link); ?>" class="link"><span><?php echo esc_html($right_menu); ?></span></a></div>
<?php } ?>

	<?php wp_footer(); ?>

</body>
</html>