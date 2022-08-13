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

  class Zip extends \ZipArchive
  {
    public static function zipStatusString($status)
    {
      return match ((int)$status) {
        \ZipArchive::ER_OK => 'N No error',
        \ZipArchive::ER_MULTIDISK => 'N Multi-disk zip archives not supported',
        \ZipArchive::ER_RENAME => 'S Renaming temporary file failed',
        \ZipArchive::ER_CLOSE => 'S Closing zip archive failed',
        \ZipArchive::ER_SEEK => 'S Seek error',
        \ZipArchive::ER_READ => 'S Read error',
        \ZipArchive::ER_WRITE => 'S Write error',
        \ZipArchive::ER_CRC => 'N CRC error',
        \ZipArchive::ER_ZIPCLOSED => 'N Containing zip archive was closed',
        \ZipArchive::ER_NOENT => 'N No such file',
        \ZipArchive::ER_EXISTS => 'N File already exists',
        \ZipArchive::ER_OPEN => 'S Can\'t open file',
        \ZipArchive::ER_TMPOPEN => 'S Failure to create temporary file',
        \ZipArchive::ER_ZLIB => 'Z Zlib error',
        \ZipArchive::ER_MEMORY => 'N Malloc failure',
        \ZipArchive::ER_CHANGED => 'N Entry has been changed',
        \ZipArchive::ER_COMPNOTSUPP => 'N Compression method not supported',
        \ZipArchive::ER_EOF => 'N Premature EOF',
        \ZipArchive::ER_INVAL => 'N Invalid argument',
        \ZipArchive::ER_NOZIP => 'N Not a zip archive',
        \ZipArchive::ER_INTERNAL => 'N Internal error',
        \ZipArchive::ER_INCONS => 'N Zip archive inconsistent',
        \ZipArchive::ER_REMOVE => 'S Can\'t remove file',
        \ZipArchive::ER_DELETED => 'N Entry has been deleted',
        default => sprintf('Unknown status %s', $status),
      };
    }

    public static function isDir($path)
    {
      return substr($path, -1) == '/';
    }

    /**
     * @return array
     */
    public function getTree(): array
    {
      $Tree = [];

      for ($i = 0; $i < $this->numFiles; $i++) {
        $path = $this->getNameIndex($i);
        $pathBySlash = array_values(explode('/', $path));
        $c = \count($pathBySlash);
        $temp = &$Tree;

        for ($j = 0; $j < $c - 1; $j++) {
          if (isset($temp[$pathBySlash[$j]])) {
            $temp = &$temp[$pathBySlash[$j]];
          } else {
            $temp = &$temp[$pathBySlash[$j]];
          }

          if ($this->isDir($path) !== true) {
            $temp[] = $pathBySlash[$c - 1];
          }
        }
      }

      return $Tree;
    }

    /**
     * creates a compressed zip file
     * @param array $files
     * @param string $destination
     * @param bool $overwrite
     * @return bool
     */
    public static function Create(array $files = [], string $destination = '', bool $overwrite = false)
    {
      //if the zip file already exists and overwrite is false, return false
      if (file_exists($destination) && !$overwrite) {
        return false;
      }

      $valid_files = [];

      if (\is_array($files)) {
        foreach ($files as $file) {
          if (file_exists($file)) {
            $valid_files[] = $file;
          }
        }
      }

      if (\count($valid_files)) {
        $zip = new \ZipArchive();
        if ($zip->open($destination, $overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE) !== true) {
          return false;
        }
//add the files
        foreach ($valid_files as $file) {
          $zip->addFile($file, $file);
        }
//debug
//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

        $zip->close();

        return file_exists($destination);
      } else {
        return false;
      }
    }
  }