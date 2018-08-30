<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
?>
    </div>
  </div>

<div class="clearfix"></div>
<div class="separator"></div>
<footer>
  <div id="footer">
    <div class="footerCadre">
      <span class="navbar navbar-fixed-bottom navbar-light bg-faded">
        <div class="col-md-12 navbar-collapse text-md-left" id="footer-body">
          <span class="col-md-3 navbar-text">
            <?php echo 'ClicShopping™ - V. '. CLICSHOPPING::getVersion(); ?> - &copy; 2008 - <?php echo date("Y"); ?><br />
            <small>based on osCommerce™</small>
          </span>
<?php
  if (isset($_SESSION['admin'])) {
?>
          <span class="col-md-4 navbar-text nav-item text-md-center"><?php echo $CLICSHOPPING_Language->getLanguageText(); ?></span>
          <span class="col-md-4 navbar-text float-md-right text-md-center footerHelp">
<?php
  echo CLICSHOPPING::getDef('text_legend');
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', CLICSHOPPING::getDef('image_edit')) .' ' . CLICSHOPPING::getDef('image_edit') .' - ';
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/copy.gif', CLICSHOPPING::getDef('image_copy_to')) .' ' . CLICSHOPPING::getDef('image_copy_to') .' - ';
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', CLICSHOPPING::getDef('image_delete')) .' ' . CLICSHOPPING::getDef('image_delete') .' - ';
  echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', CLICSHOPPING::getDef('image_preview')) .' ' . CLICSHOPPING::getDef('image_preview') . ' - ' ;
?>
            <span class="col-md-1 navbar-text float-md-right text-md-right go-top"><a href="#"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/footer/top.gif', 'Retour en haut de la page', '16', '16'); ?></a></span>
<?php
  }
?>
          </span>
        </div>
      </span>
    </div>
  </div>
</footer>

<!-- if the page request contains a link to a tab, open that tab on page load -->
    <script>
      $(function() {
        var url = document.location.toString();

        if (url.match('#')) {
        if ($('.nav-tabs a[data-target="#' + url.split('#')[1] + '"]').length === 1) {
            $('.nav-tabs a[data-target="#' + url.split('#')[1] + '"]').tab('show');
          }
        }
      });
    </script>


<!--smartmenu -->
    <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/smartmenus_config.js'); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.0.1/jquery.smartmenus.min.js"></script>

<!-- autocompletion -->
    <script  src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/tokeninput/jquery.tokeninput.min.js'); ?>"></script>
<!-- seo count words -->
    <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/charcount/charCount.js'); ?>"></script>

<!-- Modal with remote url -->
    <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/bootstrap/ajax_form/bootstrap_modal_remote_url.js'); ?>"></script>

<!-- Tab bootstrap -->
    <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/bootstrap/tab/bootstrap_tab.js'); ?>"></script>

<!-- date picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.<?php echo CLICSHOPPING::getDef('jquery_datepicker_language'); ?>.min.js"></script>

<?php include(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/javascript/clicshopping/ClicShoppingAdmin/datepicker.php'); ?>

<!-- chartist -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.js"></script>

<!--fixe footer -->
    <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/footer.js'); ?>"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
</html>
