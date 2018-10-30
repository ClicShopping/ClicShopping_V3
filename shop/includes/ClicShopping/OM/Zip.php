<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\OM;

  use ClicShopping\OM\Registry;

//  class Zip {

  class Zip extends \ZipArchive {

    public function zipStatusString( $status ) {
      switch( (int) $status ) {
        case ZipArchive::ER_OK           : return 'N No error';
        case ZipArchive::ER_MULTIDISK    : return 'N Multi-disk zip archives not supported';
        case ZipArchive::ER_RENAME       : return 'S Renaming temporary file failed';
        case ZipArchive::ER_CLOSE        : return 'S Closing zip archive failed';
        case ZipArchive::ER_SEEK         : return 'S Seek error';
        case ZipArchive::ER_READ         : return 'S Read error';
        case ZipArchive::ER_WRITE        : return 'S Write error';
        case ZipArchive::ER_CRC          : return 'N CRC error';
        case ZipArchive::ER_ZIPCLOSED    : return 'N Containing zip archive was closed';
        case ZipArchive::ER_NOENT        : return 'N No such file';
        case ZipArchive::ER_EXISTS       : return 'N File already exists';
        case ZipArchive::ER_OPEN         : return 'S Can\'t open file';
        case ZipArchive::ER_TMPOPEN      : return 'S Failure to create temporary file';
        case ZipArchive::ER_ZLIB         : return 'Z Zlib error';
        case ZipArchive::ER_MEMORY       : return 'N Malloc failure';
        case ZipArchive::ER_CHANGED      : return 'N Entry has been changed';
        case ZipArchive::ER_COMPNOTSUPP  : return 'N Compression method not supported';
        case ZipArchive::ER_EOF          : return 'N Premature EOF';
        case ZipArchive::ER_INVAL        : return 'N Invalid argument';
        case ZipArchive::ER_NOZIP        : return 'N Not a zip archive';
        case ZipArchive::ER_INTERNAL     : return 'N Internal error';
        case ZipArchive::ER_INCONS       : return 'N Zip archive inconsistent';
        case ZipArchive::ER_REMOVE       : return 'S Can\'t remove file';
        case ZipArchive::ER_DELETED      : return 'N Entry has been deleted';

        default: return sprintf('Unknown status %s', $status );
      }
    }

    public function isDir($path) {
      return substr($path, -1) == '/';
    }

    public function getTree()  {
      $Tree = array();
      $pathArray = array();
      for ($i=0; $i < $this->numFiles; $i++) {
        $path = $this->getNameIndex($i);
        $pathBySlash = array_values(explode('/', $path));
        $c = count($pathBySlash);
        $temp = &$Tree;
        for ($j=0; $j < $c-1; $j++) {
          if (isset($temp[$pathBySlash[$j]])) {
            $temp = &$temp[$pathBySlash[$j]];
          } else {
            $temp = &$temp[$pathBySlash[$j]];
          }
          if ($this->isDir($path) !== true) {
            $temp[] = $pathBySlash[$c-1];
          }
        }
      }
      return $Tree;
    }
  }