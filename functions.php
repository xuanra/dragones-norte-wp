<?php
if (!defined('ABSPATH')) exit;

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(['primary' => 'Menú principal']);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('dn-fonts', 'https://fonts.googleapis.com/css2?family=Pirata+One&family=Source+Sans+3:wght@400;600;700&display=swap', [], null);
    wp_enqueue_style('dn-style', get_stylesheet_uri(), ['dn-fonts'], wp_get_theme()->get('Version'));
});

/* Tipos de contenido: Socio (público) y Solicitud de alta (solo admin) */
add_action('init', function () {
    register_post_type('socio', [
        'labels' => ['name' => 'Socios', 'singular_name' => 'Socio', 'add_new_item' => 'Añadir socio'],
        'public' => true, 'has_archive' => false, 'rewrite' => ['slug' => 'socio'],
        'menu_icon' => 'dashicons-groups', 'supports' => ['title', 'thumbnail'],
    ]);
    register_post_type('solicitud', [
        'labels' => ['name' => 'Solicitudes de alta', 'singular_name' => 'Solicitud'],
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-clipboard',
        'supports' => ['title'], 'capabilities' => ['create_posts' => 'do_not_allow'], 'map_meta_cap' => true,
    ]);
});

/* Campo "Juego" del socio */
add_action('add_meta_boxes', function () {
    add_meta_box('dn_juego', 'Datos del socio', function ($post) {
        wp_nonce_field('dn_socio', 'dn_socio_nonce');
        echo '<label>Juego favorito<br><input type="text" name="dn_juego" style="width:100%" value="' . esc_attr(get_post_meta($post->ID, 'dn_juego', true)) . '"></label>';
    }, 'socio');
});
add_action('save_post_socio', function ($id) {
    if (!isset($_POST['dn_socio_nonce']) || !wp_verify_nonce($_POST['dn_socio_nonce'], 'dn_socio')) return;
    if (!current_user_can('edit_post', $id)) return;
    update_post_meta($id, 'dn_juego', sanitize_text_field($_POST['dn_juego'] ?? ''));
});

/* Ajustes del club en Apariencia > Personalizar > Datos del club */
function dn_campos() {
    return [
        'dn_instagram' => ['URL de Instagram', 'esc_url_raw'],
        'dn_facebook'  => ['URL de Facebook', 'esc_url_raw'],
        'dn_maps'      => ['Enlace de Google Maps', 'esc_url_raw'],
        'dn_direccion' => ['Dirección del club', 'sanitize_text_field'],
        'dn_horario'   => ['Horario', 'sanitize_text_field'],
        'dn_feed'      => ['Shortcode del feed de fotos (plugin de Instagram/Facebook)', 'wp_kses_post'],
    ];
}
add_action('customize_register', function ($c) {
    $c->add_section('dn', ['title' => 'Datos del club', 'priority' => 30]);
    foreach (dn_campos() as $k => [$label, $san]) {
        $c->add_setting($k, ['default' => '', 'sanitize_callback' => $san]);
        $c->add_control($k, ['label' => $label, 'section' => 'dn', 'type' => 'text']);
    }
});
function dn($k) { return get_theme_mod($k, ''); }

/* Menú de respaldo si aún no hay menú creado */
function dn_menu_respaldo() {
    echo '<ul>';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Inicio</a></li>';
    foreach (['socios' => 'Socios', 'alta' => 'Hazte socio'] as $slug => $t) {
        $p = get_page_by_path($slug);
        if ($p) echo '<li><a href="' . esc_url(get_permalink($p)) . '">' . esc_html($t) . '</a></li>';
    }
    echo '</ul>';
}

/* Formulario de alta: guarda la solicitud y avisa por correo */
add_action('admin_post_nopriv_dn_alta', 'dn_procesar_alta');
add_action('admin_post_dn_alta', 'dn_procesar_alta');
function dn_procesar_alta() {
    $volver = wp_get_referer() ?: home_url('/');
    $volver = remove_query_arg('alta', $volver);
    if (!isset($_POST['dn_nonce']) || !wp_verify_nonce($_POST['dn_nonce'], 'dn_alta') || !empty($_POST['web'])) {
        wp_safe_redirect(add_query_arg('alta', 'error', $volver)); exit;
    }
    $nombre = sanitize_text_field($_POST['nombre'] ?? '');
    $email  = sanitize_email($_POST['email'] ?? '');
    $tel    = sanitize_text_field($_POST['tel'] ?? '');
    $nac    = sanitize_text_field($_POST['nac'] ?? '');
    $juego  = sanitize_text_field($_POST['juego'] ?? '');
    $msg    = sanitize_textarea_field($_POST['msg'] ?? '');
    if (!$nombre || !is_email($email) || empty($_POST['privacidad'])) {
        wp_safe_redirect(add_query_arg('alta', 'error', $volver)); exit;
    }
    $id = wp_insert_post(['post_type' => 'solicitud', 'post_status' => 'publish', 'post_title' => $nombre . ' – ' . current_time('d/m/Y')]);
    foreach (compact('email', 'tel', 'nac', 'juego', 'msg') as $k => $v) update_post_meta($id, 'dn_' . $k, $v);
    wp_mail(get_option('admin_email'), 'Nueva solicitud de alta: ' . $nombre,
        "Nombre: $nombre\nCorreo: $email\nTeléfono: $tel\nNacimiento: $nac\nJuego: $juego\n\n$msg",
        ['Reply-To: ' . $email]);
    wp_safe_redirect(add_query_arg('alta', 'ok', $volver)); exit;
}

/* Columnas útiles en el listado de solicitudes */
add_filter('manage_solicitud_posts_columns', fn($c) => ['cb' => $c['cb'], 'title' => 'Solicitante', 'dn_email' => 'Correo', 'dn_juego' => 'Juego', 'date' => 'Fecha']);
add_action('manage_solicitud_posts_custom_column', function ($col, $id) {
    if ($col === 'dn_email') echo esc_html(get_post_meta($id, 'dn_email', true));
    if ($col === 'dn_juego') echo esc_html(get_post_meta($id, 'dn_juego', true));
}, 10, 2);
