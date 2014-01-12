<?php 
if ( $title ) {
    echo $before_title . $title . $after_title;
}
?>
<ul>
    <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
        <li>
            <a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
        </li>
    <?php endwhile; ?>
</ul>
<?php wp_reset_postdata(); ?>