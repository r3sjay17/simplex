<?php
/*
Template Name: Simplex Login Cover
*/
?>

<?php get_header(); ?>

    <div id="content">

        <div class="row expanded cover-login-page" >

            <main id="main" class="large-12 medium-12 " role="main">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <?php get_template_part( 'parts/loop', 'login-cover' ); ?>

                <?php endwhile; endif; ?>

            </main> <!-- end #main -->

        </div> <!-- end #inner-content -->

    </div> <!-- end #content -->

<?php get_footer(); ?>
