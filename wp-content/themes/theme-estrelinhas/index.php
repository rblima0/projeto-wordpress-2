<?php get_header(); ?>

<div id="wrapper">
	<div id="page">
		<div id="page-bgtop">
			<div id="page-bgbtm">
				<div id="content">
                    <?php if ( !is_user_logged_in() ) {
                    $about = pw_get_template( 'pg-about.php' );
                    if ( !isset( $about->ID ) ) {
                        $title = 'Site de conteúdo restrito!';
                        $content = 'É preciso estar logado no sistema para interagir com o projeto.';
                    } else {
                        $title = $about->post_title;
                        $content = apply_filters( 'the_content', $about->post_content );
                    } ?>

                    <div class="post">
						<h2 class="title"><?php echo $title; ?></h2>
						<div style="clear: both;">&nbsp;</div>
						<div class="entry"><?php echo $content; ?></div>
					</div>

                    <?php } else { ?>
                    <?php $i=1; while ( have_posts() ) { the_post(); ?>
                    <div class="post<?php if ( $i%2 == 0 ) echo ' post-alt'; ?>">
                        <h2 class="title">
                            <?php
                            printf( '<a href="%1$s" title="%2$s">%2$s</a>',
                                get_permalink(),
                                get_the_title()
                            );?>
                        </h2>
						<p class="meta">
                            <span class="date"><?php echo get_the_date( 'F d, Y' ); ?></span>
                            <span class="posted">Posted by <?php the_author_posts_link(); ?></span>
                        </p>
						<div style="clear: both;">&nbsp;</div>
						<div class="entry">
                            <?php the_content(); ?>
                        </div>
					</div>
                    <?php $i++; } pw_paginate_links(); ?>
                    <?php } ?>
					<div style="clear: both;">&nbsp;</div>
				</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>