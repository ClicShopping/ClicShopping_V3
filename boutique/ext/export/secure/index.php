<?php
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;

  chdir('../../../');
  require('includes/application_top.php');
// Demarrage de la bufferisation de sortie
   ob_start();


  CLICSHOPPING::redirect('index.php');

// Afficher le contenu du buffer
    ob_end_flush();
