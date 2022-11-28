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
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $id = HTML::sanitize($_GET['aID']);

  $sql_array = [
    'id',
    'user_name',
    'name',
    'first_name',
    'access'
  ];
  $Qadmin = $CLICSHOPPING_Administrators->db->get('administrators', $sql_array, ['id' => (int)$id]);
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
            class="col-md-7 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Administrators->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-end">
<?php
  echo HTML::form('administrator', $CLICSHOPPING_Administrators->link('Administrators&Update&aID=' . $Qadmin->valueInt('id')), 'post', 'autocomplete="off"');
  echo HTML::button($CLICSHOPPING_Administrators->getDef('button_update'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_Administrators->getDef('button_cancel'), null, $CLICSHOPPING_Administrators->link('Administrators'), 'warning');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle"><strong><?php echo $Qadmin->value('user_name'); ?></strong></div>
  <div class="adminformTitle">

    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="code"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_insert_intro'); ?></label>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="code"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Administrators->getDef('text_info_name'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('name', $Qadmin->value('name'), 'required aria-required="true" autocomplete="off" placeholder="' . $CLICSHOPPING_Administrators->getDef('text_info_name') . '"'); ?>
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
            <?php echo HTML::inputField('first_name', $Qadmin->value('first_name'), 'required aria-required="true" autocomplete="off" placeholder="' . $CLICSHOPPING_Administrators->getDef('text_info_first_name') . '"'); ?>
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
            <?php echo HTML::inputField('username', $Qadmin->value('user_name'), 'required aria-required="true" autocomplete="off" placeholder="' . $CLICSHOPPING_Administrators->getDef('text_info_username') . '"', 'email'); ?>
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
            <?php echo HTML::selectMenu('access_administrator', AdministratorAdmin::getAdministratorRight($CLICSHOPPING_Administrators->getDef('text_selected')), $Qadmin->value('access')); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </form>
</div>
<script defer src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/generate_password.js'); ?>"></script>
