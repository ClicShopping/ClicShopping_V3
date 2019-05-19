<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  class ar_create_account_pro
  {
    public $code;
    public $title;
    public $description;
    public $sort_order = 0;
    public $minutes = 90;
    public $attempts = 6;
    public $identifier;

    public function __construct()
    {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_action_recorder_create_account_pro_title');
      $this->description = CLICSHOPPING::getDef('module_action_recorder_create_account_pro_description');

      if ($this->check()) {
        if (defined('MODULE_ACTION_RECORDER_CREATE_ACCOUNT_PRO_EMAIL_MINUTES')) {
          $this->minutes = (int)MODULE_ACTION_RECORDER_CREATE_ACCOUNT_PRO_EMAIL_MINUTES;
          $this->attempts = 6; // nbr de possiblite d'envoi d'email
        }
      }
    }

    public function setIdentifier()
    {
      $this->identifier = HTTP::GetIpAddress();
    }

    public function canPerform($user_id, $user_name)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $sql_query = 'select id
                    from :table_action_recorder
                    where module = :module
                   ';

      if (!empty($user_id)) {
        $sql_query .= ' and (user_id = :user_id or identifier = :identifier)';
      } else {
        $sql_query .= ' and identifier = :identifier';
      }

      $sql_query .= ' and date_added >= date_sub(now(),
                      interval :limit_minutes minute)
                      and success = 1
                      limit 1
                      ';

      $Qcheck = $CLICSHOPPING_Db->prepare($sql_query);
      $Qcheck->bindValue(':module', $this->code);

      if (!empty($user_id)) {
        $Qcheck->bindInt(':user_id', $user_id);
      }

      $Qcheck->bindValue(':identifier', $this->identifier);
      $Qcheck->bindInt(':limit_minutes', $this->minutes);
      $Qcheck->execute();

      if ($Qcheck->fetch() !== false) {
        return false;
      }

      return true;
    }

    public function expireEntries()
    {
      $Qdel = Registry::get('Db')->prepare('delete
                                            from :table_action_recorder
                                            where module = :module
                                            and date_added < date_sub(now(),
                                            interval :limit_minutes minute)
                                          ');
      $Qdel->bindValue(':module', $this->code);
      $Qdel->bindInt(':limit_minutes', $this->minutes);
      $Qdel->execute();

      return $Qdel->rowCount();
    }

    public function check()
    {
      return defined('MODULE_ACTION_RECORDER_CREATE_ACCOUNT_PRO_EMAIL_MINUTES');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'What do you want for the minimum minutes per e-mail for one person for create professional account?',
          'configuration_key' => 'MODULE_ACTION_RECORDER_CREATE_ACCOUNT_PRO_EMAIL_MINUTES',
          'configuration_value' => '90',
          'configuration_description' => 'Minimum number of minutes to allow 1 e-mail to be sent (eg, 15 for 1 e-mail every 15 minutes)',
          'configuration_group_id' => '6',
          'sort_order' => '0',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove()
    {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys()
    {
      return array('MODULE_ACTION_RECORDER_CREATE_ACCOUNT_PRO_EMAIL_MINUTES');
    }
  }
