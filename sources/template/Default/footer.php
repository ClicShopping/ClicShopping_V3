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
      <div id="columnLeft" class="row col-12 col-lg-<?php echo $CLICSHOPPING_Template->getGridColumnWidth(); ?> order-xs-2 order-lg-1">
        <div class="col m-3">
          <?php echo $CLICSHOPPING_Template->getBlocks('boxes_column_left'); ?>
        </div>
      </div>
<?php
  }

  if ($CLICSHOPPING_Template->hasBlocks('boxes_column_right')) {
?>
      <div id="columnRight" class="row col-12 col-lg-<?php echo $CLICSHOPPING_Template->getGridColumnWidth(); ?> order-xs-3 order-lg-3">
        <div class="col m-3">
          <?php echo $CLICSHOPPING_Template->getBlocks('boxes_column_right'); ?>
        </div>
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
    $file_get_output = 'FooterOutput*';
    $file_get_call = 'FooterCall*';
    $hook_call = 'Footer';

    $CLICSHOPPING_Template->useRecursiveModulesHooksForTemplate($source_folder,  $file_get_output,  $file_get_call, $hook_call);
?>
  </body>
</html>
