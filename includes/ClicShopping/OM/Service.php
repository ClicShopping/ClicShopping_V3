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

/**
 * Handles the management of service modules for the application.
 * It provides mechanisms to start, stop, and manage services,
 * as well as functionality to handle calls before and after page content.
 */
class Service
{
  protected array $_services = [];
  protected array $_started_services = [];
  protected array $_call_before_page_content = [];
  protected array $_call_after_page_content = [];
  protected string $directory;
  protected string $directoryAdmin;

  /**
   * Constructor method for initializing directory paths.
   *
   * @return void
   */
  public function __construct()
  {
    $this->directory = CLICSHOPPING::BASE_DIR . 'Service/Shop/';
    $this->directoryAdmin = CLICSHOPPING::BASE_DIR . 'Service/ClicShoppingAdmin/';
  }

  /**
   * Initializes and starts services by scanning and processing files from a directory.
   *
   * @return void
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
   * Stops all currently started services, ensuring that the output_compression
   * service module is stopped last to ensure all buffered content is
   * compressed and sent to the client.
   *
   * @return void
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
   * Starts a service by its name and registers it as a started service.
   *
   * @param string $service The name of the service class to start.
   * @return void
   * @throws InvalidArgumentException If the service class does not exist.
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
   * Stops the specified service if it is currently started.
   *
   * @param string $service The name of the service to stop.
   * @return void
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
   * Checks if a specified service has been started.
   *
   * @param string $service The name of the service to check.
   * @return bool Returns true if the service has been started, otherwise false.
   */
  public function isStarted(string $service): bool
  {
    return in_array($service, $this->_started_services, true);
  }

  /**
   * Adds a method call to the queue of operations to be executed before the page content is rendered.
   *
   * @param object $object The object instance containing the method to call.
   * @param string $method The name of the method to be called on the provided object.
   * @return void
   */
  public function addCallBeforePageContent($object, $method)
  {
    $this->_call_before_page_content[] = [$object, $method];
  }

  /**
   * Adds a callable to be executed after the page content.
   *
   * @param object $object The object containing the method to call.
   * @param string $method The method name to be executed after the page content.
   * @return void
   */
  public function addCallAfterPageContent($object, $method): void
  {
    $this->_call_after_page_content[] = [$object, $method];
  }

  /**
   * Checks if there are any calls set to execute before rendering the page content.
   *
   * @return bool Returns true if there are calls to execute before the page content, otherwise false.
   */
  public function hasBeforePageContentCalls(): bool
  {
    return !empty($this->_call_before_page_content);
  }

  /**
   * Determines if there are any registered calls scheduled after the page content.
   *
   * @return bool Returns true if there are calls after the page content, otherwise false.
   */
  public function hasAfterPageContentCalls(): bool
  {
    return !empty($this->_call_after_page_content);
  }

  /**
   * Retrieves the list of methods or actions to be called before rendering page content.
   *
   * @return array An array of methods or actions to execute before page content is processed.
   */
  public function getCallBeforePageContent(): array
  {
    return $this->_call_before_page_content;
  }

  /**
   * Retrieves the list of callbacks to be executed after the page content.
   *
   * @return array Returns an array of callbacks registered for execution after the page content.
   */
  public function getCallAfterPageContent(): array
  {
    return $this->_call_after_page_content;
  }
}