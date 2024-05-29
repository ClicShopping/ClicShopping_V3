<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\CH\Params;

use ClicShopping\OM\HTML;

class api_key extends \ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = '';
  public ?int $sort_order = 30;
  public bool $app_configured = true;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_chatgpt_api_key_title');
    $this->description = $this->app->getDef('cfg_chatgpt_api_key_description');
  }

  public function getInputField()
  {
    $value = $this->getInputValue();

    $input = HTML::passwordField($this->key,  $value, 'id="' . $this->key . '" autocomplete="off"');

    return $input;
  }
}
