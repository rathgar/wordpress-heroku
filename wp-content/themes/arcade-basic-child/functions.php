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
  register_widget( 'BSTags_Widget' );

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
endif; // rue_widgets_init

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

// Register Custom Navigation Walker
require_once('wp_bootstrap_pagination.php');

function get_the_tags_bootstrap( $before = '', $after = '', $id = 0 ) {
  $output = '';
  $tags = get_the_tags();

  foreach ( $tags as $term ) {
      // The $term is an object, so we don't need to specify the $taxonomy.
      $term_link = get_term_link( $term );
      // If there was an error, continue to the next term.
      if ( is_wp_error( $term_link ) ) {
          continue;
      }
      // We successfully got a link. Print it out.
      $output .= '<a class="btn btn-light btn-sm" href="' . esc_url( $term_link ) . '">' . $term->name . '</a> ';
  }

  return $before . $output . $after;
}


function posts_link_next_class($format){
     $format = str_replace('href=', 'class="btn btn-light" href=', $format);
     return $format;
}
add_filter('next_post_link', 'posts_link_next_class');

function posts_link_prev_class($format) {
     $format = str_replace('href=', 'class="btn btn-light" href=', $format);
     return $format;
}
add_filter('previous_post_link', 'posts_link_prev_class');




function rue_excerpt_more( $text ) {
  if ( is_singular() )
    return $text;

  return '<p class="excerpt">' . $text . ' <a href="' . get_permalink( get_the_ID() ) . '" title="' . __( 'Read more', 'arcade-basic' ).'" class="btn btn-light btn-sm">…</a></p>';
}

function rue_excerpt( $more ) {
	return '';
}





add_action( 'init', 'rue_setup' );
function rue_setup( ) {
  remove_filter( 'wp_trim_excerpt', 'bavotasan_excerpt_more' );
  remove_filter( 'the_content_more_link', 'bavotasan_content_more_link' );
  remove_filter( 'excerpt_more', 'bavotasan_excerpt' );
  add_filter( 'wp_trim_excerpt', 'rue_excerpt_more' );
  add_filter( 'excerpt_more', 'rue_excerpt' );
}



/**
 * Adds BSTags_Widget widget.
 */
class BSTags_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bstags_widget', // Base ID
			esc_html__( 'Bootstrap Tags', 'text_domain' ), // Name
			array( 'description' => esc_html__( 'Bootstrap styled tags', 'text_domain' ), ) // Args
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
    if (! is_singular())
      return;
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
    echo get_the_tags_bootstrap();
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

		return $instance;
	}

} // class BSTags_Widget



function the_artwork_meta($before = '', $after = '', $id = 0) {
  $post = get_post($id);
  $content = $before;
  $content .= '<dl class="artwork-meta">';
  if ($post->media != NULL) {
    $content .= '<dt>Media</dt>';
    $content .= '<dd>'.$post->media.'</dd>';
  }
  if ($post->dimensions != NULL) {
    $content .= '<dt>Dimensions (mm)</dt>';
    $content .= '<dd>'.$post->dimensions.'</dd>';
   }
  $content .= '</dl>';
  $content .= $after;
  echo $content;
}

function sold_or_not($before = '', $after = '', $id = 0) {
  if ( ! has_category('for-sale') ) {
    $content = $before;
    $content .= '<div class="sold-flag text-danger border border-danger">sold</div>';
    $content .= $after;
    echo $content;
  }
}

?>
