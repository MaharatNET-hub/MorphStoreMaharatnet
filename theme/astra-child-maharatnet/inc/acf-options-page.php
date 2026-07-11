<?php
/**
 * صفحة إعدادات مركزية لاختيار أشكال عناصر المتجر.
 * يصل إليها Shop Manager عبر صلاحية manage_woocommerce (يملكها افتراضياً)
 * دون الحاجة لأي صلاحية تعديل على قوالب Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'ms_register_options_page' );

function ms_register_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => 'إعدادات أشكال المتجر',
			'menu_title' => 'أشكال المتجر',
			'menu_slug'  => 'store-layouts',
			'capability' => 'manage_woocommerce',
			'icon_url'   => 'dashicons-layout',
			'position'   => 56,
			'redirect'   => false,
		)
	);
}
