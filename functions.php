<?php
/**
 * Lowdesign Margo — clean bootstrap with Vite bundles
 */

define('LD_THEME_DIR', get_stylesheet_directory());
define('LD_THEME_URI', get_stylesheet_directory_uri());

/** --- Admin заметка, если нет сборки --- */
add_action('admin_notices', function () {
  if (!current_user_can('manage_options')) return;
  if (!ld_vite_manifest_path()) {
    echo '<div class="notice notice-warning"><p><strong>Lowdesign Margo:</strong> не найден <code>build/manifest.json</code> (или <code>build/.vite/manifest.json</code>). Собери ассеты локально и закоммить в тему.</p></div>';
  }
});

/** --- Helpers для manifest.json (Vite 5 может писать в .vite/) --- */
function ld_vite_manifest_path(): ?string {
  $base = LD_THEME_DIR . '/build';
  foreach ([$base.'/manifest.json', $base.'/.vite/manifest.json'] as $p) {
    if (file_exists($p)) return $p;
  }
  return null;
}

function ld_vite_asset_uri(string $entry): ?string {
  $manifest = ld_vite_manifest_path();
  if (!$manifest) return null;
  $data = json_decode(file_get_contents($manifest), true);
  if (!is_array($data) || empty($data[$entry]['file'])) return null;
  $rel = ltrim($data[$entry]['file'], '/');
  return LD_THEME_URI . '/build/' . $rel;
}

/** --- Подключение ассетов --- */
add_action('wp_enqueue_scripts', function () {
  // 1) Готовый Bootstrap
  $vendor_rel = '/assets/vendor/bootstrap/custom.css';
  $vendor_abs = LD_THEME_DIR . $vendor_rel;
  if (file_exists($vendor_abs)) {
    wp_enqueue_style('ld-bootstrap-custom', LD_THEME_URI . $vendor_rel, [], filemtime($vendor_abs));
  }

  // 2) CSS/JS из Vite
  if ($css = ld_vite_asset_uri('assets/src/scss/main.scss')) {
    wp_enqueue_style('ld-main', $css, ['ld-bootstrap-custom'], null);
  }
  if ($js = ld_vite_asset_uri('assets/src/js/main.js')) {
    wp_enqueue_script('ld-main', $js, [], null, true);
  }
}, 20);

/** --- Стили в редактор (Гутенберг) --- */
add_action('enqueue_block_editor_assets', function () {
  if ($css = ld_vite_asset_uri('assets/src/scss/main.scss')) {
    wp_enqueue_style('ld-editor', $css, [], null);
  }
});

/** --- Локализация --- */
add_action('after_setup_theme', function () {
  load_theme_textdomain('lowdesign-margo', LD_THEME_DIR . '/languages');
});

/** --- ACF JSON --- */
add_filter('acf/settings/save_json', fn()=> LD_THEME_DIR . '/acf-json');
add_filter('acf/settings/load_json', function($paths){
  $paths[] = LD_THEME_DIR . '/acf-json';
  return $paths;
});

/** --- Базовые фичи темы --- */
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','script','style','navigation-widgets']);
  register_nav_menus([
    'primary' => __('Primary Menu', 'lowdesign-margo'),
    'footer'  => __('Footer Menu', 'lowdesign-margo'),
  ]);
});

/** --- Точка входа инкрементов --- */
require_once LD_THEME_DIR . '/inc/bootstrap.php';