<?php
if (function_exists('room_exists_woocommerce') && room_exists_woocommerce() && !(is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART'))) {
?>
	<div class="menu_main_cart top_panel_icon">
		<?php
		$cart_items = WC()->cart->get_cart_contents_count();
		$cart_summa = strip_tags(WC()->cart->get_cart_subtotal());
		?>
		<a href="#" class="top_panel_cart_button" data-items="<?php echo esc_attr($cart_items); ?>" data-summa="<?php echo esc_attr($cart_summa); ?>">
			<span class="icon icon-iconmonstr-shopping-cart"></span>
		</a>
		<ul class="widget_area sidebar_cart"><li>
				<?php
				do_action( 'before_sidebar' );
				room_storage_set('current_sidebar', 'cart');
				if ( !dynamic_sidebar( 'sidebar-cart' ) ) {
					the_widget( 'WC_Widget_Cart', 'title=&hide_if_empty=1' );
				}
				?>
			</li></ul>
	</div>
<?php } ?>