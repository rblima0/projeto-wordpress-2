<!DOCTYPE HTML>
<html>
<head>
    <title><?php wp_title('|', true, 'right'); ?>
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body id="top">

<!-- Header -->
<header id="header">
    <div class="inner">
        <a href="#" class="image avatar"><img src="<?php echo PW_URL_THEME; ?>images/avatar.jpg" alt="" /></a>
        <h1><strong><?php echo PW_SITE_NAME; ?></strong><br />
            <?php echo PW_SITE_DESCRIPTION; ?><br />
            criado por <a href="#">Rblima0</a>.</h1>
    </div>
</header>