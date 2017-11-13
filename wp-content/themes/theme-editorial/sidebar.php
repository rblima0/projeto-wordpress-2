<?php

$sidebar = new Sidebar();
add_filter( 'the_item_screen', array( $sidebar, 'the_item_screen' ), 1, 2 );

?>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="inner">

            <!-- Search -->
            <section id="search" class="alt">
                <form method="post" action="<?php echo SITE_URL; ?>">
                    <input type="text" name="s" id="query" placeholder="Search" />
                </form>
            </section>

            <!-- Menu -->
            <nav id="menu">
                <header class="major">
                    <h2>Menu</h2>
                </header>
                <?php wp_nav_menu('menu-sidebar'); ?>
            </nav>

            <?php if ( $sidebar->have_items( 'pages' ) ) { ?>
            <!-- Section -->
            <section>
                <header class="major">
                    <h2>Páginas</h2>
                </header>
                <div class="mini-posts">
                    <?php while ( $sidebar->the_item() ) { global $item; ?>
                    <article>
                        <a href="<?php echo $item->permalink; ?>" class="image">
                            <?php echo $item->post_title; ?>
                        </a>
                        <p><?php echo $item->subtitle; ?></p>
                    </article>
                    <?php } ?>
                </div>
                <ul class="actions">
                    <li><a href="#" class="button">More</a></li>
                </ul>
            </section>
            <?php } ?>

            <?php if ( $sidebar->have_items( 'posts' ) ) { global $item; ?>
            <!-- Section -->
            <section>
                <header class="major">
                    <h2>Publicações</h2>
                </header>
                <div class="mini-posts">
                    <?php while ( $sidebar->the_item() ) { ?>
                        <article>
                            <a href="<?php echo $item->permalink; ?>" class="image">
                                <?php echo $item->thumb; ?>
                            </a>
                            <p><?php echo $item->post_title; ?></p>
                        </article>
                    <?php } ?>
                </div>
                <ul class="actions">
                    <li><a href="#" class="button">More</a></li>
                </ul>
            </section>
            <?php } ?>

            <!-- Section -->
            <section>
                <header class="major">
                    <h2>Get in touch</h2>
                </header>
                <p>Sed varius enim lorem ullamcorper dolore aliquam aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore. Proin sed aliquam facilisis ante interdum. Sed nulla amet lorem feugiat tempus aliquam.</p>
                <?php if ( $sidebar->have_items( 'info' ) ) { global $item; ?>
                <ul class="contact">
                    <?php
                    while ( $sidebar->the_item() ) {
                        global $item;
                        printf(
                            '<li class="%s">%s</li>',
                            $item->icon,
                            $item->content
                        );
                    } ?>
                </ul>
                <?php } ?>
            </section>

            <!-- Footer -->
            <footer id="footer">
                <p class="copyright">&copy; Untitled. All rights reserved. Demo Images: <a href="https://unsplash.com">Unsplash</a>. Design: <a href="https://html5up.net">HTML5 UP</a>.</p>
            </footer>

        </div>
    </div>

<?php // add_filter( 'the_item_screen', array( $screen, 'the_item_screen' ), 1, 2 ); ?>