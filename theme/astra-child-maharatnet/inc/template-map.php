<?php
/**
 * خريطة ربط كل اختيار (Variant) بمعرّف قالب Elementor (Template ID).
 *
 * ====================== مهم — خطوة مطلوبة بعد الرفع على السيرفر ======================
 * هذا الملف فارغ من الـ IDs الحقيقية عمداً، لأن القوالب تُصمَّم داخل Elementor
 * على السيرفر الفعلي (لكل عنصر Variant واحد يُحفَظ كـ Template → Section/Container)،
 * وكل قالب يحصل على ID مختلف بعد إنشائه.
 *
 * بعد تصميم القوالب في Elementor:
 *   1) افتح كل قالب وانسخ الـ ID من رابط التحرير (?post=1234).
 *   2) استبدل الرقم 0 بالـ ID الصحيح أسفل، أمام اسم الشكل المطابق.
 *   3) احفظ الملف وارفعه (git commit + push) — يظهر التغيير فوراً على الموقع.
 *
 * لا تُغيّر أسماء المفاتيح (header-01, hero-02...) لأنها مرتبطة بحقول ACF
 * المُعرَّفة في inc/acf-fields.php.
 * =======================================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, array<string,int>> خريطة [اسم الحقل => [قيمة الاختيار => ID القالب]]
 */
function ms_get_template_map() {
	return array(
		'header_style' => array(
			'header-01' => 0,
			'header-02' => 0,
			'header-03' => 0,
			'header-04' => 0,
		),
		'hero_style' => array(
			'hero-01' => 0,
			'hero-02' => 0,
			'hero-03' => 0,
			'hero-04' => 0,
			'hero-05' => 0,
		),
		'categories_style' => array(
			'categories-01' => 0,
			'categories-02' => 0,
			'categories-03' => 0,
			'categories-04' => 0,
		),
		'product_sections_style' => array(
			'product-section-01' => 0,
			'product-section-02' => 0,
		),
		'card_style' => array(
			'card-01' => 0,
			'card-02' => 0,
			'card-03' => 0,
			'card-04' => 0,
		),
		'shop_style' => array(
			'shop-01' => 0,
			'shop-02' => 0,
		),
		'single_product_style' => array(
			'single-01' => 0,
			'single-02' => 0,
			'single-03' => 0,
			'single-04' => 0,
		),
		'brands_style' => array(
			'brands-01' => 0,
			'brands-02' => 0,
		),
		'cart_style' => array(
			'cart-01' => 0,
			'cart-02' => 0,
			'cart-03' => 0,
		),
		'checkout_style' => array(
			'checkout-01' => 0,
			'checkout-02' => 0,
		),
		'testimonials_style' => array(
			'testimonials-01' => 0,
			'testimonials-02' => 0,
			'testimonials-03' => 0,
		),
		'footer_style' => array(
			'footer-01' => 0,
			'footer-02' => 0,
			'footer-03' => 0,
		),
	);
}
