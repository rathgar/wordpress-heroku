<?php
/**
 * The template for displaying For Sale category.
 *
 *
 * @since
 */
get_header(); ?>

	<div class="container">
		<div class="row">
			<section id="primary" <?php bavotasan_primary_attr(); ?>>

				<?php if ( have_posts() ) : ?>
					<header id="archive-header">
						<?php if ( is_author() ) echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
						<h1 class="page-title">For Sale</h1>
            <?php if (!is_paged()) {
						the_archive_description( '<div class="archive-meta">', '</div>' );
            } ?>
					</header><!-- #archive-header -->

					<?php
					while ( have_posts() ) : the_post();

						/* Include the post format-specific template for the content. If you want to
						 * this in a child theme then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
						get_template_part( 'template-parts/content', get_post_format() );

					endwhile;

					the_posts_navigation();
				else :
					get_template_part( 'template-parts/content', 'none' );
				endif;
				?>

			</section><!-- #primary.c8 -->
			<?php get_sidebar(); ?>
		</div>
	</div>

<?php get_footer(); ?>
