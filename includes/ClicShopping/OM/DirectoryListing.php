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

use function count;
use function in_array;
use function is_array;
use function is_string;
use function strlen;

/**
 * Class DirectoryListing
 * Provides functionality to list directories and files with configurable options such as file inclusion, directory inclusion,
 * exclusion of specific entries, recursive traversal, and optional file statistics.
 */
class DirectoryListing
{
  protected string $_directory = '';
  protected bool $_include_files = true;
  protected bool $_include_directories = true;
  protected array $_exclude_entries = ['.', '..'];
  protected bool $_stats = false;
  protected bool $_recursive = false;
  protected array $_check_extension = [];
  protected bool $_add_directory_to_filename = false;
  protected $_listing;

  /**
   * Constructor method to initialize the object with a directory path and optional statistics setting.
   *
   * @param string $directory The directory path to initialize. Defaults to an empty string.
   * @param bool $stats Optional parameter to enable or disable statistics. Defaults to false.
   *
   * @return void
   */
  public function __construct(string $directory = '', bool $stats = false)
  {
    $this->setDirectory(realpath($directory));
    $this->setStats($stats);
  }

  /**
   * Sets the directory path.
   *
   * @param string $directory The path of the directory to set.
   * @return void
   */
  public function setDirectory(string $directory)
  {
    $this->_directory = $directory;
  }

  /**
   * Sets whether to include files or not.
   *
   * @param bool $boolean A boolean value indicating whether to include files (true) or not (false).
   * @return void
   */
  public function setIncludeFiles(bool $boolean)
  {
    if ($boolean === true) {
      $this->_include_files = true;
    } else {
      $this->_include_files = false;
    }
  }

  /**
   * Sets whether directories should be included in processing.
   *
   * @param bool $boolean A boolean value indicating whether to include directories (true) or not (false).
   * @return void
   */
  public function setIncludeDirectories(bool $boolean)
  {
    if ($boolean === true) {
      $this->_include_directories = true;
    } else {
      $this->_include_directories = false;
    }
  }

  /**
   * Updates the list of entries to be excluded by adding new ones that are not already present in the list.
   *
   * @param array|string|null $entries The entries to be excluded, can be an array or a single string. If null, no action is taken.
   * @return void
   */
  public function setExcludeEntries(?array $entries)
  {
    if (is_array($entries)) {
      foreach ($entries as $value) {
        if (!in_array($value, $this->_exclude_entries)) {
          $this->_exclude_entries[] = $value;
        }
      }
    } elseif (is_string($entries)) {
      if (!in_array($entries, $this->_exclude_entries)) {
        $this->_exclude_entries[] = $entries;
      }
    }
  }

  /**
   * Sets the statistics indicator based on the given boolean value.
   *
   * @param bool $boolean Determines whether to set the statistics indicator to true or false.
   * @return void
   */
  public function setStats(bool $boolean)
  {
    if ($boolean === true) {
      $this->_stats = true;
    } else {
      $this->_stats = false;
    }
  }

  /**
   * Sets the recursive flag to the specified boolean value.
   *
   * @param bool $boolean Indicates whether the recursive flag should be set to true or false.
   * @return void
   */
  public function setRecursive(bool $boolean)
  {
    if ($boolean === true) {
      $this->_recursive = true;
    } else {
      $this->_recursive = false;
    }
  }

  /**
   * Adds a file extension to the list of extensions to check.
   *
   * @param string $extension The file extension to be added.
   * @return void
   */
  public function setCheckExtension(string $extension)
  {
    $this->_check_extension[] = mb_strtolower($extension);
  }

  /**
   * Sets whether the directory should be added to the filename.
   *
   * @param bool $boolean True to add the directory to the filename, false otherwise.
   * @return void
   */
  public function setAddDirectoryToFilename(bool $boolean)
  {
    if ($boolean === true) {
      $this->_add_directory_to_filename = true;
    } else {
      $this->_add_directory_to_filename = false;
    }
  }

  /**
   * Reads the contents of a directory and populates the listing based on the specified configurations.
   *
   * @param string $directory The directory path to read. If not provided, the default directory will be used.
   * @return void
   */
  public function read(string $directory = '')
  {
    if (empty($directory)) {
      $directory = $this->_directory;
    }

    if (!is_array($this->_listing)) {
      $this->_listing = array();
    }

    if ($dir = @dir($directory)) {
      while (($entry = $dir->read()) !== false) {
        if (!in_array($entry, $this->_exclude_entries)) {
          if (($this->_include_files === true) && is_file($dir->path . DIRECTORY_SEPARATOR . $entry)) {
            if (empty($this->_check_extension) || in_array(mb_strtolower(substr($entry, strrpos($entry, '.') + 1)), $this->_check_extension)) {
              if ($this->_add_directory_to_filename === true) {
                if ($dir->path !== $this->_directory) {
                  $entry = substr($dir->path, strlen($this->_directory) + 1) . DIRECTORY_SEPARATOR . $entry;
                }
              }

              $this->_listing[] = array('name' => $entry, 'is_directory' => false);

              if ($this->_stats === true) {
                $stats = array(
                  'size' => filesize($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'permissions' => fileperms($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'user_id' => fileowner($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'group_id' => filegroup($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'last_modified' => filemtime($dir->path . DIRECTORY_SEPARATOR . $entry)
                );

                $this->_listing[count($this->_listing) - 1] = array_merge($this->_listing[count($this->_listing) - 1], $stats);
              }
            }
          } elseif (is_dir($dir->path . DIRECTORY_SEPARATOR . $entry)) {
            if ($this->_include_directories === true) {
              $entry_name = $entry;

              if ($this->_add_directory_to_filename === true) {
                if ($dir->path !== $this->_directory) {
                  $entry_name = substr($dir->path, strlen($this->_directory) + 1) . DIRECTORY_SEPARATOR . $entry;
                }
              }

              $this->_listing[] = array('name' => $entry_name, 'is_directory' => true);

              if ($this->_stats === true) {
                $stats = array(
                  'size' => filesize($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'permissions' => fileperms($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'user_id' => fileowner($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'group_id' => filegroup($dir->path . DIRECTORY_SEPARATOR . $entry),
                  'last_modified' => filemtime($dir->path . DIRECTORY_SEPARATOR . $entry));
                $this->_listing[count($this->_listing) - 1] = array_merge($this->_listing[count($this->_listing) - 1], $stats);
              }
            }

            if ($this->_recursive === true) {
              $this->read($dir->path . DIRECTORY_SEPARATOR . $entry);
            }
          }
        }
      }

      $dir->close();

      unset($dir);
    }
  }

  /**
   * Retrieves the list of files, optionally sorted by directories.
   *
   * @param bool $sort_by_directories Determines whether to sort the files by directories.
   * @return array Returns an array of files. If no files are found, an empty array is returned.
   */
  public function getFiles(bool $sort_by_directories = true): array
  {
    if (!is_array($this->_listing)) {
      $this->read();
    }

    if (is_array($this->_listing) && (count($this->_listing) > 0)) {
      if ($sort_by_directories === true) {
        usort($this->_listing, array($this, '_sortListing'));
      }

      return $this->_listing;
    }

    return array();
  }

  /**
   * Retrieves the size of the listing.
   *
   * @return int The number of elements in the listing.
   */
  public function getSize(): int
  {
    if (!is_array($this->_listing)) {
      $this->read();
    }

    return count($this->_listing);
  }

  /**
   * Retrieves the current directory path.
   *
   * @return string The path of the directory.
   */
  public function getDirectory(): string
  {
    return $this->_directory;
  }

  /**
   * Compares two file listings for sorting purposes, prioritizing directories over files
   * and sorting alphabetically by name within each type.
   *
   * @param array $a The first file listing to compare.
   * @param array $b The second file listing to compare.
   * @return string Returns a negative integer, zero, or a positive integer
   *                as the first argument is considered less than, equal to,
   *                or greater than the second, respectively.
   */
  protected function _sortListing(array $a, array $b): string
  {
    return strcmp((($a['is_directory'] === true) ? 'D' : 'F') . $a['name'], (($b['is_directory'] === true) ? 'D' : 'F') . $b['name']);
  }
}

