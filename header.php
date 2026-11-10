<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="top"><div class="wrap">
  <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
  <nav aria-label="Principal">
    <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'dn_menu_respaldo']); ?>
  </nav>
</div></header>
<main>
