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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;

  abstract class ConfigParamAbstract
  {
    protected $code;
    protected $key_prefix;
    protected $key;
    public $title;
    public $description;
    public $default;
    public $sort_order = 0;

    abstract protected function init();

    public function __construct()
    {
      $this->code = (new \ReflectionClass($this))->getShortName();

      $this->key = $this->key_prefix . $this->code;

      $this->init();
    }

    protected function getInputValue()
    {
      $key = strtoupper($this->key);
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
