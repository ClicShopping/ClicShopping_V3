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
  use ClicShopping\Sites\ClicShoppingAdmin\CallUserFuncConfiguration;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Settings = Registry::get('Settings');

  $gID = (isset($_GET['gID'])) ? $_GET['gID'] : 1;

  $Qconfiguration = $CLICSHOPPING_Settings->db->get('configuration', [
                                                                      'configuration_id',
                                                                      'configuration_title',
                                                                      'configuration_value',
                                                                      'use_function'
                                                                      ], [
                                                                      'configuration_group_id' => (int)$gID
                                                                      ],
                                                                      'sort_order'
                                                  );

  while ($Qconfiguration->fetch() ) {

    if ($Qconfiguration->hasValue('use_function') && !is_null($Qconfiguration->value('use_function'))) {
      $use_function = $Qconfiguration->value('use_function');

      if (preg_match('/->/', $use_function)) {
        $class_method = explode('->', $use_function);

        if (!is_object(${$class_method[0]})) {
          include('includes/classes/'. $class_method[0] . '.php');
          ${$class_method[0]} = new $class_method[0]();
        }

        $cfgValue = CallUserFuncConfiguration::execute($class_method[1], $Qconfiguration->value('configuration_value'), ${$class_method[0]});

      } else {
        $cfgValue = CallUserFuncConfiguration::execute($use_function, $Qconfiguration->value('configuration_value'));
      }
    } else {
      $cfgValue = $Qconfiguration->value('configuration_value');
    }

    if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] === $Qconfiguration->valueInt('configuration_id')))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {

      $Qextra = $CLICSHOPPING_Settings->db->get('configuration', [
                                                                  'configuration_key',
                                                                  'configuration_description',
                                                                  'date_added',
                                                                  'last_modified',
                                                                  'use_function',
                                                                  'set_function'
                                                                  ], [
                                                                    'configuration_id' => $Qconfiguration->valueInt('configuration_id')
                                                                  ]
                                              );

      $cInfo_array = array_merge($Qconfiguration->toArray(), $Qextra->toArray());
      $cInfo = new ObjectInfo($cInfo_array);
    }
  }

  if ($cInfo->set_function) {
    $value_field = CallUserFuncConfiguration::execute($cInfo->set_function, htmlspecialchars($cInfo->configuration_value), $cInfo->configuration_key);
  } else {
    $value_field = HTML::inputField('configuration[' . $cInfo->configuration_key . ']', $cInfo->configuration_value);
  }

  echo HTML::form('configuration', $CLICSHOPPING_Settings->link('SettingsPopUp&Update&gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id));

?>
<div class="clearfix"></div>
<div class="row">
  <div class="col-md-12">
    <div class="card card-block headerCard">
      <div class="row">
        <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/configuration_' . $gID . '.gif', '', '40', '40'); ?></span>
        <span class="pageHeading col-md-7"><?php echo '&nbsp;' . $CLICSHOPPING_Settings->getDef('heading_title'); ?></span>
        <span class="col-md-3 text-md-right">&nbsp;
          <?php echo HTML::button($CLICSHOPPING_Settings->getDef('button_update'), null, null, 'success'); ?>
        </span>
      </div>
    </div>
  </div>
</div>


<div style="padding:20px 10px 30px 10px;">
  <div  style="font-weight: bold; font-size:12px;"><?php echo '&nbsp;' . $cInfo->configuration_title; ?></div>
  <div class="separator"></div>
  <div><?php echo $cInfo->configuration_description; ?></div>
  <div class="separator"></div>
  <div><?php echo $value_field; ?></div>
</div>

</form>
