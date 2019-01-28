<?php
/**
 * WordPress Bootstrap Pagination
 * https://github.com/talentedaamer/Bootstrap-wordpress-pagination
 */

function wp_bootstrap_pagination( $args = array() ) {

    $defaults = array(
        'range'           => 4,
        'custom_query'    => FALSE,
        'previous_string' => __( 'Previous', 'text-domain' ),
        'next_string'     => __( 'Next', 'text-domain' ),
        'before_output'   => '<nav class="text-center" aria-label="Page navigation"><ul class="pagination">',
        'after_output'    => '</ul></nav>',
        'li_class'        => 'page-item',
        'a_class'         => 'page-link',
        'disabled_class'  => 'disabled',
        'active_class'    => 'active',
        'show_first_last' => FALSE
    );

    $args = wp_parse_args(
        $args,
        apply_filters( 'wp_bootstrap_pagination_defaults', $defaults )
    );

    $args['range'] = (int) $args['range'] - 1;
    if ( !$args['custom_query'] )
        $args['custom_query'] = @$GLOBALS['wp_query'];
    $count = (int) $args['custom_query']->max_num_pages;
    $page  = intval( get_query_var( 'paged' ) );
    $ceil  = ceil( $args['range'] / 2 );

    if ( $count <= 1 )
        return FALSE;

    if ( !$page )
        $page = 1;

    if ( $count > $args['range'] ) {
        if ( $page <= $args['range'] ) {
            $min = 1;
            $max = $args['range'] + 1;
        } elseif ( $page >= ($count - $ceil) ) {
            $min = $count - $args['range'];
            $max = $count;
        } elseif ( $page >= $args['range'] && $page < ($count - $ceil) ) {
            $min = $page - $ceil;
            $max = $page + $ceil;
        }
    } else {
        $min = 1;
        $max = $count;
    }

    $echo = '';
    $previous = intval($page) - 1;
    $previous = esc_attr( get_pagenum_link($previous) );

    if ( $args['show_first_last'] ) {
      $firstpage = esc_attr( get_pagenum_link(1) );
      $disabled = ( $firstpage && (1 != $page) ) ? '' : $args['disabled_class'];
      $echo .= '<li class="page-item' . $disabled . '"><a class="' . $args['a_class'] . '" href="' . $firstpage . '">' . __( 'First', 'text-domain' ) . '</a></li>';
    }

    $disabled = ( $previous && (1 != $page) );
    $echo .= '<li class="' . join(' ', [$args['li_class'], $disabled]) . '"><a class="' . $args['a_class'] . '" href="' . $previous . '" title="' . __( 'previous', 'text-domain') . '">' . $args['previous_string'] . '</a></li>';

    if ( !empty($min) && !empty($max) ) {
        for( $i = $min; $i <= $max; $i++ ) {
            $isactive = ($page == $i) ? ' active' : '';
            $echo .= sprintf( '<li class="' . join(' ', [$args['li_class'], $isactive]) . '"><a class="' . $args['a_class'] . '" href="%s">%d</a></li>', esc_attr( get_pagenum_link($i) ), $i );
        }
    }

    $next = intval($page) + 1;
    $disabled = ($count != $page) ? '' : $args['disabled_class'];
    $next_link = esc_attr( get_pagenum_link($next) );
    $echo .= '<li class="' . join(' ', [$args['li_class'], $disabled]) . '"><a class="' . $args['a_class'] . '" href="' . $next_link . '" title="' . __( 'next', 'text-domain') . '">' . $args['next_string'] . '</a></li>';

    if ( $args['show_first_last'] ) {
      $lastpage = esc_attr( get_pagenum_link($count) );
      $disabled = ($count != $page) ? '' : $args['disabled_class'];
      $echo .= '<li class="' . join(' ', [$args['li_class'], $disabled]) . '"><a class="' . $args['a_class'] . '" href="' . $lastpage . '">' . __( 'Last', 'text-domain' ) . '</a></li>';
    }

    if ( isset($echo) )
        echo $args['before_output'] . $echo . $args['after_output'];
}

function wp_bootstrap_link($attrs=[],$text='',$before='',$after='') {
  $output = '<a ';
  foreach ($attrs as $attr => $value) {
    $output .= "{$attr}=\"{$value}\" ";
  }
  $output .= ">{$text}</a>";
  return $before . $output . $after;
}
