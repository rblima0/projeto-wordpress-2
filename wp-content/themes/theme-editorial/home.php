<?php

get_header(); ?>

    <!-- Banner -->
    <section id="banner">
        <div class="content">
            <header>
                <h1>Hi, I’m Editorial<br />
                    by wpdev</h1>
                <p>A free and fully responsive site template</p>
            </header>
            <p>Aenean ornare velit lacus, ac varius enim ullamcorper eu. Proin aliquam facilisis ante interdum congue. Integer mollis, nisl amet convallis, porttitor magna ullamcorper, amet egestas mauris. Ut magna finibus nisi nec lacinia. Nam maximus erat id euismod egestas. Pellentesque sapien ac quam. Lorem ipsum dolor sit nullam.</p>
            <ul class="actions">
                <li><a href="#" class="button big">Learn More</a></li>
            </ul>
        </div>
        <span class="image object">
            <img src="<?php echo THEME_URL; ?>images/pic10.jpg" alt="" />
        </span>
    </section>

    <!-- Section -->
    <section>
        <header class="major">
            <h2>Erat lacinia</h2>
        </header>
        <div class="features">
            <article>
                <span class="icon fa-diamond"></span>
                <div class="content">
                    <h3>Portitor ullamcorper</h3>
                    <p>Aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore. Proin aliquam facilisis ante interdum. Sed nulla amet lorem feugiat tempus aliquam.</p>
                </div>
            </article>
            <article>
                <span class="icon fa-paper-plane"></span>
                <div class="content">
                    <h3>Sapien veroeros</h3>
                    <p>Aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore. Proin aliquam facilisis ante interdum. Sed nulla amet lorem feugiat tempus aliquam.</p>
                </div>
            </article>
            <article>
                <span class="icon fa-rocket"></span>
                <div class="content">
                    <h3>Quam lorem ipsum</h3>
                    <p>Aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore. Proin aliquam facilisis ante interdum. Sed nulla amet lorem feugiat tempus aliquam.</p>
                </div>
            </article>
            <article>
                <span class="icon fa-signal"></span>
                <div class="content">
                    <h3>Sed magna finibus</h3>
                    <p>Aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore. Proin aliquam facilisis ante interdum. Sed nulla amet lorem feugiat tempus aliquam.</p>
                </div>
            </article>
        </div>
    </section>

    <?php if ( $screen->have_items() ) { ?>
    <!-- Section -->
    <section>
        <header class="major">
            <h2>Ipsum sed dolor</h2>
        </header>
        <div class="posts">
            <!-- TESTANDO OPÇÃO -->
            <!-- <?php
            for ($i=0; $i < 4 ; $i++) { 
                get_template_part('partial/post');
            } ?> -->

            <?php
            while ( $screen->the_item() ) {
                get_template_part( 'partial/post' );
            } ?>
        </div>
    </section>
    <?php } ?>

    <?php get_sidebar(); ?>

<?php get_footer(); ?>