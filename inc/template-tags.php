<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Casper
 */

if ( ! function_exists( 'casper_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @return void
 */
function casper_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	global $wp_query;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	?>
	<nav class="pagination navigation paging-navigation" role="navigation">
		<div class="nav-links">
			<?php if ( get_next_posts_link() ) : ?>
			<div class="older-posts"><?php next_posts_link( __( 'Older Posts <span class="meta-nav">&rarr;</span>', 'casper' ) ); ?></div>
			<?php endif; ?>
				<div class="page-number"><?php  echo 'Page '.$paged.' of '.$wp_query->max_num_pages; ?></div>
			<?php if ( get_previous_posts_link() ) : ?>
			<div class="newer-posts"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span> Newer Posts', 'casper' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'casper_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @return void
 */
function casper_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	global $wp_query;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	?>
	<nav class="pagination navigation post-navigation" role="navigation">
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="older-posts">%link</div>', _x( '%title <span class="meta-nav">&rarr;</span>', 'Previous post link', 'casper' ) ); ?>
				<div class="page-number"></div>
				<?php next_post_link(     '<div class="newer-posts">%link</div>',     _x( '<span class="meta-nav">&larr;</span> %title', 'Next post link',     'casper' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'casper_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function casper_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		//$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	printf( __( '<span class="posted-on">%1$s</span><span class="byline"> by %2$s</span>', 'casper' ),
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		)
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 */
function casper_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so casper_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so casper_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in casper_categorized_blog.
 */
function casper_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'casper_category_transient_flusher' );
add_action( 'save_post',     'casper_category_transient_flusher' );


if ( ! function_exists( 'custom_comments' ) ) :
function custom_comments( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
    ?>
    <div class="post pingback">
        <p><?php _e( 'Pingback:', 'shape' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'shape' ), ' ' ); ?></p>
    <?php
            break;
        default :
    ?>
    <div <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment">
            <footer>
                <div class="comment-author vcard">
                	<span class="avatar">
                    	<?php echo get_avatar( $comment, 80 ); ?>
                    </span>
                    <span class="meta">
                    	<?php printf( __( '%s', 'shape' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
                    	<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
	                    <?php
	                        /* translators: 1: date, 2: time */
	                        printf( __( '%1$s at %2$s', 'shape' ), get_comment_date(), get_comment_time() ); ?>
	                    </time></a>
	                    <?php edit_comment_link( __( '(Edit)', 'shape' ), ' ' );
	                    ?>
                    </span>
                </div><!-- .comment-author .vcard -->
                <?php if ( $comment->comment_approved == '0' ) : ?>
                    <em><?php _e( 'Your comment is awaiting moderation.', 'shape' ); ?></em>
                    <br />
                <?php endif; ?>
            </footer>

            <div class="comment-content"><?php comment_text(); ?></div>

            <div class="reply">
                <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            </div><!-- .reply -->
        </article><!-- #comment-## -->

    <?php
            break;
    endswitch;
}
endif; // ends check for shape_comment()