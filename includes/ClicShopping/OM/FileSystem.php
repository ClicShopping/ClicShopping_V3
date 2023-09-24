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

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileSystem
{
  /**
   * @param string $base
   * @param bool $recursive
   * @return array
   */
  public static function getDirectoryContents(string $base, bool $recursive = true): array
  {
    $base = str_replace('\\', '/', $base); // Unix style directory separator "/"

    $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;

    if ($recursive === true) {
      $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, $flags));
    } else {
      $dir = new FilesystemIterator($base, $flags);
    }

    $result = [];

    foreach ($dir as $file) {
      $result[] = $file->getPathName();
    }

    return $result;
  }

  /**
   * @param $location
   * @param bool $recursive_check
   * @return bool
   */
  public static function isWritable(string $location, bool $recursive_check = false): bool
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

  /**
   * @param $dir
   * @param bool $dry_run
   * @return array
   */
  public static function rmdir(string $dir, bool $dry_run = false): array
  {
    $result = [];

    if (is_dir($dir)) {
      foreach (static::getDirectoryContents($dir, false) as $file) {
        if (is_dir($file)) {
          $result = array_merge($result, static::rmdir($file, $dry_run));
        } else {
          $result[] = [
            'type' => 'file',
            'source' => $file,
            'result' => ($dry_run === false) ? unlink($file) : static::isWritable($file)
          ];
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

  /**
   * @param string $directory
   * @return bool
   */
  public static function isDirectoryEmpty(string $directory): bool
  {
    $dir = new FilesystemIterator($directory, FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS);

    return ($dir->valid() === false);
  }

  /**
   * @param $pathname
   * @return mixed
   */
  public static function displayPath(string $pathname): string
  {
    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $pathname);
  }

  /**
   * Recursively remove a directory or a single file
   * @param string $source The source to remove
   */
  public static function rmFile(string $source)
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
   * Parse file permissions to a human readable layout
   * @param $mode
   * @return string
   */
  public function getFilePermissions(string $mode)
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
    $owner = [];
    $group = [];
    $world = [];

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
