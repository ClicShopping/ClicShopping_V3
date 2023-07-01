<?php
  use ClicShopping\OM\HTTP;

  if (!isset($_GET['Admin']) || $_GET['Admin'] !== 'ClicShoppingAdmin') {
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CKEditor 5 – Classic editor</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP::getShopUrlDomain(); ?>ext/elFinder-master/js/elfinder.full.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP::getShopUrlDomain(); ?>ext/elFinder-master/css/theme.css"/>
</head>
<body>
</body>
</html>
