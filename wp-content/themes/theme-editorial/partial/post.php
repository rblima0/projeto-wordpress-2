<?php global $item; ?>

    <article>
        <a href="<?php echo $item->permalink; ?>" class="image">
            <?php echo $item->thumb; ?>
        </a>
        <h3><?php echo $item->post_title; ?></h3>
        <p><?php echo $item->post_excerpt; ?></p>
        <ul class="actions">
            <li><a href="<?php echo $item->permalink; ?>" class="button">Veja mais</a></li>
        </ul>
    </article>