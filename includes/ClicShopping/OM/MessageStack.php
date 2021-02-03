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

  /**
   * The MessageStack class manages information messages to be displayed.
   * Messages that are shown are automatically removed from the stack.
   */

  namespace ClicShopping\OM;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class MessageStack
  {
    protected array $data = [];

    public function __construct()
    {
      register_shutdown_function(function () {
        if (!empty($this->data)) {
          $_SESSION['MessageStack_Data'] = $this->data;
        }
      });

      if (isset($_SESSION['MessageStack_Data']) && is_array($_SESSION['MessageStack_Data'])) {
        foreach ($_SESSION['MessageStack_Data'] as $group => $messages) {
          foreach ($messages as $message) {
            $this->add($message['text'], $message['type'], $group);
          }
        }

        unset($_SESSION['MessageStack_Data']);
      }
      Registry::get('Hooks')->watch('Account', 'LogoutAfter', 'execute', function() {
        $this->reset('main');
      });
    }

    /**
     * Add a message to the stack
     *
     * @param string $group The group the message belongs to
     * @param string $message The message information text
     * @param string $type The type of message: info, error, warning, success
     *
     */
    public function add(string $message, string $type = 'error', string $group = 'main')
    {
      switch ($type) {
        case 'error':
          $type = 'danger';
          break;
      }

      $stack = [
        'text' => $message,
        'type' => $type
      ];

      if (!$this->exists($group) || !in_array($stack, $this->data[$group])) {
        $this->data[$group][] = $stack;
      }
    }
  
    /**
     * @param string|null $group
     */
    public function reset(?string $group = null)
    {
      if (isset($group)) {
        if ($this->exists($group)) {
          unset($this->data[$group]);
        }
      } else {
        $this->data = [];
      }
    }

    /**
     * Checks to see if a group in the stack contains messages
     * @param string|null $group
     * @return bool
     */
    public function exists(?string $group = null)
    {
      if (isset($group)) {
         return array_key_exists($group, $this->data);
      }

      return !empty($this->data);
    }

    /**
     * Checks to see if the message stack contains messages
     * @return bool
     */
    public function hasContent()
    {
      return !empty($this->data);
    }

    /**
     * Get the messages belonging to a group. The messages are placed into an
     * unsorted list wrapped in a DIV element with the "messageStack" style sheet
     * class.
     *
     * @param string $group The name of the group to get the messages from
     */
    public function get(string $group): string
    {
      $result = '';

      if ($this->exists($group)) {
        $data = [];

        if (is_array($this->data[$group])) {
          foreach ($this->data[$group] as $message) {
            $data['alert-' . $message['type']][] = $message['text'];
          }

          foreach ($data as $type => $messages) {
            $result .= '<div class="alert ' . HTML::outputProtected($type) . '" role="alert">';

            foreach ($messages as $message) {
              $result .= '<p>' . $message . '</p>';
            }

            $result .= '</div>';
          }

          unset($this->data[$group]);
        }
      }
      
      return $result;
    }

    /**
     * Get the message stack array data set
     * @param string|null $group
     * @return array|mixed
     */
    public function getAll(?string $group = null)
    {
      if (isset($group)) {
        if ($this->exists($group)) {
          return $this->data[$group];
        }

        return [];
      }

      return $this->data;
    }

    /**
     * Get the number of messages belonging to a group
     * @param string|null $group
     * @return int
     */
    public function size(?string $group = null): int
    {
      if (isset($group)) {
        if ($this->exists($group)) {
          return \count($this->data[$group]);
        }

        return 0;
      }

      return \count($this->data);
    }
  }
