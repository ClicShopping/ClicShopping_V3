<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\ClicShoppingAdmin;

use ClicShopping\OM\HTML;
use ReflectionClass;
use function constant;
use function defined;

abstract class ConfigParamAbstract
{
  protected string $code;
  protected string $key_prefix;
  protected string $key;
  public $title;
  public $description;
  public $default;
  public ?int $sort_order = 0;

  abstract protected function init();

  public function __construct()
  {
    $this->code = (new ReflectionClass($this))->getShortName();

    $this->key = $this->key_prefix . $this->code;

    $this->init();
  }

  protected function getInputValue()
  {
    $key = mb_strtoupper($this->key);
    $value = defined($key) ? constant($key) : null;

    if (!isset($value) && isset($this->default)) {
      $value = $this->default;
    }

    return $value;
  }

  public function getInputField()
  {
    $input = HTML::inputField($this->key, $this->getInputValue());

    return $input;
  }

  public function getSetField()
  {
    $input = $this->getInputField();

    $result = <<<EOT
  <div class="row">
    <div class="col-md-7">
      <strong>{$this->title} : </strong>
      <p>{$this->description}</p>
    </div>
    <div class="col-md-5">
      {$input}
    </div>
  </div>
EOT;

    return $result;
  }
}
