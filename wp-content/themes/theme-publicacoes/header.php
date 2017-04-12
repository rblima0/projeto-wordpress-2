<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php wp_title( '|', true, 'right' ); echo PW_SITE_NAME; ?></title>
    <?php wp_head(); ?>

    <link href='http://fonts.googleapis.com/css?family=Oswald:400,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
    <link href="<?php echo PW_URL_THEME; ?>style.css" rel="stylesheet" type="text/css" media="screen" />

</head>
<body>
<div id="wrapper">
    <div id="header-wrapper">
        <div id="header" class="container">
            <div id="logo">
                h1><a href="<?php echo PW_URL; ?>" title="<?php echo PW_SITE_NAME; ?>"><?php echo PW_SITE_NAME; ?></a></h1>
            </div>
            <div id="menu">
                <?php wp_nav_menu('menu-header'); ?>
            </div>
        </div>
        <div id="banner">
            <div class="content"><img src="<?php echo PW_URL_THEME; ?>images/img02.jpg" width="1000" height="300" alt="" /></div>
        </div>
    </div>