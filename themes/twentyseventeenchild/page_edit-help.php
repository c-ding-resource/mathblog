<?php
/**
 * The template for edit help page
 */

$post_id=$_GET['id'];
get_header(); ?>
<div class="wrap">
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <header class="page-header">
            <h1 class="page-title">Writing Sample</h1>
        </header><!-- .page-header -->
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <p>Here is a writing sample, click the icon <i class="fa fa-eye"></i> in the editor toolbar for quick preview.</p>
                <form action="<?php echo admin_url('admin-ajax.php')?>" method="post" id="edit-form">
                    <p class="content-section"><label></label>
                        <textarea name="content" placeholder='Enter your content here' ><?php while ( have_posts() ) :the_post(); echo get_the_content();endwhile;?></textarea>
                    </p>
                    <input type="hidden"  name="citations" value='@article{einstein,
    author =       "Albert Einstein",
    title =        "{Zur Elektrodynamik bewegter K{\"o}rper}. ({German})
        [{On} the electrodynamics of moving bodies]",
    journal =      "Annalen der Physik",
    volume =       "322",
    number =       "10",
    pages =        "891--921",
    year =         "1905",
    DOI =          "http://dx.doi.org/10.1002/andp.19053221004"
}'/>
                </form>
            </div><!-- .entry-content -->
        </article><!-- #post-<?php the_ID(); ?> -->
    </main><!-- .site-main -->
</div><!-- .content-area -->
</div>

<?php get_footer(); ?>