<?php
/**
 * تقييد صلاحيات Shop Manager (القسم 5 - الخطوة 6 من توصيف المشروع):
 * يصل فقط لصفحة «أشكال المتجر»، دون أي صلاحية تعديل على قوالب Elementor
 * أو إعدادات الثيم — بذلك لا يمكنه إتلاف التصميم، فقط تبديل الأشكال الجاهزة.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'ms_restrict_shop_manager_capabilities' );
add_action( 'admin_init', 'ms_restrict_shop_manager_capabilities_once' );

/**
 * يعيد ضبط صلاحيات الدور مرة واحدة فقط بعد تفعيل الثيم (تنشيط أول مرة على السيرفر)،
 * ثم أيضاً عند كل admin_init طالما لم يتم الضبط بعد، لتغطية حالة تفعيل ووكومرس/إيليمنتور
 * لاحقاً بعد الثيم (فيُعاد تسجيل صلاحياتهما الافتراضية على shop_manager).
 */
function ms_restrict_shop_manager_capabilities_once() {
	if ( get_option( 'ms_shop_manager_capabilities_restricted' ) ) {
		return;
	}

	ms_restrict_shop_manager_capabilities();
}

function ms_restrict_shop_manager_capabilities() {
	$role = get_role( 'shop_manager' );

	if ( ! $role ) {
		return;
	}

	// صلاحيات يجب حجبها عن Shop Manager: تعديل التصميم/القوالب/إعدادات الثيم.
	$capabilities_to_remove = array(
		'edit_theme_options',
		'edit_elementor_templates',
		'edit_others_elementor_templates',
		'publish_elementor_templates',
		'delete_elementor_templates',
		'edit_elementor_library',
		'edit_others_elementor_library',
		'publish_elementor_library',
		'delete_elementor_library',
		'customize',
		'install_plugins',
		'install_themes',
		'switch_themes',
	);

	foreach ( $capabilities_to_remove as $cap ) {
		$role->remove_cap( $cap );
	}

	update_option( 'ms_shop_manager_capabilities_restricted', 1 );
}
