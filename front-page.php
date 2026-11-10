<?php get_header(); ?>
<div class="hero"><div class="wrap">
  <h1><?php bloginfo('name'); ?></h1>
  <p><?php bloginfo('description'); ?></p>
  <div class="row">
    <?php $alta = get_page_by_path('alta'); ?>
    <a class="btn" href="<?php echo esc_url($alta ? get_permalink($alta) : '#'); ?>">Hazte socio</a>
    <a class="btn ghost" href="#donde">Cómo llegar</a>
  </div>
</div></div>

<section class="block"><div class="wrap">
  <h2>Últimas noticias</h2>
  <div class="news">
  <?php $q = new WP_Query(['posts_per_page' => 6, 'ignore_sticky_posts' => true]);
  if ($q->have_posts()) : while ($q->have_posts()) : $q->the_post(); ?>
    <article class="n">
      <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
      <h3><a href="<?php the_permalink(); ?>" style="text-decoration:none"><?php the_title(); ?></a></h3>
      <p><?php echo esc_html(get_the_excerpt()); ?></p>
    </article>
  <?php endwhile; wp_reset_postdata(); else : ?>
    <p>Aún no hay noticias. Publica una entrada desde el panel para verla aquí.</p>
  <?php endif; ?>
  </div>
</div></section>

<section class="block social"><div class="wrap">
  <h2>Fotos de nuestras partidas</h2>
  <?php if (dn('dn_feed')) : echo do_shortcode(dn('dn_feed')); else : ?>
    <p class="note">Aquí aparecerá el feed de fotos. Instala un plugin de Instagram/Facebook y pega su shortcode en Personalizar &gt; Datos del club.</p>
  <?php endif; ?>
  <div class="row" style="margin-top:1.5rem">
    <?php if (dn('dn_instagram')) : ?><a class="btn" href="<?php echo esc_url(dn('dn_instagram')); ?>" target="_blank" rel="noopener">Síguenos en Instagram</a><?php endif; ?>
    <?php if (dn('dn_facebook')) : ?><a class="btn ghost" href="<?php echo esc_url(dn('dn_facebook')); ?>" target="_blank" rel="noopener">Visita nuestro Facebook</a><?php endif; ?>
  </div>
</div></section>

<section class="block" id="donde"><div class="wrap">
  <h2>Dónde estamos</h2>
  <?php if (dn('dn_direccion')) echo '<p>' . esc_html(dn('dn_direccion')) . '</p>'; ?>
  <?php if (dn('dn_horario')) echo '<p>' . esc_html(dn('dn_horario')) . '</p>'; ?>
  <?php if (dn('dn_maps')) : ?><a class="btn" href="<?php echo esc_url(dn('dn_maps')); ?>" target="_blank" rel="noopener">Abrir en Google Maps</a><?php endif; ?>
</div></section>
<?php get_footer();
