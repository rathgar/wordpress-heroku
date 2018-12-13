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
        <figcaption class="wp-caption-text artwork-caption clearfix">
          <h2><?php echo $post->post_title; ?></h2>
          <dl class="artwork-meta">
            <?php if ($post->media != NULL) { ?>
            <dt>Media</dt>
            <dd><?php echo $post->media; ?></dd>
          <?php } if ($post->dimensions != NULL) { ?>
            <dt>Dimensions (mm)</dt>
            <dd><?php echo $post->dimensions; ?></dd><?php } ?>
          </dl>
          <?php the_content( __( 'Read more', 'arcade-basic-child') ); ?>

          <?php if (has_category('for-sale')): ?>
            <p class="for-sale">"<?php echo $post->post_title; ?>" may still be available for sale - <a href="/index.php/artwork-enquiry?artwork-id=<?php echo urlencode(get_the_ID()); ?>&artwork-title=<?php echo urlencode($post->post_title); ?>">make an enquiry</a>.</p>
          <?php endif ?>
        </figcaption>
      </figure>
    <?php
      else :
        the_excerpt();
      endif;
    ?>
  </div><!-- .entry-content -->
  <?php if ( is_singular() && ! is_front_page() )
    get_template_part( 'template-parts/content', 'footer' ); ?>
</article><!-- #post-<?php the_ID(); ?> -->
