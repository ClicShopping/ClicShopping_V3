<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  $CLICSHOPPING_Administrators = Registry::get('Administrators');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/administrators.gif', $CLICSHOPPING_Administrators->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Administrators->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-end">
<?php
  echo HTML::button($CLICSHOPPING_Administrators->getDef('button_cancel'), null, $CLICSHOPPING_Administrators->link('Administrators'), 'warning') . ' ';
  echo HTML::form('administrators', $CLICSHOPPING_Administrators->link('Administrators&Insert&page=' . $page), 'post', 'autocomplete="off"');
  echo HTML::button($CLICSHOPPING_Administrators->getDef('button_insert'), null, null, 'success')
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Administrators->getDef('text_info_heading_new_administrator'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="code"
                 class="col-12 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_insert_intro'); ?></label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="code"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_name'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('name', null, 'required aria-required="true" placeholder="' . $CLICSHOPPING_Administrators->getDef('text_info_name') . '"'); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="code"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_firstname'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('first_name', null, 'required aria-required="true" placeholder="' . $CLICSHOPPING_Administrators->getDef('text_info_firstname') . '"'); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="code"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_username'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('username', null, 'required aria-required="true" placeholder="' . $CLICSHOPPING_Administrators->getDef('text_info_username') . '"', 'email'); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="code"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_password'); ?></label>
          <div class="col-md-5">
            <div class="input-group" role="group" aria-label="buttonGroup">
              <span><?php echo HTML::inputField('password', null, 'id="input-password" required aria-required="true" autocomplete="off" placeholder="' . $CLICSHOPPING_Administrators->getDef('text_info_password') . '"'); ?></span>
              <span><button type="button" id="button-generate" class="btn btn-primary"><i class="bi bi-arrow-clockwise"></i></button></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="code"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_access'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::selectMenu('access_administrator', AdministratorAdmin::getAdministratorRight($CLICSHOPPING_Administrators->getDef('text_selected'))); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </form>
</div>
<script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/generate_password.js'); ?>"></script>
