<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */
   use ClicShopping\OM\CLICSHOPPING;
   use ClicShopping\OM\Registry;

   $CLICSHOPPING_Hooks = Registry::get('Hooks');
   $CLICSHOPPING_Template = Registry::get('Template');
?>
      </div><!-- end bodyContent -->

<?php
  if ($CLICSHOPPING_Template->hasBlocks('boxes_column_left')) {
?>
      <div id="columnLeft" class="col-sm-6 col-lg-<?php echo $CLICSHOPPING_Template->getGridColumnWidth(); ?> order-xs-2 order-lg-1">
        <?php echo $CLICSHOPPING_Template->getBlocks('boxes_column_left'); ?>
      </div>
<?php
  }

  if ($CLICSHOPPING_Template->hasBlocks('boxes_column_right')) {
?>
      <div id="columnRight" class="col-sm-6 col-lg-<?php echo $CLICSHOPPING_Template->getGridColumnWidth(); ?> order-xs-3 order-lg-3">
        <?php echo $CLICSHOPPING_Template->getBlocks('boxes_column_right'); ?>
      </div>
<?php
  }
?>
      </div><!-- end flex -->

      <div class="separator"></div>
<?php //important before block ?>
      <footer class="page-footer" id="footer">
        <div class="hr footerHr"></div>
        <div class="footer"><?php echo $CLICSHOPPING_Template->getBlocks('modules_footer'); ?></div>
        <div class="footerSuffix"><?php echo $CLICSHOPPING_Template->getBlocks('modules_footer_suffix'); ?></div>
      </footer>
    </div> <!-- BodyWrapper -->
  </div> <!-- container //-->
<?php
    echo $CLICSHOPPING_Template->getBlocks('footer_scripts');

    $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/Shop/Footer/';

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
          echo $CLICSHOPPING_Hooks->call('Footer', $value['name']);
        }
      }
    }
?>
  </body>
</html>
