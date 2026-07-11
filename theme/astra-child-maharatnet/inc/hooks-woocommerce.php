<?php
/**
 * ربط اختيارات الأشكال بعناصر WooCommerce.
 *
 * كارت المنتج مربوط مباشرة (نفس منطق الهيدر/الفوتر): إن وُجد قالب Elementor
 * مربوط لشكل الكارت الحالي، يُستبدَل به شكل الكارت الافتراضي بالكامل.
 *
 * صفحة المتجر / المنتج المفرد / السلة / الشيك أوت / البراندات: هذه عناصر كاملة
 * الصفحة (Full Template) وليست جزءاً متكرراً، لذا الطريقة الموصى بها في التوصيف
 * (القسم 4 - الطريقة أ) هي تصميمها كقوالب Archive/Single عبر JetWooBuilder وربطها
 * شرطياً بإضافة Dynamic Conditions for Elementor مقارنةً بقيمة حقل ACF مباشرة —
 * دون الحاجة لكود إضافي. لتسهيل هذا الربط الشرطي، نضيف كلاس على وسم <body>
 * يعكس اختيار كل عنصر، ليُستخدَم كبديل بصري في أي مكان (CSS أو شروط العرض).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * استبدال كارت المنتج الافتراضي بالقالب المختار (إن وُجد).
 * يُنفَّذ فوق كل عنصر في حلقة عرض المنتجات (الأرشيف، الصفحة الرئيسية، الكاروسيلات...).
 */
add_action( 'woocommerce_before_shop_loop_item', 'ms_render_product_card', 5 );

function ms_render_product_card() {
	$template_id = ms_get_template_id( 'card_style' );

	if ( ! $template_id ) {
		return; // لا يوجد قالب مربوط بعد — يبقى شكل ووكومرس الافتراضي كما هو.
	}

	// نطبع القالب المخصّص، ونمنع الخطافات الافتراضية (صورة/عنوان/سعر/زر) من التكرار.
	ms_render_store_part( 'card_style' );

	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
}

/**
 * إضافة كلاس على <body> لكل عنصر مرتبط بـ WooCommerce، لتُستخدَم من قِبل
 * إضافة Dynamic Conditions أو أي CSS/JS مخصص لتفعيل الشكل المطابق.
 * مثال: body.shop-style-shop-02, body.checkout-style-checkout-01
 */
add_filter( 'body_class', 'ms_add_store_style_body_classes' );

function ms_add_store_style_body_classes( $classes ) {
	$context_fields = array(
		'shop_style',
		'single_product_style',
		'brands_style',
		'cart_style',
		'checkout_style',
		'categories_style',
		'product_sections_style',
		'testimonials_style',
	);

	foreach ( $context_fields as $field_name ) {
		$choice = ms_get_style( $field_name );
		if ( $choice ) {
			$classes[] = str_replace( '_', '-', $field_name ) . '-' . $choice;
		}
	}

	return $classes;
}
