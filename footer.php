<?php
/**
 * Site altlığı: bülten bandı ve footer.
 *
 * Şablonlar kendi <main> etiketlerini kapatır; bülten bandı ve footer
 * bunun ardından gelir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'template-parts/site/bulten' );
get_template_part( 'template-parts/site/footer-kolonlar' );

wp_footer();
?>
</body>
</html>
