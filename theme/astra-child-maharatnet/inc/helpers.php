<?php
/**
 * دوال مساعدة عامة: قراءة اختيار Shop Manager وعرض قالب Elementor المطابق.
 * هذا هو قلب نظام تبديل الأشكال (راجع القسم 5 من توصيف المشروع).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * يرجع القيمة المختارة حالياً لعنصر معيّن (مثلاً 'header-02').
 *
 * @param string $field_name اسم حقل ACF (header_style, hero_style...).
 * @return string
 */
function ms_get_style( $field_name ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$value = get_field( $field_name, 'option' );

	return $value ? $value : '';
}

/**
 * يرجع ID قالب Elementor المطابق لاختيار عنصر معيّن، أو 0 إن لم يُربط بعد.
 *
 * @param string $field_name اسم حقل ACF.
 * @return int
 */
function ms_get_template_id( $field_name ) {
	$choice = ms_get_style( $field_name );
	if ( ! $choice ) {
		return 0;
	}

	$map = ms_get_template_map();

	if ( isset( $map[ $field_name ][ $choice ] ) ) {
		return (int) $map[ $field_name ][ $choice ];
	}

	return 0;
}

/**
 * يطبع (أو يرجع) قالب Elementor المطابق للاختيار الحالي لعنصر معيّن.
 *
 * أمثلة استخدام:
 *   ms_render_store_part( 'header_style' );                 // يطبع مباشرة
 *   $html = ms_render_store_part( 'footer_style', false );   // يرجع النص بدل الطباعة
 *
 * @param string $field_name اسم حقل ACF.
 * @param bool   $echo       اطبع النتيجة مباشرة أو أرجعها كنص.
 * @return string|void
 */
function ms_render_store_part( $field_name, $echo = true ) {
	$template_id = ms_get_template_id( $field_name );

	if ( ! $template_id ) {
		// لا يوجد قالب مربوط بعد لهذا العنصر — لا نطبع شيء للزوار،
		// ونعرض تنبيهاً بسيطاً للأدمن فقط ليعرف أن الربط ناقص.
		if ( current_user_can( 'manage_options' ) ) {
			$notice = sprintf(
				'<div style="padding:10px;border:1px dashed #cc0000;color:#cc0000;font-family:sans-serif;">⚠ لم يتم ربط قالب Elementor بعد للعنصر: <code>%s</code>. عدّل الملف inc/template-map.php.</div>',
				esc_html( $field_name )
			);

			if ( $echo ) {
				echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput
				return;
			}

			return $notice;
		}

		return $echo ? null : '';
	}

	$output = do_shortcode( '[elementor-template id="' . $template_id . '"]' );

	if ( $echo ) {
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput
		return;
	}

	return $output;
}

/**
 * Shortcode لإدراج أي عنصر (هيرو، تصنيفات، أقسام منتجات، آراء عملاء...)
 * داخل أي صفحة عبر ودجت Shortcode في Elementor أو محرر ووردبريس:
 *   [store_part field="hero_style"]
 */
add_shortcode( 'store_part', 'ms_store_part_shortcode' );

function ms_store_part_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'field' => '' ), $atts, 'store_part' );

	if ( ! $atts['field'] || ! in_array( $atts['field'], ms_store_layout_field_names(), true ) ) {
		return '';
	}

	return ms_render_store_part( $atts['field'], false );
}
