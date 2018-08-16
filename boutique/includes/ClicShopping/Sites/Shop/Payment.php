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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Payment {
    public $modules;
    public $selected_module;
    protected $template;
    protected $lang;

// class constructor
    public function __construct($module = null) {
      $this->template = Registry::get('Template');
      $this->lang = Registry::get('Language');

      if (defined('MODULE_PAYMENT_INSTALLED') && !is_null(MODULE_PAYMENT_INSTALLED)) {
        $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);

        $include_modules = [];

        if ( (!is_null($module)) && (in_array($module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.')+1)), $this->modules) || in_array($module, $this->modules)) ) {

          $this->selected_module = $module;

          if (strpos($module, '\\') !== false) {
            $class = Apps::getModuleClass($module, 'Payment');
            $include_modules[] = ['class' => $module,
                                  'file' => $class
                                 ];
          } else {
            $include_modules[] = ['class' => $module,
                                  'file' => $module . '.php'
                                 ];
          }
        } else {
          foreach($this->modules as $value) {
            if (strpos($value, '\\') !== false) {
              $class = Apps::getModuleClass($value, 'Payment');

              $include_modules[] = ['class' => $value,
                                    'file' => $class
                                   ];
            } else {
              $class = basename($value, '.php');
              $include_modules[] = ['class' => $class,
                                    'file' => $value
                                   ];
            }
          }
        }

        for ($i=0, $n=count($include_modules); $i<$n; $i++) {

          if (strpos($include_modules[$i]['class'], '\\') !== false) {
            Registry::set('Payment_' . str_replace('\\', '_', $include_modules[$i]['class']), new $include_modules[$i]['file']);
          } else {

            $this->lang->loadDefinitions('modules/payment/' . pathinfo($include_modules[$i]['file'], PATHINFO_FILENAME));

            if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/payment/' . $include_modules[$i]['file'])) {
              include($this->template->getModuleDirectory() . '/payment/' . $include_modules[$i]['file']);
              $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
            }
          }
        }

// if there is only one payment method, select it as default because in
// checkout_confirmation.php the $_SESSION['payment'] variable is being assigned the
// $_POST['payment'] value which will be empty (no radio button selection possible)

        if ( ($this->getCountPaymentModules() == 1) && (!isset($_SESSION['payment']) || ($_SESSION['payment'] != $include_modules[0]['class'])) ) {
          $_SESSION['payment'] = $include_modules[0]['class'];
        }

        if ( (!is_null($module)) && (in_array($module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.')+1)), $this->modules) || in_array($module, $this->modules)) ) {
          if (strpos($module, '\\') !== false) {
            $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $module));

            if (isset($CLICSHOPPING_PM->form_action_url)) {
              $this->form_action_url = $CLICSHOPPING_PM->form_action_url;
            }
          } elseif (isset($GLOBALS[$module]->form_action_url)) {
            $this->form_action_url = $GLOBALS[$module]->form_action_url;
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
  public function update_status() {

    if (is_array($this->modules)) {
      if (strpos($this->selected_module, '\\') !== false) {
        $code = 'Payment_' . str_replace('\\', '_', $this->selected_module);

       if (Registry::exists($code)) {
          $CLICSHOPPING_PM = Registry::get($code);

          if (method_exists($CLICSHOPPING_PM, 'update_status')) {
            $CLICSHOPPING_PM->update_status();
          }
        }
      } else {
        if (is_object($GLOBALS[$this->selected_module])) {
          if (method_exists($GLOBALS[$this->selected_module], 'update_status')) {
            $GLOBALS[$this->selected_module]->update_status();
          }
        }
      }
    }
  }

    public function javascript_validation() {
      $js = '';
      if (is_array($this->modules)) {
        $js = '<script><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = ' . json_encode(CLICSHOPPING::getDef('js_error') . "\n\n") . ';' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.checkout_payment.payment.length) {' . "\n" .
              '    for (var i=0; i<document.checkout_payment.payment.length; i++) {' . "\n" .
              '      if (document.checkout_payment.payment[i].checked) {' . "\n" .
              '        payment_value = document.checkout_payment.payment[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.checkout_payment.payment.checked) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  } else if (document.checkout_payment.payment.value) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  }' . "\n\n";

        foreach($this->modules as $value) {
          if (strpos($value, '\\') !== false) {
            $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $value));

            if ($CLICSHOPPING_PM->enabled) {
              $js .= $CLICSHOPPING_PM->javascript_validation();
            }
          } else {
            $class = basename($value, '.php');
            if ($GLOBALS[$class]->enabled) {
              $js .= $GLOBALS[$class]->javascript_validation();
            }
          }
        }

        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
               '    error_message = error_message + ' . json_encode(CLICSHOPPING::getDef('js_error_no_payment_module_selected') . "\n") . ';' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1) {' . "\n" .
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
        }

        return $js;
      }

    public function checkout_initialization_method() {
      $initialize_array = [];

      if (is_array($this->modules)) {
        foreach($this->modules as $value) {
          if (strpos($value, '\\') !== false) {
            $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $value));

            if ($CLICSHOPPING_PM->enabled && method_exists($CLICSHOPPING_PM, 'checkout_initialization_method')) {
              $initialize_array[] = $CLICSHOPPING_PM->checkout_initialization_method();
            }
          } else {
            $class = basename($value, '.php');
            if ($GLOBALS[$class]->enabled && method_exists($GLOBALS[$class], 'checkout_initialization_method')) {
              $initialize_array[] = $GLOBALS[$class]->checkout_initialization_method();
            }
          }
        }
      }

      return $initialize_array;
    }

    public function selection() {
      $selection_array = [];

      if (is_array($this->modules)) {
        foreach($this->modules as $value) {
          if (strpos($value, '\\') !== false) {
            $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $value));

            if ($CLICSHOPPING_PM->enabled) {
              $selection = $CLICSHOPPING_PM->selection();
              if (is_array($selection)) $selection_array[] = $selection;
            }
          } else {
            $class = basename($value, '.php');
            if ($GLOBALS[$class]->enabled) {
              $selection = $GLOBALS[$class]->selection();
              if (is_array($selection)) $selection_array[] = $selection;
            }
          }
        }
      }

      return $selection_array;
    }

    public function pre_confirmation_check() {
      if (is_array($this->modules)) {
        if (strpos($this->selected_module, '\\') !== false) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            $CLICSHOPPING_PM->pre_confirmation_check();
          }
        } else {
          if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
            $GLOBALS[$this->selected_module]->pre_confirmation_check();
          }
        }
      }
    }

    public function confirmation() {
      if (is_array($this->modules)) {
        if (strpos($this->selected_module, '\\') !== false) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->confirmation();
          }
        } else {
          if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
            return $GLOBALS[$this->selected_module]->confirmation();
          }
        }
      }
    }

    public function process_button() {
      if (is_array($this->modules)) {
        if (strpos($this->selected_module, '\\') !== false) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->process_button();
          }
        } else {
          if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
            return $GLOBALS[$this->selected_module]->process_button();
          }
        }
      }
    }

    public function before_process() {
      if (is_array($this->modules)) {
        if (strpos($this->selected_module, '\\') !== false) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->before_process();
          }
        } else {
          if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
            return $GLOBALS[$this->selected_module]->before_process();
          }
        }
      }
    }

    public function after_process() {
      if (is_array($this->modules)) {
        if (strpos($this->selected_module, '\\') !== false) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->after_process();
          }
        } else {
          if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
            return $GLOBALS[$this->selected_module]->after_process();
          }
        }
      }
    }

    public function get_error() {
      if (is_array($this->modules)) {
        if (strpos($this->selected_module, '\\') !== false) {
          $CLICSHOPPING_PM = Registry::get('Payment_' . str_replace('\\', '_', $this->selected_module));

          if ($CLICSHOPPING_PM->enabled) {
            return $CLICSHOPPING_PM->get_error();
          }
        } else {
          if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
            return $GLOBALS[$this->selected_module]->get_error();
          }
        }
      }
    }

    public function getCountPaymentModules() {
      $count = 0;

      $modules_array = explode(';', MODULE_PAYMENT_INSTALLED);

      for ($i=0, $n=count($modules_array); $i<$n; $i++) {
        $m = $modules_array[$i];

        $CLICSHOPPING_PM = null;

        if (strpos($m, '\\') !== false) {
          list($vendor, $app, $module) = explode('\\', $m);

          $module = $vendor . '\\' . $app . '\\' . $module;

          $code = 'Payment_' . str_replace('\\', '_', $module);

          if (Registry::exists($code)) {
            $CLICSHOPPING_PM = Registry::get($code);
          }
        } else {
          $module = substr($m, 0, strrpos($m, '.'));

          if (isset($GLOBALS[$module]) && is_object($GLOBALS[$module])) {
            $CLICSHOPPING_PM = $GLOBALS[$module];
          }
        }

        if (isset($CLICSHOPPING_PM) && $CLICSHOPPING_PM->enabled) {
          $count++;
        }
      }

      return $count;
    }
  }
