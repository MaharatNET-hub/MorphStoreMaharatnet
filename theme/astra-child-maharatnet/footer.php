<?php
/**
 * الفوتر — يستبدل فوتر Astra الافتراضي بالكامل ويعرض القالب المطابق
 * لاختيار Shop Manager في حقل footer_style.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</div><!-- #content -->

<footer id="colophon" class="site-footer ms-store-footer" role="contentinfo">
	<?php ms_render_store_part( 'footer_style' ); ?>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>
