<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {
     public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ChatGpt = Registry::get('ChatGpt');
      $CLICSHOPPING_Composer = Registry::get('Composer');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_ChatGpt->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('ChatGptAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installChatGptDb();
      $CLICSHOPPING_Composer->install('openai-php/client');
      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ChatGpt->getDef('alert_module_install_success'), 'success', 'ChatGpt');

      $CLICSHOPPING_ChatGpt->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_ChatGpt = Registry::get('ChatGpt');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_chatgpt']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = [
          'sort_order' => 100,
          'link' => 'index.php?A&Configuration\ChatGpt&ChatGpt&Configure',
          'image' => 'chatgpt.gif',
          'b2b_menu' => 0,
          'access' => 1,
          'app_code' => 'app_configuration_chatgpt'
        ];

        $insert_sql_data = ['parent_id' => 14];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_ChatGpt->getDef('title_menu')];

          $insert_sql_data = [
            'id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }

    private static function installChatGptDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_gpt"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_gpt (
  gpt_id int(11) NOT NULL,
  question text NOT NULL,
  response text NOT NULL,
  date_added date DEFAULT NULL
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_gpt  ADD PRIMARY KEY (gpt_id);
ALTER TABLE :table_gpt  MODIFY gpt_id int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE :table_gpt_usage (
  usage_id int(11) NOT NULL,
  gpt_id int(11) NOT NULL,
  promptTokens int(11) DEFAULT NULL,
  completionTokens int(11) DEFAULT NULL,
  totalTokens int(11) DEFAULT NULL,
  ia_type varchar(255) DEFAULT NULL,
  model varchar(255) DEFAULT NULL,
  date_added date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE :table_gpt_usage  ADD PRIMARY KEY (usage_id);
ALTER TABLE :table_gpt_usage  MODIFY usage_id int(11) NOT NULL AUTO_INCREMENT;

EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
