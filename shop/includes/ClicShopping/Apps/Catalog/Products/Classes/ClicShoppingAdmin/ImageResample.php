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

  /*
  * File: SimpleImage.php
  * Author: Simon Jarvis
  * Modified by: Miguel Fermin
  * Based in: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
  *
  * This program is free software; you can redistribute it and/or
  * modify it under the terms of the GNU General Public License
  * as published by the Free Software Foundation; either version 2
  * of the License, or (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU General Public License for more details:
  * http://www.gnu.org/licenses/gpl.html
  * https://gist.github.com/908143
  */

  namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;

  class ImageResample
  {

    protected $image;
    protected $image_type;
    protected $filename;
    protected $height;
    protected $width;
    protected $size;
    protected $scale;
    protected $x;
    protected $y;

    public function __construct($filename = null)
    {
      if (!empty($filename)) {
        $this->load($filename);
      }
    }

    public function load($filename)
    {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if ($this->image_type == IMAGETYPE_JPEG) {
        $this->image = imagecreatefromjpeg($filename);
      } elseif ($this->image_type == IMAGETYPE_GIF) {
        $this->image = imagecreatefromgif($filename);
      } elseif ($this->image_type == IMAGETYPE_PNG) {
        $this->image = imagecreatefrompng($filename);
      } elseif ($this->image_type == IMAGETYPE_WEBP) {
        $this->image = imagecreatefromwebp($filename);
      } else {
        if (isset($_GET['ProductsAttributes'])) {
          CLICSHOPPING::redirect(null, 'A&Catalog\ProductsAttributes&ProductsAttributes&error=fileNotSupported');
        }

        if (isset($_GET['Porducts'])) {
          CLICSHOPPING::redirect(null, 'A&Catalog\Products&Products&error=fileNotSupported');
        }
      }

    }

    public function save($filename, $image_type = IMAGETYPE_PNG, $compression = 100, $permissions = null)
    {
      if ($image_type == IMAGETYPE_JPEG) {
        imagejpeg($this->image, $filename, $compression);
      } elseif ($image_type == IMAGETYPE_GIF) {
        imagegif($this->image, $filename);
      } elseif ($image_type == IMAGETYPE_PNG) {
        imagepng($this->image, $filename);
      } elseif ($image_type == IMAGETYPE_WEBP) {
        imagewebp($this->image, $filename);
      }

      if (!is_null($permissions)) {
        chmod($filename, $permissions);
      }
    }

    public function output($image_type = IMAGETYPE_JPEG, $quality = 80)
    {
      if ($image_type == IMAGETYPE_JPEG) {
        header("Content-type: image/jpeg");
        imagejpeg($this->image, null, $quality);
      } elseif ($image_type == IMAGETYPE_GIF) {
        header("Content-type: image/gif");
        imagegif($this->image);
      } elseif ($image_type == IMAGETYPE_PNG) {
        header("Content-type: image/png");
        imagepng($this->image);
      } elseif ($image_type == IMAGETYPE_WEBP) {
        header("Content-type: image/webp");
        imagepng($this->image);
      }
    }

    public function getWidth()
    {
      return imagesx($this->image);
    }

    public function getHeight()
    {
      return imagesy($this->image);
    }

    public function resizeToHeight($height)
    {
      $ratio = $height / $this->getHeight();
      $width = round($this->getWidth() * $ratio);
      $this->resize($width, $height);
    }

    public function resizeToWidth($width)
    {
      $ratio = $width / $this->getWidth();
      $height = round($this->getHeight() * $ratio);
      $this->resize($width, $height);
    }

    public function square($size)
    {
      $new_image = imagecreatetruecolor($size, $size);

      if ($this->getWidth() > $this->getHeight()) {
        $this->resizeToHeight($size);

        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);
      } else {
        $this->resizeToWidth($size);

        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);
      }

      $this->image = $new_image;
    }

    public function scale($scale)
    {
      $width = $this->getWidth() * $scale / 100;
      $height = $this->getHeight() * $scale / 100;
      $this->resize($width, $height);
    }

    public function resize($width, $height)
    {
      $new_image = imagecreatetruecolor($width, $height);

      imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
      imagealphablending($new_image, false);
      imagesavealpha($new_image, true);

      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
    }

    public function cut($x, $y, $width, $height)
    {
      $new_image = imagecreatetruecolor($width, $height);

      imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
      imagealphablending($new_image, false);
      imagesavealpha($new_image, true);

      imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);

      $this->image = $new_image;
    }

    public function maxarea($width, $height = null)
    {
      $height = $height ? $height : $width;

      if ($this->getWidth() > $width) {
        $this->resizeToWidth($width);
      }
      if ($this->getHeight() > $height) {
        $this->resizeToheight($height);
      }
    }

    public function minarea($width, $height = null)
    {
      $height = $height ? $height : $width;

      if ($this->getWidth() < $width) {
        $this->resizeToWidth($width);
      }
      if ($this->getHeight() < $height) {
        $this->resizeToheight($height);
      }
    }

    public function cutFromCenter($width, $height)
    {

      if ($width < $this->getWidth() && $width > $height) {
        $this->resizeToWidth($width);
      }
      if ($height < $this->getHeight() && $width < $height) {
        $this->resizeToHeight($height);
      }

      $x = ($this->getWidth() / 2) - ($width / 2);
      $y = ($this->getHeight() / 2) - ($height / 2);

      return $this->cut($x, $y, $width, $height);
    }

    public function maxareafill($width, $height, $red = 0, $green = 0, $blue = 0)
    {
      $this->maxarea($width, $height);
      $new_image = imagecreatetruecolor($width, $height);
      $color_fill = imagecolorallocate($new_image, $red, $green, $blue);
      imagefill($new_image, 0, 0, $color_fill);
      imagecopyresampled($new_image,
        $this->image,
        floor(($width - $this->getWidth()) / 2),
        floor(($height - $this->getHeight()) / 2),
        0, 0,
        $this->getWidth(),
        $this->getHeight(),
        $this->getWidth(),
        $this->getHeight()
      );
      $this->image = $new_image;
    }

  }




  /* usage
  The first example below will load a file named picture.jpg resize it to 250 pixels wide and 400 pixels high and resave it as picture2.jpg
     $image = new SimpleImage();
     $image->load('picture.jpg');
     $image->resize(250,400);
     $image->save('picture2.jpg');

  If you want to resize to a specifed width but keep the dimensions ratio the same then the script can work out the required height for you, just use the resizeToWidth function.
     $image = new SimpleImage();
     $image->load('picture.jpg');
     $image->resizeToWidth(250);
     $image->save('picture2.jpg');

  You may wish to scale an image to a specified percentage like the following which will resize the image to 50% of its original width and height
     $image = new SimpleImage();
     $image->load('picture.jpg');
     $image->scale(50);
     $image->save('picture2.jpg');

  You can of course do more than one thing at once. The following example will create two new images with heights of 200 pixels and 500 pixels
     $image = new SimpleImage();
     $image->load('picture.jpg');
     $image->resizeToHeight(500);
     $image->save('picture2.jpg');
     $image->resizeToHeight(200);
     $image->save('picture3.jpg');

// Resize the canvas and fill the empty space with a color of your choice
    $image->maxareafill(600,400, 32, 39, 240);
    $image->save('lemon_filled.jpg');

//  The output function lets you output the image straight to the browser without having to save the file. Its useful for on the fly thumbnail generation


  <?php
     if( isset($_POST['submit']) ) {
        include_once('SimpleImage.php');
        $image = new SimpleImage();
        $image->load($_FILES['uploaded_image']['tmp_name']);
        $image->resizeToWidth(150);
        $image->output();
     } else {
  ?>

     <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="uploaded_image" />
        <input type="submit" name="submit" value="Upload" />
     </form>

  <?php
     }
  ?>

  */

