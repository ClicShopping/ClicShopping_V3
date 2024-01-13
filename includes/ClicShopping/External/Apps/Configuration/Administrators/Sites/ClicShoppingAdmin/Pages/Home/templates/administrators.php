<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;
use ClicShopping\OM\ObjectInfo;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Administrators = Registry::get('Administrators');
$CLICSHOPPING_Page = Registry::get('Site')->getPage();
$CLICSHOPPING_Language = Registry::get('Language');
$CLICSHOPPING_Template = Registry::get('TemplateAdmin');

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

$Qadmin = $CLICSHOPPING_Administrators->db->get('administrators', ['id',
  'user_name',
  'name',
  'first_name',
  'access'
],
  null,
  'user_name'
);
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
          <span
            class="col-md-7 text-end"><?php echo HTML::button($CLICSHOPPING_Administrators->getDef('button_insert'), null, $CLICSHOPPING_Administrators->link('Insert&page=' . $page), 'success'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-1"></div>
  <table
    id="table"
    data-toggle="table"
    data-icons-prefix="bi"
    data-icons="icons"
    data-toolbar="#toolbar"
    data-buttons-class="primary"
    data-show-toggle="true"
    data-show-columns="true"
    data-mobile-responsive="true">

    <thead class="dataTableHeadingRow">
    <tr>
      <th
        data-field="administrator"><?php echo $CLICSHOPPING_Administrators->getDef('table_heading_administrators'); ?></th>
      <th data-field="user"><?php echo $CLICSHOPPING_Administrators->getDef('table_heading_user'); ?></th>
      <th data-field="right"><?php echo $CLICSHOPPING_Administrators->getDef('table_heading_right'); ?></th>
      <th data-field="action" data-switchable="false"
          class="text-end"><?php echo $CLICSHOPPING_Administrators->getDef('table_heading_action'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    while ($Qadmin->fetch() !== false) {
    if ((!isset($_GET['aID']) || (isset($_GET['aID']) && ((int)$_GET['aID'] === $Qadmin->valueInt('id')))) && !isset($aInfo)) {
      $aInfo = new ObjectInfo($Qadmin->toArray());
    }
    ?>
    <tr>
      <td><?php echo $Qadmin->value('user_name'); ?></td>
      <td><?php echo $Qadmin->value('first_name') . ' ' . $Qadmin->value('name'); ?></td>
      <td>
        <?php
        $access = $Qadmin->value('access');

        if ($access == 1) {
          echo $CLICSHOPPING_Administrators->getDef('text_all_rights_admin');
        } elseif ($access == 2) {
          echo $CLICSHOPPING_Administrators->getDef('text_rights_employee');
        } else {
          echo $CLICSHOPPING_Administrators->getDef('text_rights_visitor');
        }
        ?>
      </td>
      <td class="text-end">
        <div class="btn-group" role="group" aria-label="buttonGroup">
          <?php
          echo '<a href="' . $CLICSHOPPING_Administrators->link('Edit&aID=' . $Qadmin->valueInt('id')) . '"><h4><i class="bi bi-pencil" title="' . $CLICSHOPPING_Administrators->getDef('image_edit') . '"></i></h4></a>';
          echo '&nbsp;';
          echo '<a href="' . $CLICSHOPPING_Administrators->link('Delete&aID=' . $Qadmin->valueInt('id')) . '"><h4><i class="bi bi-trash2" title="' . $CLICSHOPPING_Administrators->getDef('image_delete') . '"></i></h4></a>';
          echo '&nbsp;';
          echo '</tr>';
          }
          ?>
        </div>
      </td>
    </tr>
    </tbody>
  </table>
</div>
