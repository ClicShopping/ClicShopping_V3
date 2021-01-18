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

  use ClicShopping\OM\HTML;
?>


<div class="card">
  <div class="card-header">
    <?php echo TEXT_TITLE_WELCOME; ?>
  </div>
  <div class="card-block">
    <p class="card-text">
      <form action="index.php" method="get">
        <?php echo HTML::selectMenu('language', $languages_array, $language, 'onChange="this.form.submit();"'); ?>
      </form>
    </p>
  </div>
</div>

<div class="separator"></div>
<p><?php echo TEXT_LICENCE; ?></p>

<div class="separator"></div>
<div class="card">
  <div class="card-header">
    License
  </div>
  <div class="card-block">
    <p class="card-text col-md-12">
      <?php include_once('license.txt'); ?>
    </p>
  </div>
</div>

<div class="separator"></div>
<?php echo HTML::form('form', 'verify.php'); ?>
  <div class="col-md-12 text-end">
    <?php echo HTML::button(TEXT_ACCEPT_LICENCE, null, null, 'success'); ?>
  </div>
</form>
