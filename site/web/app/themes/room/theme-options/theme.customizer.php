<?php
/**
 * Theme customizer
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

//--------------------------------------------------------------
//-- Register Customizer Controls
//--------------------------------------------------------------

define('CUSTOMIZE_PRIORITY', 200);		// Start priority for the new controls

if (!function_exists('room_customizer_register_controls')) {
	add_action( 'customize_register', 'room_customizer_register_controls', 11 );
	function room_customizer_register_controls( $wp_customize ) {

		// Setup standard WP Controls
		// ---------------------------------
		
		// Remove unused sections
		$wp_customize->remove_section( 'colors');
		$wp_customize->remove_section( 'static_front_page');

		// Reorder standard WP sections
		$wp_customize->get_panel( 'nav_menus' )->priority = 30;
		$wp_customize->get_panel( 'widgets' )->priority = 40;
		$wp_customize->get_section( 'title_tagline' )->priority = 50;
		$wp_customize->get_section( 'background_image' )->priority = 60;
		$wp_customize->get_section( 'header_image' )->priority = 80;
		
		// Modify standard WP controls
		$wp_customize->get_control( 'blogname' )->description      = esc_html__('Use "[[" and "]]" to modify style and color of parts of the text, "||" to break current line', 'room');
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
		
		$sec = $wp_customize->get_section( 'background_image' );
		$sec->title = esc_html__('Background', 'room');
		$sec->description = esc_html__('Used only if "Content - Body style" equal to "boxed"', 'room');
		
		// Move standard option 'Background Color' to the section 'Background Image'
		$wp_customize->add_setting( 'background_color', array(
			'default'        => get_theme_support( 'custom-background', 'default-color' ),
			'theme_supports' => 'custom-background',
			'transport'		 => 'postMessage',
			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
			'sanitize_js_callback' => 'maybe_hash_hex_color',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
			'label'   => esc_html__( 'Background color', 'room' ),
			'section' => 'background_image',
		) ) );
		
		/*
		$sec = $wp_customize->get_section( 'colors' );
		if ( is_object($sec) ) {
			$sec->title = esc_html__('Background color', 'room');
			$sec->description = esc_html__('Used only if "Content - Body style" equal to "boxed"', 'room');
			$sec->priority = 70;
		}
		*/

		// Add Theme specific controls
		// ---------------------------------
		$cur_section = '';
		$i = 0;
		$options = room_storage_get('options');
		foreach ($options as $id=>$opt) {
			
			$i++;
			
			if (!empty($opt['hidden'])) continue;
			
			if ($opt['type'] == 'section') {

				$sec = $wp_customize->get_section( $id );
				if ( is_object($sec) && !empty($sec->title) ) {
					$sec->title      = $opt['title'];
					$sec->description= $opt['desc'];
					if ( !empty($opt['priority']) )	$sec->priority = $opt['priority'];
				} else {
					$wp_customize->add_section( esc_attr($id) , array(
						'title'      => $opt['title'],
						'description'=> $opt['desc'],
						'priority'	 => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i
					) );
				}
				$cur_section = $id;

			} else if ($opt['type'] == 'select') {

				$wp_customize->add_setting( $id, array(
					'default'           => room_get_theme_option($id),
					'sanitize_callback' => 'room_sanitize_value',
					'transport'         => !isset($opt['refresh']) || $opt['refresh'] ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority'	 => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
					'type'     => 'select',
					'choices'  => $opt['options']
				) );

			} else if ($opt['type'] == 'radio') {

				$wp_customize->add_setting( $id, array(
					'default'           => room_get_theme_option($id),
					'sanitize_callback' => 'room_sanitize_value',
					'transport'         => !isset($opt['refresh']) || $opt['refresh'] ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority'	 => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
					'type'     => 'radio',
					'choices'  => $opt['options']
				) );

			} else if ($opt['type'] == 'switch') {

				$wp_customize->add_setting( $id, array(
					'default'           => room_get_theme_option($id),
					'sanitize_callback' => 'room_sanitize_value',
					'transport'         => !isset($opt['refresh']) || $opt['refresh'] ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new Room_Customize_Switch_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority' => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
					'choices'  => $opt['options']
				) ) );

			} else if ($opt['type'] == 'checkbox') {

				$wp_customize->add_setting( $id, array(
					'default'           => room_get_theme_option($id),
					'sanitize_callback' => 'room_sanitize_value',
					'transport'         => !isset($opt['refresh']) || $opt['refresh'] ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority'	 => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
					'type'     => 'checkbox'
				) );

			} else if ($opt['type'] == 'color') {

				$wp_customize->add_setting( $id, array(
					'default'           => room_get_theme_option($id),
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => !isset($opt['refresh']) || $opt['refresh'] ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority'	 => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
				) ) );

			} else if ($opt['type'] == 'image') {

				$wp_customize->add_setting( $id, array(
					'default'           => room_get_theme_option($id),
					'sanitize_callback' => 'room_sanitize_value',
					'transport'         => !isset($opt['refresh']) || $opt['refresh'] ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority' => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
				) ) );

			} else if ($opt['type'] == 'info') {
			
				$wp_customize->add_setting( $id, array(
					'default'           => '',
					'sanitize_callback' => 'room_sanitize_value',
					'transport'         => 'postMessage'
				) );

				$wp_customize->add_control( new Room_Customize_Info_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority' => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
				) ) );

			} else {	// if ($opt['type'] == 'text') {

				$wp_customize->add_setting( $id, array(
					'default'           => room_get_theme_option($id),
					'sanitize_callback' => 'room_sanitize_value',
					'transport'         => !isset($opt['refresh']) || $opt['refresh'] ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($cur_section),
					'priority'	 => !empty($opt['priority']) ? $opt['priority'] : CUSTOMIZE_PRIORITY+$i,
					'type'     => 'text'
				) );
			}

		}
	}
}


// Create custom controls for customizer
if (!function_exists('room_customizer_custom_controls')) {
	add_action( 'customize_register', 'room_customizer_custom_controls' );
	function room_customizer_custom_controls( $wp_customize ) {
	
		class Room_Customize_Info_Control extends WP_Customize_Control {
			public $type = 'info';

			public function render_content() {
				?>
				<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<span class="customize-control-description desctiption"><?php echo esc_html( $this->description ); ?></span>
				</label>
				<?php
			}
		}
	
		class Room_Customize_Switch_Control extends WP_Customize_Control {
			public $type = 'switch';

			public function render_content() {
				?>
				<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<span class="customize-control-description desctiption"><?php echo esc_html( $this->description ); ?></span>
				<?php
				if (is_array($this->choices) && count($this->choices)>0) {
					foreach ($this->choices as $k=>$v) {
						?><label><input type="radio" name="_customize-radio-<?php echo esc_attr($this->id); ?>" <?php $this->link(); ?> value="<?php echo esc_attr($k); ?>">
						<?php echo esc_html($v); ?></label><?php
					}
				}
				?>
				</label>
				<?php
			}
		}
	
	}
}


// Sanitize plain value
if (!function_exists('room_sanitize_value')) {
	function room_sanitize_value($value) {
		return empty($value) ? $value : trim(strip_tags($value));
	}
}


//--------------------------------------------------------------
// Save custom settings in CSS file
//--------------------------------------------------------------


// Save CSS with custom colors and fonts after save custom options
if (!function_exists('room_customizer_action_save_after')) {
	add_action('customize_save_after', 'room_customizer_action_save_after');
	function room_customizer_action_save_after($api=false) {
		$settings = $api->settings();
		$scheme = $settings['color_scheme']->value();
		$colors = room_get_scheme_colors($scheme);
		foreach($colors as $k=>$v)
			$colors[$k] = $settings[$k]->value();
		$css = room_customizer_get_css($colors);
		room_customizer_save_css($css);
	}
}

// Save CSS with custom colors and fonts after switch theme
if (!function_exists('room_customizer_action_switch_theme')) {
	add_action('after_switch_theme', 'room_customizer_action_switch_theme');
	function room_customizer_action_switch_theme() {
		$css = room_customizer_get_css();
		room_customizer_save_css($css);
	}
}

// Save CSS with custom colors and fonts into uploads
if (!function_exists('room_customizer_save_css')) {
	function room_customizer_save_css($css) {
		$file = room_get_file_dir('css/custom.css');
		if (file_exists($file)) {
			room_fpc($file,
				"/*\n"
				. esc_html__('This file is automatically generated when save options in Customizer! Do not modify!', 'room') . "\n"
				. "*/\n"
				. $css
			);
		}
	}
}


//--------------------------------------------------------------
// Color schemes manipulations
//--------------------------------------------------------------

// Return specified color from current (or specified) color scheme
if ( !function_exists( 'room_get_scheme_color' ) ) {
	function room_get_scheme_color($color_name, $scheme = '') {
		$scheme_mod = room_get_theme_option( 'color_scheme' );
		if (empty($scheme)) $scheme = $scheme_mod;
		if (empty($scheme) || room_storage_empty('schemes', $scheme)) $scheme = 'default';
		$colors = room_storage_get_array('schemes', $scheme, 'colors');
		return $scheme==$scheme_mod 
			? room_get_theme_option($color_name, $colors[$color_name])
			: $colors[$color_name];
	}
}

// Return colors from current color scheme
if ( !function_exists( 'room_get_scheme_colors' ) ) {
	function room_get_scheme_colors($scheme = '') {
		if (empty($scheme)) $scheme = room_get_theme_option( 'color_scheme' );
		if (empty($scheme) || room_storage_empty('schemes', $scheme)) $scheme = 'default';
		$colors = room_storage_get_array('schemes', $scheme, 'colors');
		$rez = array();
		foreach ($colors as $k=>$v) {
			$rez[$k] = isset($_GET['color_scheme']) ? $v : room_get_scheme_color($k, $scheme);
		}
		return $rez;
	}
}

// Return list schemes
if ( !function_exists( 'room_get_list_schemes' ) ) {
	function room_get_list_schemes() {
		$list = array();
		$schemes = room_storage_get('schemes');
		if (is_array($schemes) && count($schemes) > 0) {
			foreach ($schemes as $slug => $scheme) {
				$list[$slug] = $scheme['title'];
			}
		}
		return $list;
	}
}

// Return theme fonts settings
if ( !function_exists( 'room_get_theme_fonts' ) ) {
	function room_get_theme_fonts($tag = '') {
		return !empty($tag) && !room_storage_empty('theme_fonts', $tag) 
					? room_storage_get_array('theme_fonts', $tag) 
					: room_storage_get('theme_fonts');
	}
}


//--------------------------------------------------------------
// Customizer JS and CSS
//--------------------------------------------------------------

// Binds JS listener to make Customizer color_scheme control.
// Passes color scheme data as colorScheme global.
if ( !function_exists( 'room_customizer_control_js' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'room_customizer_control_js' );
	function room_customizer_control_js() {
		room_enqueue_style( 'room-customizer-style', room_get_file_url('theme-options/theme.customizer.css') );
		room_enqueue_script( 'room-customizer-color-scheme-control-script', room_get_file_url('theme-options/theme.customizer.color-scheme.js'), array( 'customize-controls', 'iris', 'underscore', 'wp-util' ) );
		wp_localize_script( 'room-customizer-color-scheme-control-script', 'room_color_schemes', room_storage_get('schemes') );
		wp_localize_script( 'room-customizer-color-scheme-control-script', 'room_dependencies', room_get_theme_dependencies() );
	}
}

// Binds JS handlers to make the Customizer preview reload changes asynchronously.
if ( !function_exists( 'room_customizer_preview_js' ) ) {
	add_action( 'customize_preview_init', 'room_customizer_preview_js' );
	function room_customizer_preview_js() {
		room_enqueue_script( 'room-customize-preview-script', room_get_file_url('theme-options/theme.customizer.preview.js'), array( 'customize-preview' ) );
	}
}

// Output an Underscore template for generating CSS for the color scheme.
// The template generates the css dynamically for instant display in the Customizer preview.
if ( !function_exists( 'room_customizer_css_template' ) ) {
	add_action( 'customize_controls_print_footer_scripts', 'room_customizer_css_template' );
	function room_customizer_css_template() {
		$colors = array(
			
			// Whole block border and background
			'bg_color'				=> '{{ data.bg_color }}',
			'bd_color'				=> '{{ data.bd_color }}',
			
			// Text and links colors
			'text'					=> '{{ data.text }}',
			'text_light'			=> '{{ data.text_light }}',
			'text_dark'				=> '{{ data.text_dark }}',
			'text_link'				=> '{{ data.text_link }}',
			'text_hover'			=> '{{ data.text_hover }}',
		
			// Alternative blocks (submenu items, form's fields, etc.)
			'alter_bg_color'		=> '{{ data.alter_bg_color }}',
			'alter_bg_hover'		=> '{{ data.alter_bg_hover }}',
			'alter_bd_color'		=> '{{ data.alter_bd_color }}',
			'alter_bd_hover'		=> '{{ data.alter_bd_hover }}',
			'alter_text'			=> '{{ data.alter_text }}',
			'alter_light'			=> '{{ data.alter_light }}',
			'alter_dark'			=> '{{ data.alter_dark }}',
			'alter_link'			=> '{{ data.alter_link }}',
			'alter_hover'			=> '{{ data.alter_hover }}',

			// Additional accented colors (if used in the current theme)
//			'accent2'				=> '{{ data.accent2 }}',
//			'accent2_hover'			=> '{{ data.accent2_hover }}',
//			'accent3'				=> '{{ data.accent3 }}',
//			'accent3_hover'			=> '{{ data.accent3_hover }}',

			// Inverse blocks (with background equal to the links color or one of accented colors)
			'inverse_text'			=> '{{ data.inverse_text }}',
			'inverse_light'			=> '{{ data.inverse_light }}',
			'inverse_dark'			=> '{{ data.inverse_dark }}',
			'inverse_link'			=> '{{ data.inverse_link }}',
			'inverse_hover'			=> '{{ data.inverse_hover }}',
			
			// Additional (calculated) colors
			'bg_color_alpha'		=> '{{ data.bg_color_alpha }}',
			'alter_bg_color_alpha'	=> '{{ data.alter_bg_color_alpha }}',
			'alter_bd_color_alpha'	=> '{{ data.alter_bd_color_alpha }}',
			'alter_link_alpha'	=> '{{ data.alter_link_alpha }}',

		);
		?>
		<script type="text/html" id="tmpl-room-color-scheme">
			<?php echo trim(room_customizer_get_css( $colors, null, false )); ?>
		</script>
		<?php
	}
}



// -----------------------------------------------------------------
// -- Page Options section
// -----------------------------------------------------------------



//-------------------------------------------------------
//-- Meta boxes
//-------------------------------------------------------



// Callback function to show fields in meta box
if (!function_exists('room_show_meta_box_hot_spot')) {
	function room_show_meta_box_hot_spot() {
		global $post, $post_type;
		$hot_spot_left = get_post_meta($post->ID, 'room_options_hot_spot_left', true);
		$hot_spot_top = get_post_meta($post->ID, 'room_options_hot_spot_top', true);
		$hot_spot_des = get_post_meta($post->ID, 'room_options_hot_spot_des', true);

		$hot_spot_left_2 = get_post_meta($post->ID, 'room_options_hot_spot_left_2', true);
		$hot_spot_top_2 = get_post_meta($post->ID, 'room_options_hot_spot_top_2', true);
		$hot_spot_des_2 = get_post_meta($post->ID, 'room_options_hot_spot_des_2', true);

		$hot_spot_left_3 = get_post_meta($post->ID, 'room_options_hot_spot_left_3', true);
		$hot_spot_top_3 = get_post_meta($post->ID, 'room_options_hot_spot_top_3', true);
		$hot_spot_des_3 = get_post_meta($post->ID, 'room_options_hot_spot_des_3', true);

		$hot_spot_left_4 = get_post_meta($post->ID, 'room_options_hot_spot_left_4', true);
		$hot_spot_top_4 = get_post_meta($post->ID, 'room_options_hot_spot_top_4', true);
		$hot_spot_des_4 = get_post_meta($post->ID, 'room_options_hot_spot_des_4', true);

		$hot_spot_left_5 = get_post_meta($post->ID, 'room_options_hot_spot_left_5', true);
		$hot_spot_top_5 = get_post_meta($post->ID, 'room_options_hot_spot_top_5', true);
		$hot_spot_des_5 = get_post_meta($post->ID, 'room_options_hot_spot_des_5', true);
		?>
		<div class="room_meta_box_hot_spot">
			<input type="hidden" name="meta_box_post_nonce" value="<?php echo esc_attr(wp_create_nonce(get_admin_url())); ?>" />
			<input type="hidden" name="meta_box_post_type" value="<?php echo esc_attr($post_type); ?>" />

			<label class="room_meta_box_label">
				<?php esc_html_e('Hot Spot 1', 'room'); ?>
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Left:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_left" value="<?php echo !empty($hot_spot_left) ? (int)$hot_spot_left : ''; ?>" />
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Top:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_top" value="<?php echo !empty($hot_spot_top) ? (int)$hot_spot_top : ''; ?>" />
			</label>
			<label class="room_meta_box_label">
				<?php esc_html_e('Title:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_des" value="<?php echo !empty($hot_spot_des) ? esc_html($hot_spot_des) : ''; ?>" />
			</label>
			<hr/>

			<label class="room_meta_box_label">
				<?php esc_html_e('Hot Spot 2', 'room'); ?>
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Left:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_left_2" value="<?php echo !empty($hot_spot_left_2) ? (int)$hot_spot_left_2 : ''; ?>" />
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Top:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_top_2" value="<?php echo !empty($hot_spot_top_2) ? (int)$hot_spot_top_2 : ''; ?>" />
			</label>
			<label class="room_meta_box_label">
				<?php esc_html_e('Title:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_des_2" value="<?php echo !empty($hot_spot_des_2) ? esc_html($hot_spot_des_2) : ''; ?>" />
			</label>
			<hr/>

			<label class="room_meta_box_label">
				<?php esc_html_e('Hot Spot 3', 'room'); ?>
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Left:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_left_3" value="<?php echo !empty($hot_spot_left_3) ? (int)$hot_spot_left_3 : ''; ?>" />
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Top:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_top_3" value="<?php echo !empty($hot_spot_top_3) ? (int)$hot_spot_top_3 : ''; ?>" />
			</label>
			<label class="room_meta_box_label">
				<?php esc_html_e('Title:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_des_3" value="<?php echo !empty($hot_spot_des_3) ? esc_html($hot_spot_des_3) : ''; ?>" />
			</label>
			<hr/>

			<label class="room_meta_box_label">
				<?php esc_html_e('Hot Spot 4', 'room'); ?>
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Left:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_left_4" value="<?php echo !empty($hot_spot_left_4) ? (int)$hot_spot_left_4 : ''; ?>" />
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Top:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_top_4" value="<?php echo !empty($hot_spot_top_4) ? (int)$hot_spot_top_4 : ''; ?>" />
			</label>
			<label class="room_meta_box_label">
				<?php esc_html_e('Title:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_des_4" value="<?php echo !empty($hot_spot_des_4) ? esc_html($hot_spot_des_4) : ''; ?>" />
			</label>
			<hr/>

			<label class="room_meta_box_label">
				<?php esc_html_e('Hot Spot 5', 'room'); ?>
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Left:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_left_5" value="<?php echo !empty($hot_spot_left_5) ? (int)$hot_spot_left_5 : ''; ?>" />
			</label>
			<label class="room_meta_box_label_pos">
				<?php esc_html_e('Top:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_top_5" value="<?php echo !empty($hot_spot_top_5) ? (int)$hot_spot_top_5 : ''; ?>" />
			</label>
			<label class="room_meta_box_label">
				<?php esc_html_e('Title:', 'room'); ?>
				<input type="text" name="room_options_hot_spot_des_5" value="<?php echo !empty($hot_spot_des_5) ? esc_html($hot_spot_des_5) : ''; ?>" />
			</label>
			<hr/>

			<div class="room_meta_box_description">
				<?php esc_html_e('Specify the hot spot\'s position (in %) and its title', 'room'); ?>
			</div>
		</div>
	<?php
	}
}


if ( !function_exists('room_init_meta_box') ) {
	add_action( 'after_setup_theme', 'room_init_meta_box' );
	function room_init_meta_box() {
		if ( is_admin() ) {
			add_action("admin_enqueue_scripts", 'room_add_meta_box_scripts');
			add_action('save_post',			'room_save_meta_box');
			add_action('add_meta_boxes',	'room_add_meta_box');
		}
	}
}
	
// Load required styles and scripts for admin mode
if ( !function_exists( 'room_add_meta_box_scripts' ) ) {
	//add_action("admin_enqueue_scripts", 'room_add_meta_box_scripts');
	function room_add_meta_box_scripts() {
		// If current screen is 'Edit Page' - load fontello
		$screen = get_current_screen();
		if ($screen->id == 'page' && $screen->post_type=='page') {
			room_enqueue_style( 'fontello-style',  room_get_file_url('css/fontello/fontello-embedded.css') );
			wp_enqueue_script('jquery-ui-tabs', false, array('jquery', 'jquery-ui'));
			room_enqueue_script( 'room-meta-box-script', room_get_file_url('theme-options/theme.meta-box.js'), array('jquery') );
		}
	}
}


// Add meta box
if (!function_exists('room_add_meta_box')) {
	//add_action('add_meta_boxes', 'room_add_meta_box');
	function room_add_meta_box() {
		global $post_type;
		if ($post_type=='page') {
			//add_meta_box('room_meta_box_page', esc_html__('Theme Options', 'room'), 'room_show_meta_box', $post_type, 'side', 'high');
			add_meta_box('room_meta_box_page', esc_html__('Theme Options', 'room'), 'room_show_meta_box', $post_type, 'advanced', 'default');
		}
		if ($post_type=='post')
			add_meta_box('room_meta_box_page', esc_html__('Hot Spot', 'room'), 'room_show_meta_box_hot_spot', $post_type, 'side', 'default');

	}
}

// Callback function to show fields in meta box
if (!function_exists('room_show_meta_box')) {
	function room_show_meta_box() {
		global $post, $post_type;
		if ($post_type=='page') {
			// Load saved options 
			$meta = get_post_meta($post->ID, 'room_options', true);
			?>
			<div class="room_meta_box">
				<input type="hidden" name="meta_box_post_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
				<input type="hidden" name="meta_box_post_type" value="page" />
				<?php
				$tabs_titles = $tabs_content = array();
				$options = room_storage_get('options');
				foreach ($options as $k=>$v) {
					if (!isset($v['override']) || strpos($v['override']['mode'], $post_type)===false) continue;
					if (empty($v['override']['section']))
						$v['override']['section'] = esc_html__('General', 'room');
					if (!isset($tabs_titles[$v['override']['section']])) {
						$tabs_titles[$v['override']['section']] = $v['override']['section'];
						$tabs_content[$v['override']['section']] = '';
					}
					$v['val'] = isset($meta[$k]) ? $meta[$k] : 'inherit';
					$tabs_content[$v['override']['section']] .= room_show_meta_box_field($k, $v);
				}
				if (count($tabs_titles) > 0) {
					?>
					<div id="room_meta_box_tabs">
						<ul><?php
							$cnt = 0;
							foreach ($tabs_titles as $k=>$v) {
								$cnt++;
								?><li><a href="#room_meta_box_<?php echo esc_attr($cnt); ?>"><?php echo esc_html($v); ?></a></li><?php
							}
						?></ul>
						<?php
							$cnt = 0;
							foreach ($tabs_content as $k=>$v) {
								$cnt++;
								?>
								<div id="room_meta_box_<?php echo esc_attr($cnt); ?>" class="room_meta_box_section">
									<?php echo trim($v); ?>
								</div>
								<?php
							}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<?php		
		}
	}
}

// Display single option's field
if ( !function_exists('room_show_meta_box_field') ) {
	function room_show_meta_box_field($name, $field) {
		$inherit_state = room_param_is_inherit($field['val']);
		$output = '<div class="room_meta_box_item room_meta_box_item_'.esc_attr($field['type']).' room_meta_box_inherit_'.($inherit_state ? 'on' : 'off' ).'">'
						. '<h4 class="room_meta_box_item_title">'
							. esc_html($field['title'])
							. '<span class="room_meta_box_inherit_lock" id="room_meta_box_inherit_'.esc_attr($name).'"></span>'
						. '</h4>'
						. '<div class="room_meta_box_item_data">'
							. '<div class="room_meta_box_item_field">';
		if ($field['type']=='checkbox') {
			$output .= '<label class="room_meta_box_item_label">'
						. '<input type="checkbox" name="room_meta_box_field_'.esc_attr($name).'" value="1"'.(!empty($field['val']) ? ' checked="checked"' : '').' />'
						. esc_html($field['title'])
					. '</label>';
		} else if ($field['type']=='text') {
			$output .= '<input type="text" name="room_meta_box_field_'.esc_attr($name).'" value="'.esc_attr($field['val']).'" />';
		} else if ($field['type']=='select') {
			$output .= '<select size="1" name="room_meta_box_field_'.esc_attr($name).'">';
			foreach ($field['options'] as $k=>$v) {
				$output .= '<option value="'.esc_attr($k).'"'.($field['val']==$k ? ' selected="selected"' : '').'>'.esc_html($v).'</option>';
			}
			$output .= '</select>';
		}
		$output .=  	 '<div class="room_meta_box_inherit_cover"'.(!$inherit_state ? ' style="display:none;"' : '').'>'
							. '<span class="room_meta_box_inherit_label">' . esc_html__('Inherit', 'room') . '</span>'
							. '<input type="hidden" name="room_meta_box_inherit_'.esc_attr($name).'" value="'.esc_attr($inherit_state ? 'inherit' : '').'" />'
						. '</div>'
					. '</div>'
					. '<div class="room_meta_box_item_description">'
						. (!empty($field['override']['desc']) ? trim($field['override']['desc']) : trim($field['desc']))	// param 'desc' already processed with wp_kses()!
					. '</div>'
				. '</div>'
			. '</div>';
		return $output;
	}
}

// Save data from meta box
if (!function_exists('room_save_meta_box')) {
	//add_action('save_post', 'room_save_meta_box');
	function room_save_meta_box($post_id) {

		// verify nonce
		if ( !wp_verify_nonce( room_get_value_gp('meta_box_post_nonce'), admin_url() ) )
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		$post_type = isset($_POST['meta_box_post_type']) ? $_POST['meta_box_post_type'] : $_POST['post_type'];

		// check permissions
		$capability = 'page';
		$post_types = get_post_types( array( 'name' => $post_type), 'objects' );
		if (!empty($post_types) && is_array($post_types)) {
			foreach ($post_types  as $type) {
				$capability = $type->capability_type;
				break;
			}
		}
		if (!current_user_can('edit_'.($capability), $post_id)) {
			return $post_id;
		}

		// Save meta
		$meta = array();
		$options = room_storage_get('options');
		foreach ($options as $k=>$v) {
			// Skip not overriden options
			if (!isset($v['override']) || strpos($v['override']['mode'], $post_type)===false) continue;
			// Skip inherited options
			if (!empty($_POST['room_meta_box_inherit_'.$k])) continue;
			// Get option value from POST
			$meta[$k] = isset($_POST['room_meta_box_field_'.$k])
							? $_POST['room_meta_box_field_'.$k]
							: ($v['type']=='checkbox' ? 0 : '');
		}
		update_post_meta($post_id, 'room_options', $meta);


		// hot spot
		if ($post_type=='post'){

			$hot_spot_left = !empty($_POST['room_options_hot_spot_left']) ? $_POST['room_options_hot_spot_left'] : '';
			$hot_spot_top = !empty($_POST['room_options_hot_spot_top']) ? $_POST['room_options_hot_spot_top'] : '';
			$hot_spot_des = !empty($_POST['room_options_hot_spot_des']) ? $_POST['room_options_hot_spot_des'] : '';

			$hot_spot_left_2 = !empty($_POST['room_options_hot_spot_left_2']) ? $_POST['room_options_hot_spot_left_2'] : '';
			$hot_spot_top_2 = !empty($_POST['room_options_hot_spot_top_2']) ? $_POST['room_options_hot_spot_top_2'] : '';
			$hot_spot_des_2 = !empty($_POST['room_options_hot_spot_des_2']) ? $_POST['room_options_hot_spot_des_2'] : '';

			$hot_spot_left_3 = !empty($_POST['room_options_hot_spot_left_3']) ? $_POST['room_options_hot_spot_left_3'] : '';
			$hot_spot_top_3 = !empty($_POST['room_options_hot_spot_top_3']) ? $_POST['room_options_hot_spot_top_3'] : '';
			$hot_spot_des_3 = !empty($_POST['room_options_hot_spot_des_3']) ? $_POST['room_options_hot_spot_des_3'] : '';

			$hot_spot_left_4 = !empty($_POST['room_options_hot_spot_left_4']) ? $_POST['room_options_hot_spot_left_4'] : '';
			$hot_spot_top_4 = !empty($_POST['room_options_hot_spot_top_4']) ? $_POST['room_options_hot_spot_top_4'] : '';
			$hot_spot_des_4 = !empty($_POST['room_options_hot_spot_des_4']) ? $_POST['room_options_hot_spot_des_4'] : '';

			$hot_spot_left_5 = !empty($_POST['room_options_hot_spot_left_5']) ? $_POST['room_options_hot_spot_left_5'] : '';
			$hot_spot_top_5 = !empty($_POST['room_options_hot_spot_top_5']) ? $_POST['room_options_hot_spot_top_5'] : '';
			$hot_spot_des_5 = !empty($_POST['room_options_hot_spot_des_5']) ? $_POST['room_options_hot_spot_des_5'] : '';

			update_post_meta($post_id, 'room_options_hot_spot_left', $hot_spot_left);
			update_post_meta($post_id, 'room_options_hot_spot_top', $hot_spot_top);
			update_post_meta($post_id, 'room_options_hot_spot_des', $hot_spot_des);

			update_post_meta($post_id, 'room_options_hot_spot_left_2', $hot_spot_left_2);
			update_post_meta($post_id, 'room_options_hot_spot_top_2', $hot_spot_top_2);
			update_post_meta($post_id, 'room_options_hot_spot_des_2', $hot_spot_des_2);

			update_post_meta($post_id, 'room_options_hot_spot_left_3', $hot_spot_left_3);
			update_post_meta($post_id, 'room_options_hot_spot_top_3', $hot_spot_top_3);
			update_post_meta($post_id, 'room_options_hot_spot_des_3', $hot_spot_des_3);

			update_post_meta($post_id, 'room_options_hot_spot_left_4', $hot_spot_left_4);
			update_post_meta($post_id, 'room_options_hot_spot_top_4', $hot_spot_top_4);
			update_post_meta($post_id, 'room_options_hot_spot_des_4', $hot_spot_des_4);

			update_post_meta($post_id, 'room_options_hot_spot_left_5', $hot_spot_left_5);
			update_post_meta($post_id, 'room_options_hot_spot_top_5', $hot_spot_top_5);
			update_post_meta($post_id, 'room_options_hot_spot_des_5', $hot_spot_des_5);
		}

	}
}


//--------------------------------------------------------------
//-- Load Options list and styles
//--------------------------------------------------------------
get_template_part('theme-options/theme.options');
get_template_part('theme-options/theme.styles');
?>