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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_SecurityCheck = Registry::get('SecurityCheck');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Language->loadDefinitions('security_check');
  $info = CLICSHOPPING::getSystemInformation();
  $server = parse_url(CLICSHOPPING::getConfig('http_server'));

  function sortSecmModules($a, $b) {
    return strcasecmp($a['title'], $b['title']);
  }

  $types = ['info', 'warning', 'error'];

  $modules = [];

  if ($secdir = @dir(CLICSHOPPING::getConfig('dir_root') . 'includes/modules/security_check/')) {
    while ($file = $secdir->read()) {
      if (!is_dir(CLICSHOPPING::getConfig('dir_root') . 'includes/modules/security_check/' . $file)) {
        if (substr($file, strrpos($file, '.')) == '.php') {
          $class = 'securityCheck_' . substr($file, 0, strrpos($file, '.'));

          include(CLICSHOPPING::getConfig('dir_root') . 'includes/modules/security_check/' . $file);
          $$class = new $class();

          $modules[] = ['title' => isset($$class->title) ? $$class->title : substr($file, 0, strrpos($file, '.')),
                        'class' => $class,
                        'code' => substr($file, 0, strrpos($file, '.'))
                       ];
        }
      }
    }
    $secdir->close();
  }

  if ($extdir = @dir(CLICSHOPPING::getConfig('dir_root') . 'includes/modules/security_check/extended/')) {
    while ($file = $extdir->read()) {
      if (!is_dir(CLICSHOPPING::getConfig('dir_root') . 'includes/modules/security_check/extended/' . $file)) {

        if (substr($file, strrpos($file, '.')) == '.php') {
          $class = 'securityCheckExtended_' . substr($file, 0, strrpos($file, '.'));

          include(CLICSHOPPING::getConfig('dir_root') . 'includes/modules/security_check/extended/' . $file);

            $$class = new $class();

            $modules[] = ['title' => isset($$class->title) ? $$class->title : substr($file, 0, strrpos($file, '.')),
                          'class' => $class,
                          'code' => substr($file, 0, strrpos($file, '.'))
                         ];
        }
      }
    }
    $extdir->close();
  }

  usort($modules, 'sortSecmModules');
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/cybermarketing.gif', $CLICSHOPPING_SecurityCheck->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-5 pageHeading"><?php echo  $CLICSHOPPING_SecurityCheck->getDef('heading_title'); ?></span>
            <span class="col-md-6 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_SecurityCheck->getDef('button_reset'), null, $CLICSHOPPING_SecurityCheck->link('SecurityCheck'), 'warning') . ' ';
  echo HTML::button($CLICSHOPPING_SecurityCheck->getDef('button_reload'), null, $CLICSHOPPING_SecurityCheck->link('SecurityCheck'), 'primary');
?>
             </span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <td>
        <table class="table table-sm table-hover table-striped">
          <thead>
            <tr class="dataTableHeadingRow">
              <th width="20">&nbsp;</th>
              <th><?php echo $CLICSHOPPING_SecurityCheck->getDef('table_heading_title'); ?></td>
              <th><?php echo $CLICSHOPPING_SecurityCheck->getDef('table_heading_module'); ?></th>
              <th><?php echo $CLICSHOPPING_SecurityCheck->getDef('table_heading_info'); ?></th>
            </tr>
          <thead>
          <tbody>

<?php
  foreach ($modules as $module) {
    $secCheck = $GLOBALS[$module['class']];

    if ( !in_array($secCheck->type, $types) ) {
      $secCheck->type = 'info';
    }

    $output = '';

    if ( $secCheck->pass() ) {
      $secCheck->type = 'success';
    } else {
      $output = $secCheck->getMessage();
    }

    echo '  <tr>' . "\n" .
         '    <td class=text-md-center">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/ms_' . $secCheck->type . '.png', '', 16, 16) . '</td>' . "\n" .
         '    <td valign="top" style="white-space: nowrap;">' . HTML::outputProtected($module['title']) . '</td>' . "\n" .
         '    <td>' . HTML::outputProtected($module['code']) . '</td>' . "\n" .
         '    <td>' . $output . '</td>' . "\n" .
         '  </tr>' . "\n";
  }
?>
          <tbody>
        </table>
      </td>
    </table>
  </div>