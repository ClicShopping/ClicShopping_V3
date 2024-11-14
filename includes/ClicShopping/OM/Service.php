<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use function call_user_func;
use function in_array;

class Service
{
  protected array $_services = [];
  protected array $_started_services = [];
  protected array $_call_before_page_content = [];
  protected array $_call_after_page_content = [];
  protected string $directory;
  protected string $directoryAdmin;

  public function __construct()
  {
    $this->directory = CLICSHOPPING::BASE_DIR . 'Service/Shop/';
    $this->directoryAdmin = CLICSHOPPING::BASE_DIR . 'Service/ClicShoppingAdmin/';
  }

  /**
   *
   */
  public function start()
  {
    $this->_started_services = [];

    $exclude = ['.', '..', '_htaccess', '.htaccess'];

    if (CLICSHOPPING::getSite() == 'ClicShoppingAdmin') {
      $files = array_diff(scandir($this->directoryAdmin), $exclude);
    } else {
      $files = array_diff(scandir($this->directory), $exclude);
    }

    foreach ($files as $sm) {
      $result['file'][] = ['files_name' => $sm];
    }

    foreach ($result['file'] as &$module) {
      $class = substr($module['files_name'], 0, strrpos($module['files_name'], '.'));

      $this->startService($class);
    }
  }

  /**
   *
   */
  public function stop()
  {
    /*
      ugly workaround to force the output_compression/GZIP service module to be
      stopped last to make sure all content in the buffer is compressed and sent
      to the client
    */
    if (CLICSHOPPING::getSite() === 'Shop') {
      if ($this->isStarted('output_compression')) {
        $key = array_search('output_compression', $this->_started_services, true);
        unset($this->_started_services[$key]);

        $this->_started_services[] = 'output_compression';
      }
    }

    foreach ($this->_started_services as $service) {
      $this->stopService($service);
    }
  }

  /**
   * @param string $service
   */
  public function startService(string $service)
  {
    if (CLICSHOPPING::getSite() === 'Shop') {
      if (class_exists('ClicShopping\\Service\\Shop\\' . $service)) {
        if (call_user_func(array('ClicShopping\\Service\\Shop\\' . $service, 'start'))) {
          $this->_started_services[] = $service;
        }
      } else {
        throw new InvalidArgumentException('\'ClicShopping\\Service\\Shop\\' . $service . '\' does not exist');
      }
    } else {
      if (class_exists('ClicShopping\\Service\\ClicShoppingAdmin\\' . $service)) {
        if (call_user_func(array('ClicShopping\\Service\\ClicShoppingAdmin\\' . $service, 'start'))) {
          $this->_started_services[] = $service;
        }
      } else {
        throw new InvalidArgumentException('\'ClicShopping\\Service\\ClicShoppingAdmin\\' . $service . '\' does not exist');
      }
    }
  }

  /**
   * @param string $service
   */
  public function stopService(string $service)
  {
    if (CLICSHOPPING::getSite() === 'Shop') {
      if ($this->isStarted($service)) {
        call_user_func(array('ClicShopping\\Service\\Shop\\' . $service, 'stop'));
      }
    } else {
      if ($this->isStarted($service)) {
        call_user_func(array('ClicShopping\\Service\\ClicShoppingAdmin\\' . $service, 'stop'));
      }
    }
  }

  /**
   * @param $service
   * @return bool
   */
  public function isStarted(string $service): bool
  {
    return in_array($service, $this->_started_services, true);
  }

  /**
   * @param $object
   * @param $method
   */
  public function addCallBeforePageContent($object, $method)
  {
    $this->_call_before_page_content[] = [$object, $method];
  }

  /**
   * @param $object
   * @param $method
   */
  public function addCallAfterPageContent($object, $method): void
  {
    $this->_call_after_page_content[] = [$object, $method];
  }

  /**
   * @return bool
   */
  public function hasBeforePageContentCalls(): bool
  {
    return !empty($this->_call_before_page_content);
  }

  /**
   * @return bool
   */
  public function hasAfterPageContentCalls(): bool
  {
    return !empty($this->_call_after_page_content);
  }

  /**
   * @return array
   */
  public function getCallBeforePageContent(): array
  {
    return $this->_call_before_page_content;
  }

  /**
   * @return array
   */
  public function getCallAfterPageContent(): array
  {
    return $this->_call_after_page_content;
  }
}