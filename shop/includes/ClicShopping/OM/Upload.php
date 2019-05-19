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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\FileSystem;

  class Upload
  {
    protected $_file;
    protected $_filename;
    protected $_destination;
    protected $_permissions;
    protected $_extensions = array();
    protected $_replace = false;
    protected $_upload = array();

    public function __construct($file, $destination, $permissions = null, $extensions = null, $replace = false)
    {
// Remove trailing directory separator
      if (substr($destination, -1) == '/') {
        $destination = substr($destination, 0, -1);
      }

      if (!isset($permissions)) {
        $permissions = '777';
      }

      $this->_file = $file;
      $this->_destination = $destination;

      $this->setPermissions($permissions);

      if (isset($extensions)) {
        $this->addExtensions($extensions);
      }

      $this->_replace = $replace;
    }

    public function check()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_GET[$this->_file])) {
        $temp_filename = 'temp_' . mt_rand();

        while (file_exists(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $temp_filename)) {
          $temp_filename = 'temp_' . mt_rand();
        }

        $input = fopen('php://input', 'r');

        $size = file_put_contents(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $temp_filename, $input);

        fclose($input);

        if (isset($_SERVER['CONTENT_LENGTH']) && ($size == $_SERVER['CONTENT_LENGTH'])) {
          $this->_upload = ['type' => 'PUT',
            'name' => $_GET[$this->_file],
            'size' => $size,
            'temp_filename' => $temp_filename
          ];

        } else {
          $CLICSHOPPING_MessageStack->add('File Upload [PUT]: $_SERVER[\'CONTENT_LENGTH\'] (' . (int)$_SERVER['CONTENT_LENGTH'] . ') not set or not equal to stream size (' . (int)$size . ')', 'warning');
        }
      } elseif (isset($_FILES[$this->_file])) {
        if (isset($_FILES[$this->_file]['tmp_name']) && !empty($_FILES[$this->_file]['tmp_name']) && is_uploaded_file($_FILES[$this->_file]['tmp_name']) && ($_FILES[$this->_file]['size'] > 0)) {
          $this->_upload = ['type' => 'POST',
            'name' => $_FILES[$this->_file]['name'],
            'size' => $_FILES[$this->_file]['size'],
            'tmp_name' => $_FILES[$this->_file]['tmp_name']
          ];
        } else {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_file_cannot_process') . '$_FILES[' . $this->_file . '][\'tmp_name\']', 'warning');
        }
      }

      if (!empty($this->_upload)) {
        if (!empty($this->_extensions)) {
          if (!in_array(strtolower(substr($this->_upload['name'], strrpos($this->_upload['name'], '.') + 1)), $this->_extensions)) {
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_filetype_not_allowed') . implode(', ', $this->_extensions), 'warning');

            return false;
          }
        }

        if (!is_dir($this->_destination)) {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_catalog_image_directory_does_not_exist') . $this->_destination, 'warning');

          return false;
        }

        if (!FileSystem::isWritable($this->_destination)) {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getConfig('error_catalog_image_directory_not_writeable') . $this->_destination, 'warning');

          return false;
        }

        return true;
      }

      return false;
    }

    public function save()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->_replace === true) {
        while (file_exists($this->_destination . '/' . $this->getFilename())) {

          $salt = md5(int(rand(1, 100000)));
          $salt = substr($salt, 0, 10);

          $this->setFilename($salt . '_' . $this->getFilename());
        }
      }

      if ($this->_upload['type'] == 'PUT') {
        if (rename(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $this->_upload['temp_filename'], $this->_destination . '/' . $this->getFilename())) {
          chmod($this->_destination . '/' . $this->getFilename(), $this->_permissions);

          return true;
        }
      } elseif ($this->_upload['type'] == 'POST') {
        if (move_uploaded_file($this->_upload['tmp_name'], $this->_destination . '/' . $this->getFilename())) {
          chmod($this->_destination . '/' . $this->getFilename(), $this->_permissions);

          return true;
        }
      }

      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_file_not_saved'), 'warning');

      return false;
    }

    public function setPermissions($permissions)
    {
      $this->_permissions = octdec($permissions);
    }

    public function addExtensions($extensions)
    {
      if (!is_array($extensions)) {
        $extensions = array($extensions);
      }

      $extensions = array_map('strtolower', $extensions);

      $this->_extensions = array_merge($this->_extensions, $extensions);
    }

    public function setReplace($bool)
    {
      $this->_replace = ($bool === true);
    }

    public function getDestination()
    {
      return $this->_destination;
    }

    public function setFilename($filename)
    {
      $this->_filename = $filename;
    }

    public function getFilename()
    {
      if (isset($this->_filename)) {
        return $this->_filename;
      }

      return $this->_upload['name'];
    }

    public function getExtension()
    {
      return strtolower(substr($this->getFilename(), strrpos($this->getFilename(), '.') + 1));
    }

    public function getPermissions()
    {
      return $this->_permissions;
    }

    public function __destruct()
    {
      if (isset($this->_upload['temp_filename']) && file_exists(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $this->_upload['temp_filename'])) {
        unlink(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $this->_upload['temp_filename']);
      }
    }
  }
