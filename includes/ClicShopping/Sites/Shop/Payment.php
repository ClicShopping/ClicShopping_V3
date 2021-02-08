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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Payment
  {
    public array $modules = [];
    public string $selected_module;
    protected $template;
    protected $lang;

// class constructor
    public function __construct($module = null)
    {
      $this->template = Registry::get('Template');
      $this->lang = Registry::get('Language');

      if (\defined('MODULE_PAYMENT_INSTALLED') && !\is_null(MODULE_PAYMENT_INSTALLED)) {
        $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);

        $include_modules = [];

        if ((!\is_null($module)) && (\in_array($module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)), $this->modules) || \in_array($module, $this->modules))) {

          $this->selected_module = $module;

          if (str_contains($module, '\\')) {
            $class = Apps::getModuleClass($module, 'Payment');
            $include_modules[] = ['class' => $module,
              'file' => $class
            ];
          }
        } else {
          foreach ($this->modules as $value) {
            if (str_contains($value, '\\')) {
              $class = Apps::getModuleClass($value, 'Payment');

              $include_modules[] = ['class' => $value,
                'file' => $class
              ];
            }
          }
        }

        for ($i = 0, $n = \count($include_modules); $i < $n; $i++) {
          if (str_contains($include_modules[$i]['class'], '\\')) {
            Registry::set('Payment_' . str_replace('\\', '_', $include_modules[$i]['class']), new $include_modules[$i]['file']);
          }
        }

// if there is only one payment method, select it as default because in
// checkout_confirmation.php the $_SESSION['payment'] variable is being assigned the
// $_POST['payment'] value which will be empty (no radio button selection possible)

        if (($this->getCountPaymentModules() == 1) && (!isset($_SESSION['payment']) || ($_SESSION['payment'] != $include_modules[0]['class']))) {
          $_SESSION['payment'] = $include_modules[0]['class'];
        }

        if ((!\is_null($module)) && (\in_array($module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)), $this->modules) || \in_array($module, $this->modules))) {
          if (str_contains($module, '\\')) {
            $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $module));

            if (isset($CLICSHOPPING_PM->form_action_url)) {
              $this->form_action_url = $CLICSHOPPING_PM->form_action_url;
            }
          }
        }
      }
    }

// class methods
    /* The following method is needed in the checkout_confirmation.php page
       due to a chicken and egg problem with the payment class and order class.
       The payment modules needs the order destination data for the dynamic status
       feature, and the order class needs the payment module title.
       The following method is a work-around to implementing the method in all
       payment modules available which would break the modules in the contributions
       section. This should be looked into again post 2.2.
    */
    public function update_status()
    {

      if (\is_array($this->modules)) {
        if (str_contains($this->selected_module, '\\')) {
          $code = 'Payment_' . str_replace('\\', '_', $this->selected_module);

          if (Registry::exists($code)) {
            $CLICSHOPPING_PM = Registry::get($code);

            if (method_exists($CLICSHOPPING_PM, 'update_status')) {
              $CLICSHOPPING_PM->update_status();
            }
          }
        }
      }
    }

    public function checkout_initialization_method(): array
    {
      $initialize_array = [];

      if (\is_array($this->modules)) {
        foreach ($this->modules as $value) {
          if (str_contains($value, '\\')) {
            $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $value));

            if ($CLICSHOPPING_PM->enabled && method_exists($CLICSHOPPING_PM, 'checkout_initialization_method')) {
              $initialize_array[] = $CLICSHOPPING_PM->checkout_initialization_method();
            }
          }
        }
      }

      return $initialize_array;
    }

    public function selection(): array
    {
      $selection_array = [];

      if (\is_array($this->modules)) {
        foreach ($this->modules as $value) {
          if (str_contains($value, '\\')) {
            $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $value));

            if ($CLICSHOPPING_PM->enabled) {
              $selection = $CLICSHOPPING_PM->selection();
              if (\is_array($selection)) $selection_array[] = $selection;
            }
          }
        }
      }

      return $selection_array;
    }

    /**
     * preconfirmation
     */
    public function pre_confirmation_check()
    {
      if (\is_array($this->modules)) {
        if (str_contains($this->selected_module, '\\')) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            $CLICSHOPPING_PM->pre_confirmation_check();
          }
        }
      }
    }

    /**
     * confirmation
     */
    public function confirmation()
    {
      if (\is_array($this->modules)) {
        if (str_contains($this->selected_module, '\\')) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->confirmation();
          }
        }
      }
    }

    /**
     * process
     */
    public function process_button()
    {
      if (\is_array($this->modules)) {
        if (str_contains($this->selected_module, '\\')) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->process_button();
          }
        }
      }
    }

    /**
     * before process
     */
    public function before_process()
    {
      if (\is_array($this->modules)) {
        if (str_contains($this->selected_module, '\\')) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->before_process();
          }
        }
      }
    }

    /**
     * after process
     */
    public function after_process()
    {
      if (\is_array($this->modules)) {
        if (str_contains($this->selected_module, '\\')) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->after_process();
          }
        }
      }
    }

    /**
     * error
     */
    public function get_error()
    {
      if (\is_array($this->modules)) {
        if (str_contains($this->selected_module, '\\')) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->get_error();
          }
        }
      }
    }

    /**
     * count payment
     * @return int
     */
    public function getCountPaymentModules(): int
    {
      $count = 0;

      $modules_array = explode(';', MODULE_PAYMENT_INSTALLED);

      for ($i = 0, $n = \count($modules_array); $i < $n; $i++) {
        $m = $modules_array[$i];

        $CLICSHOPPING_PM = null;

        if (str_contains($m, '\\')) {
          list($vendor, $app, $module) = explode('\\', $m);

          $module = $vendor . '\\' . $app . '\\' . $module;

          $code = 'Payment_' . str_replace('\\', '_', $module);

          if (Registry::exists($code)) {
            $CLICSHOPPING_PM = Registry::get($code);
          }
        }

        if (isset($CLICSHOPPING_PM) && $CLICSHOPPING_PM->enabled) {
          $count++;
        }
      }

      return $count;
    }
  }
