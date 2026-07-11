<?php
/**
 * تعريف حقول اختيار الأشكال (Select Fields) — بالكود بالكامل.
 * لا حاجة لأي إعداد يدوي عبر واجهة ACF: الحقول تُسجَّل تلقائياً عند تفعيل الثيم
 * على أي سيرفر، طالما إضافة Advanced Custom Fields مُفعّلة.
 *
 * كل حقل = عنصر واحد من عناصر المتجر (راجع توصيف المشروع في docs/project-overview.md).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'ms_register_store_layout_fields' );

/**
 * يبني مصفوفة حقل Select جاهزة لإضافتها ضمن مجموعة الحقول.
 *
 * @param string $key     مفتاح فريد للحقل (بدون بادئة field_ - تُضاف تلقائياً).
 * @param string $name    اسم الحقل (meta key) المستخدم في get_field().
 * @param string $label   تسمية الحقل الظاهرة للمستخدم.
 * @param array  $choices مصفوفة [قيمة => وصف] لكل الأشكال المتاحة.
 * @param string $default القيمة الافتراضية (أول شكل عادة).
 */
function ms_select_field( $key, $name, $label, $choices, $default ) {
	return array(
		'key'           => 'field_ms_' . $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'select',
		'instructions'  => 'اختر الشكل الذي سيظهر لهذا العنصر في المتجر.',
		'required'      => 1,
		'choices'       => $choices,
		'default_value' => $default,
		'allow_null'    => 0,
		'multiple'      => 0,
		'ui'            => 1,
		'return_format' => 'value',
	);
}

function ms_register_store_layout_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array(
		ms_select_field(
			'header_style',
			'header_style',
			'شكل الهيدر',
			array(
				'header-01' => 'كلاسيك — لوجو بالوسط + قائمة أسفله',
				'header-02' => 'لوجو يسار + بحث بالوسط + أيقونات يمين',
				'header-03' => 'هيدر شفاف فوق البانر',
				'header-04' => 'هيدر مصغّر Sticky',
			),
			'header-01'
		),
		ms_select_field(
			'hero_style',
			'hero_style',
			'شكل البانر الرئيسي (Hero)',
			array(
				'hero-01' => 'سلايدر كامل العرض',
				'hero-02' => 'نص + صورة جانبية',
				'hero-03' => 'شبكة بانرات للعروض',
				'hero-04' => 'فيديو خلفية',
				'hero-05' => 'بانر بعدّاد تنازلي',
			),
			'hero-01'
		),
		ms_select_field(
			'categories_style',
			'categories_style',
			'شكل عرض التصنيفات',
			array(
				'categories-01' => 'شبكة صور دائرية',
				'categories-02' => 'كاروسيل أفقي',
				'categories-03' => 'بلاطات كبيرة مع Overlay',
				'categories-04' => 'قائمة أيقونية جانبية',
			),
			'categories-01'
		),
		ms_select_field(
			'product_sections_style',
			'product_sections_style',
			'شكل أقسام المنتجات (New Arrivals / Best Sellers / Featured / On Sale)',
			array(
				'product-section-01' => 'كاروسيل',
				'product-section-02' => 'شبكة Grid',
			),
			'product-section-01'
		),
		ms_select_field(
			'card_style',
			'card_style',
			'شكل كارت المنتج',
			array(
				'card-01' => 'كلاسيك (صورة/اسم/سعر/زر)',
				'card-02' => 'Hover يبدّل الصورة + Quick Add',
				'card-03' => 'كارت أفقي',
				'card-04' => 'كارت مع Badge وWishlist',
			),
			'card-01'
		),
		ms_select_field(
			'shop_style',
			'shop_style',
			'شكل صفحة المتجر / الأرشيف',
			array(
				'shop-01' => 'سايدبار فلاتر يسار + شبكة',
				'shop-02' => 'عرض كامل مع فلاتر علوية',
			),
			'shop-01'
		),
		ms_select_field(
			'single_product_style',
			'single_product_style',
			'شكل صفحة المنتج المفرد',
			array(
				'single-01' => 'معرض عمودي + تفاصيل يمين',
				'single-02' => 'معرض أفقي',
				'single-03' => 'عرض كامل بتبويبات',
				'single-04' => 'Sticky Add-to-Cart',
			),
			'single-01'
		),
		ms_select_field(
			'brands_style',
			'brands_style',
			'شكل صفحة البراندات',
			array(
				'brands-01' => 'شبكة لوجوهات',
				'brands-02' => 'قائمة A–Z مع فلترة',
			),
			'brands-01'
		),
		ms_select_field(
			'cart_style',
			'cart_style',
			'شكل السلة',
			array(
				'cart-01' => 'جدول كلاسيك',
				'cart-02' => 'كروت',
				'cart-03' => 'سلة جانبية منبثقة (Side Cart)',
			),
			'cart-01'
		),
		ms_select_field(
			'checkout_style',
			'checkout_style',
			'شكل الشيك أوت',
			array(
				'checkout-01' => 'خطوة واحدة بعمودين (One-page)',
				'checkout-02' => 'متعدّد الخطوات (Multi-step)',
			),
			'checkout-01'
		),
		ms_select_field(
			'testimonials_style',
			'testimonials_style',
			'شكل آراء العملاء',
			array(
				'testimonials-01' => 'كاروسيل',
				'testimonials-02' => 'شبكة كروت',
				'testimonials-03' => 'سلايدر مع صور',
			),
			'testimonials-01'
		),
		ms_select_field(
			'footer_style',
			'footer_style',
			'شكل الفوتر',
			array(
				'footer-01' => '4 أعمدة كلاسيك',
				'footer-02' => 'مبسّط بسطر واحد',
				'footer-03' => 'بنشرة بريدية + سوشال',
			),
			'footer-01'
		),
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_ms_store_layouts',
			'title'    => 'أشكال المتجر',
			'fields'   => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'store-layouts',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'active'                => true,
			'description'           => 'قوائم منسدلة لاختيار شكل كل عنصر من عناصر المتجر.',
		)
	);
}

/**
 * أسماء كل حقول أشكال المتجر في مكان واحد — تُستخدم من قبل الملفات الأخرى
 * (helpers.php, hooks-woocommerce.php) بدل تكرار الأسماء يدوياً.
 */
function ms_store_layout_field_names() {
	return array(
		'header_style',
		'hero_style',
		'categories_style',
		'product_sections_style',
		'card_style',
		'shop_style',
		'single_product_style',
		'brands_style',
		'cart_style',
		'checkout_style',
		'testimonials_style',
		'footer_style',
	);
}
