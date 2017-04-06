<?php

define( 'PW_URL',           get_home_url() . '/' );
define( 'PW_URL_THEME',     get_bloginfo( 'template_url' ) . '/' );
define( 'PW_SITE_NAME',     get_bloginfo( 'title' ) );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo PW_SITE_NAME; ?></title>
<link href="http://fonts.googleapis.com/css?family=Archivo+Narrow:400,700" rel="stylesheet" type="text/css" />
<link href="<?php echo PW_URL_THEME; ?>style.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
<div id="wrapper" class="container">

	<div id="header">
		<div id="logo">
            <h1><a href="<?php echo PW_URL; ?>" title="<?php echo PW_SITE_NAME; ?>"><?php echo PW_SITE_NAME; ?></a></h1>
            <p><?php bloginfo( 'description' ); ?></p>
		</div>
	</div>

	<div id="banner">
        <img src="<?php echo PW_URL_THEME; ?>images/pic01.jpg" width="940" height="585" alt="" />
    </div>

	<div id="page">
		<div id="content">
			<div id="box1">
				<h2>Uma revista feita por atletas profissionais!</h2>
				<a href="#"><img src="<?php echo PW_URL_THEME; ?>images/pic02.jpg" width="600" height="200" alt="" /></a>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. <strong>Praesent a tristique velit</strong>. Aliquam quis leo massa, at vehicula libero. Vestibulum iaculis mattis ullamcorper. Aliquam lectus libero, volutpat a varius sed, tincidunt quis felis. Vivamus vitae lacus nec nibh tristique ornare quis aliquam lorem. Nulla sit amet ipsum ut lorem tristique lacinia. Ut tempor odio sed magna tempus et venenatis nunc iaculis. Fusce vitae magna sit amet risus commodo porta rhoncus laoreet diam. Duis tempus porttitor eros, eu vehicula enim pellentesque in.</p>
			</div>
		</div>
		<div id="sidebar">
			<div>
				<h2>Você encontrará por aqui:</h2>
				<ul class="style1">
					<li class="first">
						<p>Donec leo, vivamus fermentum nibh in augue praesent a lacus at urna congue rutrum.</p>
					</li>
					<li>
						<p>Donec leo, vivamus fermentum nibh in augue praesent a lacus at urna congue rutrum.</p>
					</li>
					<li>
						<p>Donec leo, vivamus fermentum nibh in augue praesent a lacus at urna congue rutrum.</p>
					</li>
				</ul>
			</div>

		</div>
	</div>
	<div id="footer">
		<p>NomeDoSite.com.br | Todos os direitos reservados.</p>
	</div>
</div>
</body>
</html>