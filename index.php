<?php get_header(); ?>
<section class="block"><div class="wrap entry">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <article>
    <h2><?php the_title(); ?></h2>
    <?php the_content(); ?>
  </article>
<?php endwhile; endif; ?>
</div></section>
<?php get_footer();
