<?php
/*
Plugin Name: Related Products (Rue)
Plugin URI: http://wordpress.org/extend/plugins/#
Description: Add related products to posts
Author: Rue Turner
Version: 1.0
Author URI: https://rue.uk
*/

// register Foo_Widget widget
function register_related_products_widget() {
    register_widget( 'Related_Products' );
}
add_action( 'widgets_init', 'register_related_products_widget' );

/**
 * Adds Related_Products widget.
 */
class Related_Products extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'Related_Products', // Base ID
      esc_html__( 'Related Products', 'text_domain' ), // Name
      array( 'description' => esc_html__( 'Adds related products links to posts', 'text_domain' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    global $post;

    $relatedidsfield = $instance['relatedidsfield'];
    $product_ids = explode(',', $post->$relatedidsfield);
    $wcargs = array(
      'include' => $product_ids,
    );
    $products = wc_get_products( $wcargs );

    if ( ! empty($products) ) {
      echo $args['before_widget'];
      if ( ! empty( $instance['title'] ) ) {
        echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
      }
      // echo esc_html__( 'Hello, World!', 'text_domain' );
      if ( ! empty($instance['message']) ) {
        echo '<p>'.$instance['message'].'</p>';
      }
      echo '<div class="related-products">';
      foreach ( $products as $product ) {
        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $product->is_visible() ? $product->get_permalink( $product ) : '', $product );
        echo '<a class="related-products-item btn btn-light" href="'.esc_url( $product_permalink ).'">'.$product->get_name().'</a>';
      }
      echo '</div>';
    }

    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
    ?>
    <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>"><?php esc_attr_e( 'Message:', 'text_domain' ); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'message' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['message'] ); ?>">
    </p>
    <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'relatedidsfield' ) ); ?>"><?php esc_attr_e( 'Related product IDs field:', 'text_domain' ); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'relatedidsfield' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'relatedidsfield' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['relatedidsfield'] ); ?>">
    </p>
    <?php
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
    $instance['relatedidsfield'] = ( ! empty( $new_instance['relatedidsfield'] ) ) ? sanitize_text_field( $new_instance['relatedidsfield'] ) : '';
    $instance['message'] = ( ! empty( $new_instance['message'] ) ) ? sanitize_text_field( $new_instance['message'] ) : '';

    return $instance;
  }

} // class Related_Products
