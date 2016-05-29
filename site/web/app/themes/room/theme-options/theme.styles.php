<?php
/**
 * Generate custom CSS
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Return CSS with custom colors and fonts
if (!function_exists('room_customizer_get_css')) {

	function room_customizer_get_css($colors=null, $fonts=null, $minify=true) {
	
		if (empty($colors)) $colors = room_get_scheme_colors();
		if (empty($fonts))	$fonts = room_get_theme_fonts();
		$tags = array('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'link', 'info', 'menu', 'submenu', 'logo', 'button', 'input');
		foreach ($tags as $tag) {
			if (!isset($fonts[$tag]))
				$fonts[$tag] = array( 'family' => 'inherit');
		}
		
		// Make theme-specific colors and tints
		if (substr($colors['text'], 0, 1) == '#') {
			$colors['text_link_alpha'] = room_hex2rgba($colors['text_link'], 0.6);
			$colors['bg_color_alpha'] = room_hex2rgba($colors['bg_color'], 0.6);
			$colors['alter_bg_color_alpha'] = room_hex2rgba($colors['alter_bg_color'], 0.7);
			$colors['alter_bd_color_alpha'] = room_hex2rgba($colors['alter_bd_color'], 0.6);
			$colors['alter_link_alpha'] = room_hex2rgba($colors['alter_link'], 0.85);
		}

		$css = <<<CSS

/* Common tags */
body {
	font-family: {$fonts['p']['family']};
	color: {$colors['text']};
	background-color: {$colors['bg_color']};
}
h1 {font-family: {$fonts['h1']['family']};}
h2 {font-family: {$fonts['h2']['family']};}
h3 {font-family: {$fonts['h3']['family']};}
h4 {font-family: {$fonts['h4']['family']};}
h5 {font-family: {$fonts['h5']['family']};}
h6 {font-family: {$fonts['h6']['family']};}

h1, h2, h3, h4, h5, h6,
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
	color: {$colors['text_dark']};
}
dt, b, strong, s, strike, del {	
	color: {$colors['text_dark']};
}

a {
	font-family: {$fonts['link']['family']};
	color: {$colors['text_link']};
}
a:hover {
	color: {$colors['text_hover']};
}

blockquote {
	font-family: {$fonts['p']['family']};
}
blockquote > a,
blockquote > p > a,
blockquote cite {
	font-family: {$fonts['h4']['family']};
}


table {
	color: {$colors['text_dark']};
}
td, th {
	border-color: {$colors['bd_color']};
}
table > thead > tr, table > body > tr:first-child {
	color: {$colors['inverse_dark']};
	background-color: {$colors['text_hover']};
}

hr {
	border-color: {$colors['bd_color']};
}
figure figcaption,
.wp-caption-overlay .wp-caption .wp-caption-text,
.wp-caption-overlay .wp-caption .wp-caption-dd {
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
}
ul > li:before {
	color: {$colors['alter_link']};
}
.post_content ul, .widget_area ul, aside ul,
.post_content ol, .widget_area ol, aside ol {
	color: {$colors['alter_dark']};
}

/* Form fields */
input[type="tel"],
input[type="text"],
input[type="number"],
input[type="email"],
input[type="search"],
input[type="password"],
.select_container,
textarea {
	font-family: {$fonts['input']['family']};
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
}
.select_container select {
	font-family: {$fonts['input']['family']};
	color: {$colors['alter_light']};
}
input[type="tel"]:focus,
input[type="text"]:focus,
input[type="number"]:focus,
input[type="email"]:focus,
input[type="search"]:focus,
input[type="password"]:focus,
.select_container:hover,
textarea:focus {
	color: {$colors['alter_dark']};
	background-color: {$colors['alter_bg_hover']};
}
.select_container select:focus {
	color: {$colors['alter_dark']};
}
.select_container:after {
	color: {$colors['alter_light']};
	border-color: {$colors['alter_light']};
}
input::-webkit-input-placeholder,
textarea::-webkit-input-placeholder {
	color: {$colors['alter_light']};
}
input[type="radio"] + label:before,
input[type="checkbox"] + label:before {
	border-color: {$colors['alter_bd_color']};
	background-color: {$colors['alter_bg_color']};
}

/* WP Standard classes */
.sticky .sticky_label {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_dark']};
}

/* Page */
#page_preloader,
.page_wrap {
/*	border-color: {$colors['text_dark']}; */
	background-color: {$colors['bg_color']};
}

/* Header */
.logo b {
	font-family: {$fonts['p']['family']};
	color: {$colors['alter_light']};
}
.logo b:after {
	background-color: {$colors['alter_light']};
}
.logo .logo_text,
.logo i {
	font-family: {$fonts['logo']['family']};
	color: {$colors['alter_link']};
}
.logo_slogan {
	color: {$colors['alter_text']};
}

/* Social items */
.socials_wrap .social_item a {
	color: {$colors['alter_dark']};
}
.socials_wrap .social_item a:hover {
	color: {$colors['alter_light']};
}

/* Search */
.search_wrap.search_state_opened .search_form {
    border-color: {$colors['text_dark']};
}
.search_wrap .search_field,
.search_wrap .search_submit {
	color: {$colors['text_dark']};
}
.search_wrap .search_submit:hover {
	color: {$colors['text_hover']};
}
.search_wrap .label {
	font-family: {$fonts['menu']['family']};
	color: {$colors['text_dark']};
}
.post_item_404 .search_wrap .search_form {
	border-color: {$colors['text_dark']};
}
.post_item_404 .page_title {
	font-family: {$fonts['h4']['family']};
}

/* Cart */
.top_panel_buttons .menu_main_cart .top_panel_cart_button:before {
	background-color: {$colors['alter_link']};
}

/* Main menu */
.menu_main_wrap {
	background-color: {$colors['alter_dark']};
}
.top_panel_fixed .menu_main_wrap {}
.menu_main_nav li > a {
	font-family: {$fonts['menu']['family']};
}



/* Responsive menu */
.menu_mode_responsive .menu_main_responsive_button,
.menu_mode_responsive .menu_main_responsive a {
	font-family: {$fonts['menu']['family']};
}
.menu_mode_responsive .menu_main_responsive_button:hover {

}
.menu_mode_responsive .menu_main_responsive a:hover {

}


/* Posts slider */
.slider_swiper[data-slides-per-view="1"] .slide_cats {
	color: {$colors['text_link']};
}
.slider_swiper .slider_prev:hover,
.slider_swiper .slider_next:hover,
.slider_swiper .slider_next:hover:after {
	background-color: {$colors['text_dark']};
	color: {$colors['inverse_text']};
}
.slider_swiper .swiper-pagination-bullet-active {
	background-color: {$colors['alter_link']};
}

/* Post layouts */
.post_item {
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
}
.post_item .post_label,
.post_featured .post_label {
	background-color: {$colors['text_link']};
	color: {$colors['inverse_text']};
}
.post_featured .post_label.label_pinit:hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
}
.post_item a {
	color: {$colors['alter_link']};
}
.post_item a:hover {
	color: {$colors['alter_hover']};
}
.post_item .post_title a {
	color: {$colors['alter_dark']};
}
.post_item .post_title a:hover {
	color: {$colors['alter_hover']};
}
.post_item .more-link {
	background-color: {$colors['alter_link']};
	font-family: {$fonts['h4']['family']};
}
.post_item .more-link:hover {
	background-color: {$colors['alter_link_alpha']};
}
.sc_recent_news .post_footer .post_counters_comments,
.post_item .post_footer {
	border-color: {$colors['bd_color']};
	color: {$colors['alter_light']};
}
.sc_recent_news .post_footer .post_counters_comments:before {
	color: {$colors['alter_link']};
}
.sc_recent_news .post_footer .post_counters_comments:hover:before {
	color: {$colors['alter_light']};
}
.sc_recent_news .number {
	color: {$colors['alter_link']};
}
.post_item .post_footer .post_date a,
.post_item .post_footer .post_counters a {
	color: {$colors['alter_light']};
}
.post_item .post_footer .post_date a:hover,
.post_item .post_footer .post_counters a:hover,
.post_item .post_footer .post_counters_edit:hover:before,
.post_item .post_footer .post_counters_edit:hover a {
	color: {$colors['alter_dark']};
}
.post_item .post_footer .post_more a {
	border-color: {$colors['alter_bd_color']};
	color: {$colors['alter_light']};
}
.post_item:hover .post_footer .post_more a {
	border-color: {$colors['alter_hover']};
	color: {$colors['alter_hover']};
}
.post_item .post_footer .post_more a:hover {
	border-color: {$colors['alter_hover']};
	background-color: {$colors['alter_hover']};
	color: {$colors['inverse_text']};
}
.post_counters .social_items {

}
.post_counters .social_item {
	background-color: {$colors['alter_bg_color']};
}

.post_featured:after {
	background-color: {$colors['bg_color']};
}


/* Post Formats */
.format-aside .post_content,
.format-quote:not(.post_item_single) .post_content,
.format-link .post_content,
.format-status .post_content {

}
.format-aside .post_content a,
.format-quote:not(.post_item_single) .post_content a,
.format-link .post_content a,
.format-status .post_content a {
	color: {$colors['inverse_link']};
}
.format-quote:not(.post_item_single) .post_content a {
	color: {$colors['inverse_text']};
}
.format-aside .post_content a:hover,
.format-quote:not(.post_item_single) .post_content a:hover,
.format-link .post_content a:hover,
.format-status .post_content a:hover {
	color: {$colors['inverse_hover']};
}
.format-quote.post_item_single .post_content a {
	color: {$colors['text_dark']};
}
.format-quote.post_item_single .post_content a:hover {
	color: {$colors['text_link']};
}
.format-quote.post_item_single .post_content:before {
	color: {$colors['text_link']};
}
.format-chat p > b,
.format-chat p > strong {
	color: {$colors['text_dark']};
}

/* Pagination */
.nav-links-old a {
	background-color: {$colors['alter_link']};
	font-family: {$fonts['h4']['family']};
}
.nav-links-old a:hover {
	background-color: {$colors['alter_link_alpha']};
}

.page_links > a,
.nav-links .page-numbers {
	color: {$colors['alter_light']};
	font-family: {$fonts['h4']['family']};
}
.page_links > span:not(.page_links_title),
.page_links > a:hover,
.nav-links .page-numbers.current,
.nav-links a.page-numbers:hover {
	color: {$colors['text_dark']};
	border-color: {$colors['alter_link']};
}
.single .nav-links {
	border-color: {$colors['bd_color']};
}

/* Single post */
.post_item_single .post_header .post_info {
	color: {$colors['alter_light']};
}
.post_item_single .post_taxes .cats_label,
.post_item_single .post_taxes .tags_label {
	color: {$colors['text_dark']};
}
.post_item_single .post_taxes a {
	color: {$colors['text_light']};
}
.post_item_single .post_taxes a:hover {
	color: {$colors['text_dark']};
}
.post_item_single .post_counters .post_counters_item,
.post_item_single .post_counters .post_counters_item > a,
.post_item_single .post_counters .socials_caption {
	color: {$colors['text_dark']};
}
.post_item_single .post_counters .post_counters_item:hover,
.post_item_single .post_counters .post_counters_item:hover:before,
.post_item_single .post_counters .post_counters_item > a:hover,
.post_item_single .post_header .post_info a:hover span,
.post_item_single .post_header .post_info a:hover {
	color: {$colors['text_link']};
}



.post_item_single .post_counters .socials_caption:hover {
	color: {$colors['text_hover']};
}
.post_item_single .post_header .post_info a {
	color: {$colors['alter_link']};
}
.post_item_single .post_header .post_info a span {
	color: {$colors['alter_light']};
}
.post_item .post_categories a,
.post_item_single .post_taxes .post_tags a {
	background-color: {$colors['alter_link']};
	font-family: {$fonts['h4']['family']};
}
.post_item .post_categories a:hover,
.post_item_single .post_taxes .post_tags a:hover {
	background-color: {$colors['alter_link_alpha']};
}
.post_counters .post_counters_item:before {
	color: {$colors['alter_link']};
}
.comments_list_wrap .comment_reply a {
	font-family: {$fonts['h4']['family']};
	color: {$colors['alter_link']};
}
.comments_list_wrap .comment_reply a:hover {
	color: {$colors['alter_hover']};
}

.socials_wrap .socials_caption {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_text']};
	font-family: {$fonts['h4']['family']};
}

/* Author info */
.author_info {
	border-color: {$colors['bd_color']};
}
.author_info .wrap_in {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_text']};
	border-color: {$colors['bd_color']};
}
.author_info .author_title {
	color: {$colors['alter_dark']};
}

/* Single post navi */
.single .nav-links .nav-links {
	border-color: {$colors['bd_color']};
}
.single .nav-links a {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_light']};
}
.single .nav-links a:hover {
	background-color: {$colors['alter_bg_hover']};
	border-color: {$colors['alter_hover']};
	color: {$colors['alter_dark']};
}
.single .nav-links a:after {
	border-color: {$colors['alter_bd_color']};
}

/* Attachments navi */
.image-navigation .nav-previous a:after,
.image-navigation .nav-next a:after {
	background-color: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
	color: {$colors['alter_light']};
}
.image-navigation .nav-previous a:hover,
.image-navigation .nav-next a:hover {
	background-color: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
	color: {$colors['text_link']};
}
.image-navigation .nav-previous a:hover:after,
.image-navigation .nav-next a:hover:after {
	color: {$colors['text_link']};
}

/* Related posts */
.related_wrap {
	border-color: {$colors['bd_color']};
}

/* Comments */
.sc_contact_form,
.comments_list_wrap,
.comments_form_wrap {
	border-color: {$colors['bd_color']};
}
.comments_list_wrap li + li,
.comments_list_wrap li ul {
	border-color: {$colors['bd_color']};
}
.comments_list_wrap .comment_text {
	color: {$colors['text']};
}
.sc_contact_form button,
.comments_wrap .form-submit input[type="submit"],
.comments_wrap .form-submit input[type="button"] {
	background-color: {$colors['alter_link']};
	font-family: {$fonts['h4']['family']};
}
.sc_contact_form button:hover,
.comments_wrap .form-submit input[type="submit"]:hover,
.comments_wrap .form-submit input[type="button"]:hover,
.sc_contact_form button:focus,
.comments_wrap .form-submit input[type="submit"]:focus,
.comments_wrap .form-submit input[type="button"]:focus {
	background-color: {$colors['alter_link_alpha']};
}

/* Validation result */
.sc_contact_form .result {
	border-color: {$colors['text_hover']};
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
}


/* Sidebar */
.sidebar aside {
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
}
.sidebar .widget_twitter {
	border-color: {$colors['alter_bg_color']};
}
aside .widget_title {
	font-family: {$fonts['h4']['family']};
}

.top_panel_title {
	background-color: {$colors['alter_bg_color']};
}

.widgets_above_page .above_page:nth-child(2n),
.widgets_above_page_wrap {
	border-color: {$colors['bd_color']};
}


/* Widgets */
aside input[type="text"],
aside input[type="number"],
aside input[type="email"],
aside input[type="search"],
aside input[type="password"],
aside .select_container,
aside .mc4wp_wrap,
.widget_search form,
aside textarea {
	color: {$colors['text']};
	background-color: {$colors['bg_color']};
}
aside input[type="text"]:focus,
aside input[type="number"]:focus,
aside input[type="email"]:focus,
aside input[type="search"]:focus,
aside input[type="password"]:focus,
aside .select_container:hover,
aside .mc4wp_wrap:hover,
.widget_search form:hover,
aside textarea:focus {
	color: {$colors['text_dark']};
	background-color: {$colors['bg_color']};
}
aside .select_container select {
	color: {$colors['text']};
}
aside .select_container:hover select,
aside .select_container select:focus {
	color: {$colors['text_dark']};
}
aside .mc4wp_wrap:after,
.widget_search form:after,
aside .select_container:after {
	color: {$colors['text_light']};
	border-color: {$colors['text_light']};
}
.widget_area .post_item .post_info .post_counters_comments:before {
	color: {$colors['alter_link']};
}
.widget_area .post_item .post_info .post_counters_comments:hover:before {
	color: {$colors['alter_hover']};
}
aside input::-webkit-input-placeholder,
aside textarea::-webkit-input-placeholder {
	color: {$colors['text_light']};
}
aside h1, aside h2, aside h3, aside h4, aside h5, aside h6,
aside h1 a, aside h2 a, aside h3 a, aside h4 a, aside h5 a, aside h6 a,
.copyright_wrap h1, .copyright_wrap h2, .copyright_wrap h3, .copyright_wrap h4, .copyright_wrap h5, .copyright_wrap h6,
.copyright_wrap h1 a, .copyright_wrap h2 a, .copyright_wrap h3 a, .copyright_wrap h4 a, .copyright_wrap h5 a, .copyright_wrap h6 a {
	color: {$colors['alter_dark']};
}
aside a,
.copyright_wrap a {
	color: {$colors['alter_link']};
}
aside a:hover,
.copyright_wrap a:hover {
	color: {$colors['alter_hover']};
}
aside ul li {
	color: {$colors['alter_light']};
}
aside ul li a {
	color: {$colors['alter_dark']};
}
aside ul li a:hover {
	color: {$colors['alter_hover']};
}
aside ul li:before {
	color: {$colors['alter_link']};
}

.search_results.widget_area .post_item + .post_item {
	border-color: {$colors['bd_color']};
}
.widget_area .post_info_counters .post_counters_item,
aside .post_info_counters .post_counters_item {
	color: {$colors['alter_light']};
}
.widget_area .post_info_counters .post_counters_item:hover,
aside .post_info_counters .post_counters_item:hover {
	color: {$colors['alter_dark']};
}

.widget_area .post_item .post_info a,
aside .post_item .post_info a {
	color: {$colors['alter_light']};
}
.widget_area .post_item .post_info a:hover,
aside .post_item .post_info a:hover {
	color: {$colors['alter_hover']};
}

/* Widget with bg image */
aside.widget_bg_image a,
aside.widget_bg_image ul li a {
	color: {$colors['inverse_text']};
}
aside.widget_bg_image a:hover,
aside.widget_bg_image ul li a:hover {
	color: {$colors['inverse_hover']};
}

/* Tag cloud */
.widget_product_tag_cloud a,
.widget_tag_cloud a {
	border-color: {$colors['alter_bd_color']};
	color: {$colors['alter_light']};
}
.widget_product_tag_cloud a:hover,
.widget_tag_cloud a:hover {
	border-color: {$colors['alter_hover']};
	color: {$colors['alter_hover']};
}

/* RSS */
.widget_rss li {
	color: {$colors['alter_text']};
}
.widget_rss .rss-date {
	color: {$colors['alter_light']};
}
.widget_rss li+li {
	border-color: {$colors['alter_bd_color_alpha']};
}
.widget_rss li > a {
	font-family: {$fonts['h6']['family']};
	color: {$colors['alter_dark']};
}
.widget_rss li > a:hover {
	color: {$colors['alter_hover']};
}

/* Recent Comments */
.widget_recent_comments span.comment-author-link {
	color: {$colors['alter_dark']};
}

/* Calendar */
.widget_calendar table {
	color: {$colors['alter_text']};
}
.widget_calendar tbody td a,
.widget_calendar th {
	color: {$colors['alter_dark']};
}
.widget_calendar tbody td a:after {
	color: {$colors['alter_link']};
}
.widget_calendar tbody td a:hover {
	color: {$colors['alter_hover']};
}
.widget_calendar td#today {
	color: {$colors['inverse_text']};
}
.widget_calendar td#today:before {
	background-color: {$colors['alter_link']};
}
.widget_calendar td#today a,
.widget_calendar td#today a:after {
	color: {$colors['inverse_text']};
}
.widget_calendar #prev a,
.widget_calendar #next a {
	color: {$colors['alter_dark']};
}
.widget_calendar #prev a span,
.widget_calendar #next a span {
	border-color: {$colors['alter_bd_color']};
	color: {$colors['alter_text']};
}
.widget_calendar #prev a:hover,
.widget_calendar #next a:hover,
.widget_calendar #prev a:hover span,
.widget_calendar #next a:hover span {
	color: {$colors['alter_hover']};
}
.widget_calendar #prev a:hover span,
.widget_calendar #next a:hover span {
	border-color: {$colors['alter_hover']};
}

/* Room - Socials */
.widget_socials .social_item a:hover {
	background-color: {$colors['alter_link']};
}

/* Room - Popular posts */
aside .sc_tabs .sc_tabs_titles li a {
	color: {$colors['text_dark']};
}
aside .sc_tabs .sc_tabs_titles li.ui-tabs-active:after {
	background-color: {$colors['text_dark']};
}

/* Room - Recent News */
.sc_recent_news_header {
	border-color: {$colors['text_dark']};
}
.sc_recent_news_header_category_item_more {
	color: {$colors['text_link']};
}
.sc_recent_news_header_more_categories {
	border-color: {$colors['alter_bd_color']};
	background-color:{$colors['alter_bg_color']};
}
.sc_recent_news_header_more_categories > a {
	color:{$colors['alter_link']};
}
.sc_recent_news_header_more_categories > a:hover {
	color:{$colors['alter_hover']};
	background-color:{$colors['alter_bg_hover']};
}
.sc_recent_news .post_counters_item,
.sc_recent_news .post_counters .post_edit a {
	background-color:{$colors['alter_bg_color']};
}
.sidebar .sc_recent_news .post_counters_item,
.sidebar .sc_recent_news .post_counters .post_edit a {
	background-color:{$colors['bg_color']};
}
.sc_recent_news .post_counters .post_edit a {
	color:{$colors['alter_dark']};
}
.sc_recent_news_style_news-magazine .post_accented_border {
	border-color: {$colors['bd_color']};
}
.sc_recent_news_style_news-excerpt .post_item {
	border-color: {$colors['bd_color']};
}

.sc_recent_news_style_news-portfolio .post_item .post_info,
.sc_recent_news_style_news-portfolio .post_item .post_info:after {
	background-color: {$colors['alter_bg_color']};
}
.sc_recent_news_style_news-portfolio .post_item .post_info .post_title a {
	color:{$colors['alter_dark']};
}
.sc_recent_news_style_news-portfolio .post_item .post_info .post_title a:hover {
	color:{$colors['alter_hover']};
}

/* Footer */
.footer_wrap {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_text']};
}
.copyright_text {
	color: {$colors['alter_text']};
}
.menu_footer_nav li {
	font-family: {$fonts['h4']['family']};
}

.scroll_to_top a,
.left_menu_link a,
.right_menu_link a {
	font-family: {$fonts['h4']['family']};
	color: {$colors['alter_dark']};
}
.slide_info .slide_link a {
	font-family: {$fonts['h4']['family']};
}
.sc_recent_news_style_news-portfolio .post_item .post_info .link,
#sb_instagram .sbi_follow_btn a,
.aboutme_title_website a {
	font-family: {$fonts['h4']['family']};
	color: {$colors['text_dark']};
}
.sc_recent_news_style_news-portfolio .post_item .post_info .link:hover,
#sb_instagram .sbi_follow_btn a:hover,
.socials_wrap .social_item a:hover,
.aboutme_title_website a:hover {
	color: {$colors['text_dark']};
}
.sc_recent_news_style_news-portfolio .link:before,
.sc_recent_news_style_news-portfolio .link:after,
.slide_link a:before,
.slide_link a:after,
#sb_instagram .sbi_follow_btn a:before,
#sb_instagram .sbi_follow_btn a:after,
.scroll_to_top a:before,
.left_menu_link a:before,
.right_menu_link a:before,
.aboutme_title_website a:after,
.aboutme_title_website a:before {
	background-color: {$colors['alter_link']};
}

.mc4wp_wrap input[type="submit"] {
	font-family: {$fonts['h4']['family']};
	background-color: {$colors['alter_link']};
}
.mc4wp_wrap input[type="submit"]:hover {
	background-color: {$colors['alter_link_alpha']};
}


.hot_spot_info .point {
	background-color: {$colors['text_dark']};
}
.hot_spot_hover.hovered .point,
.hot_spot .hot_spot_hover.hovered,
.post_featured.width_hot_spot:hover .hot_spot_hover.hovered {
	background-color: {$colors['alter_link']};
}
.hot_spot_info .hot_spot_item a {
	color: {$colors['text_link']};
}
.hot_spot_info .hot_spot_item a:hover {
	color: {$colors['text_hover']};
}
.hot_spot_hover.hovered {
	background-color: {$colors['alter_bg_color']};
}
.hot_spot_info  {
	border-color: {$colors['bd_color']};
}

CSS;

		$css = apply_filters('room_filter_get_css', $css, $colors, $fonts);

		return $minify ? room_minify_css($css) : $css;
	}
}
?>