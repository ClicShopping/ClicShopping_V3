<!DOCTYPE html>
<html dir="ltr" lang="fr">
<head>
  <meta charset="UTF-8" />

  <title>Error / Erreur</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="generator" content="ClicShopping" />
    <meta name="author" content="e-Imaginis & Innov Concept" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
    <![endif]-->

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet" />
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="stylesheet.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div style="padding-top:50px; padding-bottom:20px; text-align: center;"><h1><?php echo $_SERVER['HTTP_HOST']; ?></h1></div>
      <div class="error-template">
        <h1>Oops!</h1>
        <h2>403 Not Found</h2>
        <div class="error-details">
          Sorry, an error has occured, Forbidden error Access !
        </div>
        <div class="error-actions">
          <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>"><span class="glyphicon glyphicon-home"></span>  Go to the web store</a>
        <div>
      </div>
    </div>
  </div>
</div>
</body>
</html>