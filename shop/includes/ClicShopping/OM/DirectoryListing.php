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

  class DirectoryListing
  {
    protected $_directory = '';
    protected $_include_files = true;
    protected $_include_directories = true;
    protected $_exclude_entries = array('.', '..');
    protected $_stats = false;
    protected $_recursive = false;
    protected $_check_extension = array();
    protected $_add_directory_to_filename = false;
    protected $_listing;

    public function __construct($directory = '', $stats = false)
    {
      $this->setDirectory(realpath($directory));
      $this->setStats($stats);
    }

    public function setDirectory($directory)
    {
      $this->_directory = $directory;
    }

    public function setIncludeFiles($boolean)
    {
      if ($boolean === true) {
        $this->_include_files = true;
      } else {
        $this->_include_files = false;
      }
    }

    public function setIncludeDirectories($boolean)
    {
      if ($boolean === true) {
        $this->_include_directories = true;
      } else {
        $this->_include_directories = false;
      }
    }

    public function setExcludeEntries($entries)
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

    public function setStats($boolean)
    {
      if ($boolean === true) {
        $this->_stats = true;
      } else {
        $this->_stats = false;
      }
    }

    public function setRecursive($boolean)
    {
      if ($boolean === true) {
        $this->_recursive = true;
      } else {
        $this->_recursive = false;
      }
    }

    public function setCheckExtension($extension)
    {
      $this->_check_extension[] = strtolower($extension);
    }

    public function setAddDirectoryToFilename($boolean)
    {
      if ($boolean === true) {
        $this->_add_directory_to_filename = true;
      } else {
        $this->_add_directory_to_filename = false;
      }
    }

    public function read($directory = '')
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
            if (($this->_include_files === true) && is_file($dir->path . '/' . $entry)) {
              if (empty($this->_check_extension) || in_array(strtolower(substr($entry, strrpos($entry, '.') + 1)), $this->_check_extension)) {
                if ($this->_add_directory_to_filename === true) {
                  if ($dir->path != $this->_directory) {
                    $entry = substr($dir->path, strlen($this->_directory) + 1) . '/' . $entry;
                  }
                }

                $this->_listing[] = array('name' => $entry,
                  'is_directory' => false);
                if ($this->_stats === true) {
                  $stats = array('size' => filesize($dir->path . '/' . $entry),
                    'permissions' => fileperms($dir->path . '/' . $entry),
                    'user_id' => fileowner($dir->path . '/' . $entry),
                    'group_id' => filegroup($dir->path . '/' . $entry),
                    'last_modified' => filemtime($dir->path . '/' . $entry));
                  $this->_listing[count($this->_listing) - 1] = array_merge($this->_listing[count($this->_listing) - 1], $stats);
                }
              }
            } elseif (is_dir($dir->path . '/' . $entry)) {
              if ($this->_include_directories === true) {
                $entry_name = $entry;

                if ($this->_add_directory_to_filename === true) {
                  if ($dir->path != $this->_directory) {
                    $entry_name = substr($dir->path, strlen($this->_directory) + 1) . '/' . $entry;
                  }
                }

                $this->_listing[] = array('name' => $entry_name,
                  'is_directory' => true);
                if ($this->_stats === true) {
                  $stats = array('size' => filesize($dir->path . '/' . $entry),
                    'permissions' => fileperms($dir->path . '/' . $entry),
                    'user_id' => fileowner($dir->path . '/' . $entry),
                    'group_id' => filegroup($dir->path . '/' . $entry),
                    'last_modified' => filemtime($dir->path . '/' . $entry));
                  $this->_listing[count($this->_listing) - 1] = array_merge($this->_listing[count($this->_listing) - 1], $stats);
                }
              }

              if ($this->_recursive === true) {
                $this->read($dir->path . '/' . $entry);
              }
            }
          }
        }

        $dir->close();
        unset($dir);
      }
    }

    public function getFiles($sort_by_directories = true)
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

    public function getSize()
    {
      if (!is_array($this->_listing)) {
        $this->read();
      }

      return count($this->_listing);
    }

    public function getDirectory()
    {
      return $this->_directory;
    }

    protected function _sortListing($a, $b)
    {
      return strcmp((($a['is_directory'] === true) ? 'D' : 'F') . $a['name'], (($b['is_directory'] === true) ? 'D' : 'F') . $b['name']);
    }
  }

