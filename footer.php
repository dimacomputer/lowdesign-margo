<?php if (!defined('ABSPATH')) exit; ?>
</main>
<footer class="site-footer mt-5">
  <div class="container py-4 d-flex justify-content-between align-items-center">
    <small>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></small>
    <?php if (has_nav_menu('footer')): ?>
      <nav><?php wp_nav_menu(['theme_location'=>'footer','menu_class'=>'nav small','container'=>false]); ?></nav>
    <?php endif; ?>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>