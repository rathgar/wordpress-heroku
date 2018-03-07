<?php
/**
 * The template for displaying posts in the Image post format
 *
 * @since
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
  <?php
  // Display a thumbnail if one exists and not on single post
  bavotasan_display_post_thumbnail();
  get_template_part( 'template-parts/content', 'header' ); ?>

  <div class="entry-content description clearfix">

    <?php if ( is_singular() && ! is_front_page() ) : ?>
      <figure class="wp-caption artwork">
        <?php the_post_thumbnail(); ?>
        <figcaption class="wp-caption-text artwork-caption">
          <h2><?php echo $post->post_title; ?></h2>
          <dl class="artwork-meta">
            <dd title="Dimensions"><?php echo $post->dimensions; ?></dd>
            <dd title="Media"><?php echo $post->media; ?></dd>
          </dl>
          <?php the_content( __( 'Read more', 'arcade-basic-child') ); ?>
        </figcaption>
      </figure>

      <?php if (has_category('for-sale')): ?>
        This work may be available <a href="/index.php/artwork-enquiry?artwork-title=<?php echo urlencode($post->post_title); ?>">for sale</a>.
      <?php endif ?>

    <?php
      else :
        the_excerpt();
      endif;
    ?>
  </div><!-- .entry-content -->
  <?php if ( is_singular() && ! is_front_page() )
    get_template_part( 'template-parts/content', 'footer' ); ?>
</article><!-- #post-<?php the_ID(); ?> -->
