<!DOCTYPE HTML>
<html>
<head>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php wp_head(); ?>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--[if lte IE 8]><script src="<?php echo PW_URL_THEME; ?>assets/js/ie/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="<?php echo PW_URL_THEME; ?>assets/css/main.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="<?php echo PW_URL_THEME; ?>assets/css/ie8.css" /><![endif]-->
</head>
<body id="top">

<!-- Header -->
<header id="header">
    <div class="inner">
        <a href="#" class="image avatar"><img src="<?php echo PW_URL_THEME; ?>images/avatar.jpg" alt="" /></a>
        <h1><strong><?php echo PW_SITE_NAME; ?></strong><br />
            <?php bloginfo( 'description' ); ?><br />
            criado por <a href="#">Rblima0</a>.</h1>
    </div>
</header>