<?php get_header(); ?>
<section class="block"><div class="wrap">
  <h2><?php the_title(); ?></h2>
  <div class="members">
  <?php $q = new WP_Query(['post_type' => 'socio', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
  if ($q->have_posts()) : while ($q->have_posts()) : $q->the_post(); ?>
    <div class="m">
      <div class="av"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></div>
      <div><b><?php the_title(); ?></b><span><?php echo esc_html(get_post_meta(get_the_ID(), 'dn_juego', true)); ?></span></div>
    </div>
  <?php endwhile; wp_reset_postdata(); else : ?>
    <p>Aún no hay socios publicados. Añádelos en el panel, en Socios &gt; Añadir socio.</p>
  <?php endif; ?>
  </div>
</div></section>
<?php get_footer();
