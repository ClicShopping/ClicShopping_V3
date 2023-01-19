<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Github;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Upgrade = Registry::get('Upgrade');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Github = new Github();
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $cache_directory = CLICSHOPPING::BASE_DIR . 'Work/Cache/Marketplace/';

  if ($CLICSHOPPING_MessageStack->exists('main')) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row col-md-12">
          <div class="col-md-12">
            <dic class="row">
              <span
                class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/apps.png', $CLICSHOPPING_Upgrade->getDef('heading_title'), '40', '40'); ?></span>
              <span
                class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Upgrade->getDef('heading_title'); ?></span>
              <span
                class="col-md-6 text-end"><?php echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_marketplace'), null, $CLICSHOPPING_Upgrade->link('Ipb'), 'danger');  ?>
              </span>
            </div>
          </div>
        </div>
      </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12">
    <div class="row">
        <span class="alert alert-info" role="alert">
          <?php echo $CLICSHOPPING_Upgrade->getDef('text_step_upgrade'); ?>
        </span>
    </div>
    <div class="separator"></div>

    <?php
    if (isset($_SESSION['markeplace_file_id'])) {
      $id = $_SESSION['markeplace_file_id'];

      $sql_array = [
        'file_name',
        'file_url_download',
        'is_installed',
        'file_url_screenshot'
      ];

      $information = $CLICSHOPPING_Upgrade->db->get('marketplace_file_informations', $sql_array, ['file_id' => $id]);

      $json_name = 'apps_configuration_antispam.json';
    ?>
      <div class="row">
        <span class="col-md-6">
          <blockquote>
            <ul>
              <?php
                if(is_file($cache_directory . $json_name)) {
                  $get_json_file = file_get_contents($cache_directory . $json_name, true);
                  $result = json_decode($get_json_file);

                  foreach ($result as $key => $value) {
                    $text = '';

                    if (!\is_array($value)) {
                      $text = $value;
                    }

                    if ($key == 'module_directory') {
                      $_SESSION['module_directory'] = $value;
                    }

                    if ($key == 'apps_name') {
                      $_SESSION['module_apps_name'] = $value;
                    }

                    echo '<li>' . $key . ' : ' . $text . '</li>';

                    if (\is_array($value)) {
                      echo '<div class="separator"></div>';

                      foreach ($value as $item) {
                        echo '      -' . $item->name . '<br />';
                        echo '      -' . $item->company . '<br />';
                        echo '      -' . $item->email . '<br />';
                        echo '      -' . $item->website . '<br />';
                        echo '      -' . $item->Community . '<br />';
                      }
                    }
                  }

                  if (isset($_SESSION['module_directory'])) {
                    echo '<div class="separator"></div>';
                    echo '<div class="alert alert-success" role="alert">';
                    echo '<span class="text-center"><h3>';
                    echo HTML::link(CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'index.php?A&' . $_SESSION['module_directory'] . '\\' . $_SESSION['module_apps_name'], $CLICSHOPPING_Upgrade->getDef('text_activate'));
                    echo '</h3></span>';
                    echo '</div>';
                  }
                }
              ?>
            </ul>
          </blockquote>
        </span>
        <span class="col-md-6">
          <div class="text-center">
              <img src="<?php echo $information->value('file_url_screenshot'); ?>" class="figure-img img-fluid alt="<?php echo $information->value('file_name'); ?>">
          </div>
        </span>
      </div>
      <?php
        $sql_array = [
          'is_installed' => 1
        ];

        $CLICSHOPPING_Upgrade->db->save('marketplace_file_informations', $sql_array, ['file_id' => $id]);
      }
      ?>
  </div>
</div>
<?php
  unset( $_SESSION['module_apps_name']);
  unset($_SESSION['module_directory']);
  unset($_SESSION['markeplace_file_id']);


