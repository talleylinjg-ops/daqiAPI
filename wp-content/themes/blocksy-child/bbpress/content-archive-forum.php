<?php
defined( 'ABSPATH' ) || exit;

$latest_topics = new WP_Query( array(
	'post_type'      => 'topic',
	'post_status'    => 'publish',
	'posts_per_page' => 5,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
) );
?>

<div id="bbpress-forums" class="bbpress-wrapper">

	<?php if ( $latest_topics->have_posts() ) : ?>
		<section class="latest-topics">
			<h2 class="latest-topics-title"><?php esc_html_e( 'Latest Topics', 'blocksy-child' ); ?></h2>
			<ul class="latest-topics-list">
				<?php while ( $latest_topics->have_posts() ) : $latest_topics->the_post(); ?>
					<li class="latest-topic-item">
						<span class="latest-topic-date"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></span>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php bbp_get_template_part( 'form', 'search' ); ?>

	<?php bbp_breadcrumb(); ?>

	<?php bbp_forum_subscription_link(); ?>

	<?php do_action( 'bbp_template_before_forums_index' ); ?>

	<?php if ( bbp_has_forums() ) : ?>

		<?php bbp_get_template_part( 'loop',     'forums'    ); ?>

	<?php else : ?>

		<?php bbp_get_template_part( 'feedback', 'no-forums' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_forums_index' ); ?>

</div>
