<?php
/**
 * Astra Child - Maharatnet Store
 * نظام تصميم متعدّد الأشكال: نقطة الدخول الرئيسية للثيم الابن.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MS_CHILD_VERSION', '1.0.0' );
define( 'MS_CHILD_DIR', get_stylesheet_directory() );
define( 'MS_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * تحميل ملفات القوالب الأنماط (parent + child).
 */
function ms_enqueue_styles() {
	wp_enqueue_style(
		'astra-parent-style',
		get_template_directory_uri() . '/style.css'
	);

	wp_enqueue_style(
		'ms-child-style',
		get_stylesheet_uri(),
		array( 'astra-parent-style' ),
		MS_CHILD_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'ms_enqueue_styles' );

/**
 * تحميل الملفات الفرعية للثيم بالترتيب الصحيح.
 * كل ملف مسؤول عن جزء واحد من نظام الأشكال المتعدّدة.
 */
$ms_includes = array(
	'inc/acf-options-page.php',    // صفحة إعدادات ACF المركزية (أشكال المتجر)
	'inc/acf-fields.php',          // تعريف حقول الاختيار (Select) لكل عنصر
	'inc/template-map.php',        // خريطة ربط كل اختيار بـ ID قالب Elementor
	'inc/helpers.php',             // render_store_part() + shortcode [store_part]
	'inc/hooks-woocommerce.php',   // ربط كارت المنتج + body classes لعناصر WooCommerce
	'inc/role-permissions.php',    // تقييد صلاحيات Shop Manager
);

foreach ( $ms_includes as $ms_include_file ) {
	$ms_include_path = MS_CHILD_DIR . '/' . $ms_include_file;
	if ( file_exists( $ms_include_path ) ) {
		require_once $ms_include_path;
	}
}
