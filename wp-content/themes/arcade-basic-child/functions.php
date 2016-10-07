<?php
add_filter('opengraph_locale', 'override_og_locale');
function override_og_locale($locale) {
  return "en_GB";
}

add_action( 'widgets_init', 'rue_widgets_init' );
if ( ! function_exists( 'rue_widgets_init' ) ) :
/**
 * Creating the sidebars
 *
 * This function is attached to the 'widgets_init' action hook.
 *
 * @uses  register_sidebar()
 *
 * @since 1.0.0
 */
function rue_widgets_init() {
  //$rue_theme_options = rue_theme_options();

  register_sidebar( array(
    'name' => __( 'Footer Area', 'arcade' ),
    'id' => 'footer-area',
    'description' => __( 'That bit down the bottom.', 'arcade' ),
    'before_widget' => '<aside id="%1$s" class="footer-widget col-md-4 %2$s">',
    'after_widget' => '</aside>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
  ) );
}
endif; // bavotasan_widgets_init

add_action('wp_head','hook_favicons');
function hook_favicons() {
  $output = <<<EOT
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
  <link rel="manifest" href="/manifest.json">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#cc0000">
  <meta name="theme-color" content="#cc0000">
EOT;
  echo $output;
}
?>
