<?php get_header(); ?>

	<div id="page">
		<div id="content">
            <?php the_post(); ?>
			<div class="post">
				<h2 class="title">
                    <?php the_title(); ?>
                </h2>
				<div style="clear: both;">&nbsp;</div>
				<div class="entry">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
        <?php get_sidebar(); ?>
    </div>

<?php get_footer(); ?>