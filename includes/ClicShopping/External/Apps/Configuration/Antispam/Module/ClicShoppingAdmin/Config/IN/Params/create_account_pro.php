<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\IN\Params;

use ClicShopping\OM\HTML;

class create_account_pro extends \ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = 'False';
  public ?int $sort_order = 60;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_antispam_create_account_pro_title');
    $this->description = $this->app->getDef('cfg_antispam_create_account_pro_description');
  }

  public function getInputField()
  {
    $value = $this->getInputValue();

    $input = HTML::radioField($this->key, 'True', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_antispam_create_account_pro_true') . ' ';
    $input .= HTML::radioField($this->key, 'False', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_antispam_create_account_pro_false');

    return $input;
  }
}