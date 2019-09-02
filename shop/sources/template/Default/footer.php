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
      <footer>
        <div class="hr footerHr"></div>
        <div class="footer"><?php echo $CLICSHOPPING_Template->getBlocks('modules_footer'); ?></div>
        <div class="footerSuffix"><?php echo $CLICSHOPPING_Template->getBlocks('modules_footer_suffix'); ?></div>
      </footer>
    </div> <!-- BodyWrapper -->
  </div> <!-- container //-->
    <script defer src="https://kit.fontawesome.com/89fdf54890.js"></script>
    <script defer src="<?php echo CLICSHOPPING::link($CLICSHOPPING_Template->getTemplateDefaultJavaScript('clicshopping/footer.js')); ?>"></script>
    <?php echo $CLICSHOPPING_Template->getBlocks('footer_scripts'); ?>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
