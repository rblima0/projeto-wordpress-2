<?php get_header(); ?>

	<div id="page">
		<div id="content">
            <?php the_post(); ?>
			<div class="post">
				<h2 class="title">
                    <?php the_title(); ?>
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
		</div>
        <?php get_sidebar(); ?>
    </div>

<?php get_footer(); ?>