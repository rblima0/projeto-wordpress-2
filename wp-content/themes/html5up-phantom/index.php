<?php get_header(); ?>

<!-- Menu -->
<!--<nav id="menu">
    <h2>Menu</h2>
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="generic.html">Ipsum veroeros</a></li>
        <li><a href="generic.html">Tempus etiam</a></li>
        <li><a href="generic.html">Consequat dolor</a></li>
        <li><a href="elements.html">Elements</a></li>
    </ul>
</nav>-->

<!-- Main -->
<div id="main">
    <div class="inner">

        <header>
            <h1><?php bloginfo('description'); ?></h1>
            <p>Etiam quis viverra lorem, in semper lorem. Sed nisl arcu euismod sit amet nisi euismod sed cursus arcu elementum ipsum arcu vivamus quis venenatis orci lorem ipsum et magna feugiat veroeros aliquam. Lorem ipsum dolor sit amet nullam dolore.</p>
        </header>

        <?php
        if (!have_posts()){
            echo "<h2>Nenhum conteúdo encontrado...</h2>";
        } else {?>
        <section class="tiles">
            <?php
            $color = 0;
            while(have_posts()){
                the_post();
                $color++;
                if($color > 6 ) $color = 1;
                global $post;
            ?>
            <article class="style<?php echo $color; ?>">
                <span class="image">
                    <?php if(!has_post_thumbnail()){ ?>
                        <img src="<?php echo THEME_URL; ?>images/pic01.jpg" alt="" />
                    <?php } else {
                        the_post_thumbnail('thumbnail');
                    }
                    ?>

                </span>
                <a href="<?php echo get_permalink($post); ?>">
                    <h2><?php the_title(); ?></h2>
                    <div class="content">
                        <p><?php echo get_the_excerpt(); ?></p>
                    </div>
                </a>
            </article>
            <?php } ?>
        </section>

            <nav class="pagination" style="margin-top: 2em;">
                <?php
                echo paginate_links(
                    array(
                        'prev_text' => 'Mais recentes',
                        'next_text' => 'Mais antigos'
                    )
                );
                ?>
            </nav>
        <?php } ?>
    </div>
</div>

<?php get_footer(); ?>
