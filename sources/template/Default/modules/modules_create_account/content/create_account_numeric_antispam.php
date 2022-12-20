<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT

 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

?>
<div class="col-md-<?php echo $content_width; ?>" id="antispamNumeric">
  <div class="separator"></div>
  <div class="row col-md-12">
    <label for="inputVerificationCode" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_numeric_antispam'); ?><span class="text-warning"><?php echo HTML::outputProtected($antispam); ?></span></label>
    <div class="col-sm-6 col-md-4">
      <?php echo HTML::inputField('antispam', null, 'required aria-required="true" id="inputVerificationCode" aria-describedby="' . CLICSHOPPING::getDef('entry_numeric_antispam') . '" placeholder="' . CLICSHOPPING::getDef('entry_numeric_antispam') . '"'); ?>
    </div>
  </div>
</div>
