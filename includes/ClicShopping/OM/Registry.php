<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM;

  class Registry
  {
    protected static array $aliases = [];
    protected static array $data = [];

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function get(string $key): mixed
    {
      if (static::exists($key)) {
        $value = static::$data[$key];
      } else {
        $registry_class = null;

        if (array_key_exists($key, static::$aliases)) {
          $registry_class = 'ClicShopping\\OM\\' . static::$aliases[$key];
        } else {
          $bt = debug_backtrace(0, 2);
          $class = $bt[1]['class'];
          $ns_array = explode('\\', $class);

          if (\count($ns_array) > 5) {
            if (implode('\\', \array_slice($ns_array, 0, 4)) === 'ClicShopping\\Custom\\Sites') {
              $registry_class = implode('\\', \array_slice($ns_array, 0, 5)) . '\\Registry\\' . $key;
            } elseif (implode('\\', \array_slice($ns_array, 0, 4)) === 'ClicShopping\\Sites') {
              $registry_class = implode('\\', \array_slice($ns_array, 0, 5)) . '\\Registry\\' . $key;
            }
          }
        }

        if (!isset($registry_class)) {
          $site = CLICSHOPPING::getSite();

          if (isset($site)) {
            if (class_exists('ClicShopping\\Custom\\Sites\\' . $site . '\\' . $key)) {
              $registry_class = 'ClicShopping\\Custom\\Sites\\' . $site . '\\' . $key;
            } else {
              $registry_class = 'ClicShopping\\Sites\\' . $site . '\\' . $key;
            }
          }
        }

        if (isset($registry_class)) {
          while (!isset($value)) {
            if (is_a($registry_class, '\\ClicShopping\\OM\\RegistryAbstract', true)) {
              $RegistryObject = new $registry_class();

              if ($RegistryObject->hasAlias()) {
                $registry_class = 'ClicShopping\\OM\\' . $RegistryObject->getAlias();
                continue;
              } else {
                $value = static::$data[$key] = $RegistryObject->getValue();
              }
            } else {
              break;
            }
          }
        }
      }

      if (!isset($value)) {
        trigger_error('ClicShopping\Registry::get(): "' . $key . '" is not registered');
      }

      return $value ?? null;
    }

    /**
     * @param string $key
     * @param $value
     * @param bool $force
     * @return bool
     */
    public static function set(string $key, $value, bool $force = false): bool
    {
      if (static::exists($key) && ($force !== true)) {
        trigger_error('ClicShopping\Registry::set(): "' . $key . '" is already registered and is not forced to be replaced');
        return false;
      }
      static::$data[$key] = $value;
      return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
      return array_key_exists($key, static::$data);
    }

    /**
     * @param string $key
     */
    public static function remove(string $key): void
    {
      unset(static::$data[$key]);
    }

    /**
     * @param string $key
     * @param string $class
     * @return bool
     */
    public static function addAlias(string $key, string $class): bool
    {
      if (static::aliasExists($key)) {
        trigger_error('ClicShopping\Registry::addAlias(): "' . $key . '" is already registered to "' . static::$aliases[$key] . '" and cannot be replaced by "' . $class . '"');
        return false;
      }
      static::$aliases[$key] = $class;
      return true;
    }

    /**
     * @param array $keys
     */
    public static function addAliases(array $keys): void
    {
      foreach ($keys as $key => $class) {
        static::addAlias($key, $class);
      }
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function aliasExists(string $key): bool
    {
      return array_key_exists($key, static::$aliases);
    }

    /**
     * @param string $key
     */
    public static function removeAlias(string $key): void
    {
      unset(static::$aliases[$key]);
    }
  }
