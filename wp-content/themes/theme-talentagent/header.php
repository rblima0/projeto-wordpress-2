<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php wp_title( '|', true, 'right' ); echo PW_SITE_NAME; ?></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <?php wp_head(); ?>

    <link href="<?php echo PW_URL_THEME; ?>assets/default.css" rel="stylesheet" type="text/css" media="all" />
    <link href='http://fonts.googleapis.com/css?family=Oswald:400,300' rel='stylesheet' type='text/css' />
    <link href='http://fonts.googleapis.com/css?family=Abel|Satisfy' rel='stylesheet' type='text/css' />
    <!--[if IE 6]>
    <link href="<?php echo PW_URL_THEME; ?>assets/default_ie6.css" rel="stylesheet" type="text/css" />
    <![endif]-->
</head>
<body>
<div id="wrapper">
    <div id="header-wrapper">
        <div id="header" class="container">
            <div id="logo">
                <h1><a href="<?php echo PW_URL; ?>" title="<?php echo PW_SITE_NAME; ?>"><?php echo PW_SITE_NAME; ?></a></h1>
                <p><?php echo PW_SITE_DESCRIPTION; ?></p>
            </div>
            <div id="menu">
                <?php wp_nav_menu('menu-header'); ?>
            </div>
        </div>
    </div>