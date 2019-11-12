<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
?>
</div>
<footer>
  <div id="footer">
   <nav class="navbar navbar-fixed-bottom navbar-light bg-faded">
     <div class="row footerCadre">
     <div class="col-md-12 navbar-collapse text-md-left" id="footer-body">
        <span class="col-md-3 navbar-text">
          <?php echo 'ClicShopping™ - V. ' . CLICSHOPPING::getVersion(); ?> - &copy; 2008 - <?php echo date('Y'); ?><br/>
        </span>
<?php
  if (isset($_SESSION['admin'])) {
    ?>
       <span class="col-md-4 navbar-text nav-item text-md-center">
          <?php echo $CLICSHOPPING_Language->getLanguageText(); ?>
       </span>
       <span class="col-md-4 navbar-text float-md-right text-md-center footerHelp">
<?php
  echo CLICSHOPPING::getDef('text_legend');
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', CLICSHOPPING::getDef('image_edit')) . ' ' . CLICSHOPPING::getDef('image_edit') . ' - ';
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/copy.gif', CLICSHOPPING::getDef('image_copy_to')) . ' ' . CLICSHOPPING::getDef('image_copy_to') . ' - ';
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', CLICSHOPPING::getDef('image_delete')) . ' ' . CLICSHOPPING::getDef('image_delete') . ' - ';
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', CLICSHOPPING::getDef('image_preview')) . ' ' . CLICSHOPPING::getDef('image_preview') . ' - ';
?>
       <span class="col-md-1 navbar-text float-md-right text-md-right go-top">
         <a href="#"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/footer/top.gif', 'Retour en haut de la page', '16', '16'); ?></a></span>
      </span>
    <?php
  }
?>
     </div>
    </div>
   </nav>
  </div>
  <div class="footerCadre separator"></div>
</footer>
<?php
    $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/ClicShoppingAdmin/Footer/';

    if (is_dir($source_folder)) {
      $files_get_output = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'FooterOutput*');
      $files_get_call = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'FooterCall*');

      foreach ($files_get_output as $value) {
        if (!empty($value['name'])) {
          echo $CLICSHOPPING_Hooks->output('Footer', $value['name'], null, 'display');
        }
      }

      foreach ($files_get_call as $value) {
        if (!empty($value['name'])) {
          $CLICSHOPPING_Hooks->call('Footer', $value['name']);
        }
      }
    }
?>
</html>
