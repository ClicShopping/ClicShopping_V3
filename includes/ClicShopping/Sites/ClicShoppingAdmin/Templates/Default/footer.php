<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Template = Registry::get('TemplateAdmin');
$CLICSHOPPING_Hooks = Registry::get('Hooks');
$CLICSHOPPING_Language = Registry::get('Language');

if (VERTICAL_MENU_CONFIGURATION == 'true') {
  ?>
  <!-- vertical menu start -->
  </div>
  </div>
  </div>
  <!-- end vertical menu -->
  <?php
}
?>
</div>
</div>
<!-- end center page-->

<div class="separator"></div>
<div class="separator"></div>
<footer id="footer">
  <nav class="navbar fixed-bottom navbar-light bg-faded footerCadre" role="navigation">
    <div class="navbar-collapse text-start" id="footer-body">
      <div class="row">
        <?php
        if (isset($_SESSION['admin'])) {
          ?>
          <span class="col-md-2 navbar-text">
                <?php echo 'ClicShopping™ - V. ' . CLICSHOPPING::getVersion(); ?> - &copy; 2008 - <?php echo date('Y'); ?><br/>
              </span>
          <span class="col-md-7 navbar-text nav-item text-center">
                <?php echo $CLICSHOPPING_Language->getLanguageText(); ?>
              </span>
          <span class="col-md-2 navbar-text text-end footerHelp go-top">Licence MIT - GPL2</span>
          <?php
        }
        ?>
      </div>
    </div>
  </nav>
</footer>
<div class="separator"></div>
</div>
<script defer
        src="<?php echo CLICSHOPPING::link("Shop/ext/javascript/clicshopping/ClicShoppingAdmin/page_loader.js"); ?>"></script>
<?php
$source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/ClicShoppingAdmin/Footer/';
$file_get_output = 'FooterOutput*';
$file_get_call = 'FooterCall*';
$hook_call = 'Footer';

$CLICSHOPPING_Template->useRecursiveModulesHooksForTemplate($source_folder, $file_get_output, $file_get_call, $hook_call);
?>
</body>
</html>
