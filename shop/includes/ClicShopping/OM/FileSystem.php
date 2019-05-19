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


  namespace ClicShopping\OM;

  class FileSystem
  {
    public static function getDirectoryContents($base)
    {
      $base = str_replace('\\', '/', $base); // Unix style directory separator "/"

      $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS));

      $result = [];

      foreach ($dir as $file) {
        $result[] = $file->getPathName();
      }

      return $result;
    }

    public static function isWritable($location, $recursive_check = false)
    {
      if ($recursive_check === true) {
        if (!file_exists($location)) {
          while (true) {
            $location = dirname($location);

            if (file_exists($location)) {
              break;
            }
          }
        }
      }

      return is_writable($location);
    }

    public static function rmdir($dir, $dry_run = false)
    {
      $result = [];

      if (is_dir($dir)) {
        foreach (scandir($dir) as $file) {
          if (!in_array($file, ['.', '..'])) {
            if (is_dir($dir . '/' . $file)) {
              $result = array_merge($result, static::rmdir($dir . '/' . $file, $dry_run));
            } else {
              $result[] = [
                'type' => 'file',
                'source' => $dir . '/' . $file,
                'result' => ($dry_run === false) ? unlink($dir . '/' . $file) : static::isWritable($dir . '/' . $file)
              ];
            }
          }
        }

        $result[] = [
          'type' => 'directory',
          'source' => $dir,
          'result' => ($dry_run === false) ? rmdir($dir) : static::isWritable($dir)
        ];
      }

      return $result;
    }

    public static function displayPath($pathname)
    {
      return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $pathname);
    }


    /**
     * Recursively remove a directory or a single file
     *
     * @param string $source The source to remove
     * @access public
     */
    public static function rmFile($source)
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (is_dir($source)) {
        $dir = dir($source);
        while ($file = $dir->read()) {
          if (($file != '.') && ($file != '..')) {
            if (FileSystem::isWritable($source . '/' . $file)) {
              static::rmFile($source . '/' . $file);
            } else {
              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_file_not_removeable') . ' ' . $source . '/' . $file, 'error');
            }
          }
        }
        $dir->close();

        if (static::isWritable($source)) {
          rmdir($source);
        } else {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_file_not_removeable') . ' ' . $source, 'error');
        }
      } else {
        if (static::isWritable($source)) {
          unlink($source);
        } else {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_file_not_removeable') . ' ' . $source, 'error');
        }
      }
    }

    /**
     * @access public
     * Parse file permissions to a human readable layout
     *
     * @param int $mode The file permission to parse
     */
    public function getFilePermissions($mode)
    {
// determine type
      if (($mode & 0xC000) == 0xC000) { // unix domain socket
        $type = 's';
      } elseif (($mode & 0x4000) == 0x4000) { // directory
        $type = 'd';
      } elseif (($mode & 0xA000) == 0xA000) { // symbolic link
        $type = 'l';
      } elseif (($mode & 0x8000) == 0x8000) { // regular file
        $type = '-';
      } elseif (($mode & 0x6000) == 0x6000) { //bBlock special file
        $type = 'b';
      } elseif (($mode & 0x2000) == 0x2000) { // character special file
        $type = 'c';
      } elseif (($mode & 0x1000) == 0x1000) { // named pipe
        $type = 'p';
      } else { // unknown
        $type = '?';
      }

// determine permissions
      $owner['read'] = ($mode & 00400) ? 'r' : '-';
      $owner['write'] = ($mode & 00200) ? 'w' : '-';
      $owner['execute'] = ($mode & 00100) ? 'x' : '-';
      $group['read'] = ($mode & 00040) ? 'r' : '-';
      $group['write'] = ($mode & 00020) ? 'w' : '-';
      $group['execute'] = ($mode & 00010) ? 'x' : '-';
      $world['read'] = ($mode & 00004) ? 'r' : '-';
      $world['write'] = ($mode & 00002) ? 'w' : '-';
      $world['execute'] = ($mode & 00001) ? 'x' : '-';

// adjust for SUID, SGID and sticky bit
      if ($mode & 0x800) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
      if ($mode & 0x400) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
      if ($mode & 0x200) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

      return $type .
        $owner['read'] . $owner['write'] . $owner['execute'] .
        $group['read'] . $group['write'] . $group['execute'] .
        $world['read'] . $world['write'] . $world['execute'];
    }

    /**
     * Copy file
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public static function copyFile(string $source, string $destination): bool
    {
      $target_dir = dirname($destination);

      if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      return copy($source, $destination);
    }

    /**
     * Move File
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public static function moveFile(string $source, string $destination): bool
    {
      $target_dir = dirname($destination);

      if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      if (copy($source, $destination)) {
        unlink($source);
        return true;
      }

      return false;
    }
  }
