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

use function in_array;
use function is_array;

/**
 * Class responsible for handling file uploads via POST or PUT requests, validating file extensions, permissions,
 * and destination validations, and saving the uploaded files to the desired directory.
 */
class Upload
{
  protected $_file;
  protected string $_filename;
  protected string $_destination;
  protected int $_permissions;
  protected array $_extensions = [];
  protected bool $_replace = false;
  protected array $_upload = [];

  /**
   * Constructor to initialize file handling with specified parameters.
   *
   * @param string $file The file to be processed.
   * @param string $destination The destination directory where the file will be processed.
   * @param string|null $permissions Optional. File permissions to be set. Defaults to '777' if not specified.
   * @param array|null $extensions Optional. Additional extensions to be added.
   * @param bool $replace Indicates whether to replace existing files. Defaults to false.
   *
   * @return void
   */
  public function __construct($file, $destination, $permissions = null, $extensions = null, bool $replace = false)
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

  /**
   * Validates and processes file upload requests using either PUT or POST methods.
   * Checks if the uploaded file meets configured requirements such as extensions,
   * and ensures it is saved to a writable destination directory.
   *
   * @return bool Returns true if the file upload is successfully validated and processed;
   *              otherwise, false.
   */
  public function check(): bool
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
        $this->_upload = [
          'type' => 'PUT',
          'name' => $_GET[$this->_file],
          'size' => $size,
          'temp_filename' => $temp_filename
        ];
      } else {
        $CLICSHOPPING_MessageStack->add('File Upload [PUT]: $_SERVER[\'CONTENT_LENGTH\'] (' . (int)$_SERVER['CONTENT_LENGTH'] . ') not set or not equal to stream size (' . (int)$size . ')', 'warning');
      }
    } elseif (isset($_FILES[$this->_file])) {
      if (isset($_FILES[$this->_file]['tmp_name']) && !empty($_FILES[$this->_file]['tmp_name']) && is_uploaded_file($_FILES[$this->_file]['tmp_name']) && ($_FILES[$this->_file]['size'] > 0)) {
        $this->_upload = [
          'type' => 'POST',
          'name' => $_FILES[$this->_file]['name'],
          'size' => $_FILES[$this->_file]['size'],
          'tmp_name' => $_FILES[$this->_file]['tmp_name']
        ];
      }
    }

    if (!empty($this->_upload)) {
      if (!empty($this->_extensions)) {
        if (!in_array(mb_strtolower(substr($this->_upload['name'], strrpos($this->_upload['name'], '.') + 1)), $this->_extensions)) {
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

  /**
   * Saves the uploaded file to the specified destination directory.
   * Depending on the upload type ('PUT' or 'POST'), the method either renames or moves the file
   * to the target location, ensuring no duplicate names are present if the `_replace` property is true.
   * Permissions are applied to the saved file.
   * A warning message is added to the message stack if the file cannot be saved.
   *
   * @return bool Returns true if the file is successfully saved, otherwise returns false.
   */
  public function save()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    if ($this->_replace === true) {
      while (file_exists($this->_destination . DIRECTORY_SEPARATOR . $this->getFilename())) {

        $salt = md5(rand(1, 100000));
        $salt = substr($salt, 0, 10);

        $this->setFilename($salt . '_' . $this->getFilename());
      }
    }

    if ($this->_upload['type'] == 'PUT') {
      if (rename(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $this->_upload['temp_filename'], $this->_destination . DIRECTORY_SEPARATOR . $this->getFilename())) {
        chmod($this->_destination . DIRECTORY_SEPARATOR . $this->getFilename(), $this->_permissions);

        return true;
      }
    } elseif ($this->_upload['type'] == 'POST') {
      if (move_uploaded_file($this->_upload['tmp_name'], $this->_destination . DIRECTORY_SEPARATOR . $this->getFilename())) {
        chmod($this->_destination . DIRECTORY_SEPARATOR . $this->getFilename(), $this->_permissions);

        return true;
      }
    }

    $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_file_not_saved'), 'warning');

    return false;
  }

  /**
   * Sets the permissions for the current object.
   *
   * @param mixed $permissions The permissions value to be set, which will be converted to an octal decimal.
   * @return void
   */
  public function setPermissions($permissions)
  {
    $this->_permissions = octdec($permissions);
  }

  /**
   * Adds one or more extensions to the existing list of extensions.
   *
   * @param mixed $extensions A single extension as a string or multiple extensions as an array.
   * @return void
   */
  public function addExtensions($extensions)
  {
    if (!is_array($extensions)) {
      $extensions = [$extensions];
    }

    $extensions = array_map('mb_strtolower', $extensions);

    $this->_extensions = array_merge($this->_extensions, $extensions);
  }

  /**
   * Sets the replace flag.
   *
   * @param bool $bool Indicates whether to enable or disable the replace flag.
   * @return void
   */
  public function setReplace(bool $bool)
  {
    $this->_replace = ($bool === true);
  }

  /**
   * Retrieves the destination property.
   *
   * @return mixed The value of the destination property.
   */
  public function getDestination()
  {
    return $this->_destination;
  }

  /**
   * Sets the filename property.
   *
   * @param string $filename The name of the file to be set.
   * @return void
   */
  public function setFilename(string $filename)
  {
    $this->_filename = $filename;
  }

  /**
   * Retrieves the filename.
   *
   * @return string Returns the filename if set, otherwise returns the upload's name.
   */
  public function getFilename()
  {
    if (isset($this->_filename)) {
      return $this->_filename;
    }

    return $this->_upload['name'];
  }

  /**
   * Retrieves the file extension from the filename in lowercase.
   *
   * @return string The file extension in lowercase.
   */
  public function getExtension(): string
  {
    return mb_strtolower(substr($this->getFilename(), strrpos($this->getFilename(), '.') + 1));
  }

  /**
   * Retrieves the permissions property.
   *
   * @return mixed Returns the value of the _permissions property.
   */
  public function getPermissions()
  {
    return $this->_permissions;
  }

  /**
   * Destructor method that ensures the temporary uploaded file is deleted if it exists.
   *
   * @return void
   */
  public function __destruct()
  {
    if (isset($this->_upload['temp_filename']) && file_exists(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $this->_upload['temp_filename'])) {
      unlink(CLICSHOPPING::BASE_DIR . 'Work/Temp/' . $this->_upload['temp_filename']);
    }
  }
}
