<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Newsletter</title>
  <meta name="Description" content="Newsletters of ClicShopping</description" />
  <style type="text/css">
  a img {
  border:0;
  }
  </style>
</head>
<body>
<p align="center"><a href="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>"><img src="../../image/logos/invoice/invoice_logo.png"></a></p>
<h2 align="center">Newsletters</h2>
<blockquote><a href="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>">Index</a></blockquote>
<?php
  $template_directory = getcwd();
  if ($handle = opendir($template_directory)) {
    $i=1;

    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && $file != 'index.php' && $file != '.htaccess') {
            echo '<blockquote>' . $i . ' - <a href=' . $file . '>' . $file . '</a></blockquote>';
        $i++;
        }
    }
    closedir($handle);
  }
?>
</body>
</html>
