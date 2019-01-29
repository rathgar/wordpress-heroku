<?php
/**
 * The template for displaying posts in the Image post format
 *
 * @since
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( ['clearfix', (has_category('for-sale'))? 'for-sale' : 'not-for-sale', (is_singular()? '' : 'media')] ); ?>>
<?php if (! is_singular()): ?>
  <div class="media-left">
    <?php // Display a thumbnail if one exists and not on single post
      bavotasan_display_post_thumbnail(); ?>
  </div>
  <div class="media-body">
    <?php get_template_part( 'template-parts/content', 'header' ); ?>
    <?php echo get_the_excerpt(); ?>
  </div>
<?php else: ?>
  <?php
  get_template_part( 'template-parts/content', 'header' );
  ?>
  <div class="entry-content description clearfix">
    <?php if ( ! is_front_page() ) : ?>
      <figure class="wp-caption artwork">
        <?php the_post_thumbnail(); ?>
        <figcaption class="wp-caption-text artwork-caption clearfix">
          <h2><?php echo $post->post_title; ?></h2>
          <?php the_artwork_meta(); ?>
          <?php the_content( __( 'Read more', 'arcade-basic') ); ?>
          <?php if (has_category('for-sale')) { ?>
            <div class="clearfix call-to-action"><a href="/contact/artwork-enquiry?artwork-id=<?php echo urlencode(get_the_ID()); ?>&artwork-title=<?php echo urlencode($post->post_title); ?>" class="btn btn-light alignright">Enquire about this work</a></div>
          <?php } ?>
        </figcaption>
      </figure>
      <?php else :
        echo get_the_excerpt();
      ?>
    <?php endif; ?>
  </div><!-- .entry-content -->

<?php endif;
 if ( is_singular() && ! is_front_page() )
    get_template_part( 'template-parts/content', 'footer' ); ?>
</article><!-- #post-<?php the_ID(); ?> -->
