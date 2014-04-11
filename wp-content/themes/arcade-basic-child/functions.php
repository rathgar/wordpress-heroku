<?php
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

?>
