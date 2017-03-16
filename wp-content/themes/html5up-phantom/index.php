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
            <h1>This is Phantom, a free, fully responsive site<br />
                template designed by <a href="http://html5up.net">HTML5 UP</a>.</h1>
            <p>Etiam quis viverra lorem, in semper lorem. Sed nisl arcu euismod sit amet nisi euismod sed cursus arcu elementum ipsum arcu vivamus quis venenatis orci lorem ipsum et magna feugiat veroeros aliquam. Lorem ipsum dolor sit amet nullam dolore.</p>
        </header>

        <?php
        if (!have_posts()){
            echo "<h2>Nenhum conteúdo encontrado...</h2>";
        } else {?>
        <section class="tiles">
            <?php while(have_posts()){
                the_post();
                global $post;
            ?>
            <article class="style1">
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
        <?php } ?>
    </div>
</div>

<?php get_footer(); ?>
