<?php
/**
 * Home/Blog listing template - NovaAPI-style
 */

get_header();
?>

<div class="ct-container-full" data-content="normal" data-vertical-spacing="top:bottom">
	<article class="nova-blog-page">
		<div class="container">

			<section style="padding-bottom:24px">
				<h2 class="section-title" style="text-align:left;margin-bottom:4px">Blog</h2>
				<p class="section-sub" style="text-align:left">Guides, tutorials and tips for getting the most out of your API credits — plus eSIM travel guides and VPN privacy setups.</p>
			</section>

			<div class="cat-menu">
				<?php
				$order = array('getting-started', 'guides', 'models', 'news', 'cost');
				$all_cats = get_categories(array('hide_empty' => false, 'exclude' => array(1)));
				$cats = array();
				foreach ($order as $slug) {
					foreach ($all_cats as $c) {
						if ($c->slug === $slug) { $cats[] = $c; }
					}
				}
				$current_cat = get_queried_object();
				$current_slug = $current_cat && isset($current_cat->slug) ? $current_cat->slug : '';
				foreach ($cats as $c):
					$active = $current_slug === $c->slug ? ' active' : '';
					?>
					<a class="cat-btn<?php echo esc_attr($active); ?>" href="<?php echo esc_url(get_category_link($c)); ?>"><?php echo esc_html($c->name); ?></a>
				<?php endforeach; ?>
				<a class="cat-btn<?php echo $current_slug === '' ? ' active' : ''; ?>" href="<?php echo esc_url(home_url('/blog/')); ?>">All</a>
			</div>

			<div class="blog-grid">
				<?php if (have_posts()): while (have_posts()): the_post(); ?>
					<?php
					$cover = get_the_post_thumbnail_url(get_the_ID(), 'full');
					$post_cats = get_the_category();
					$post_cat = !empty($post_cats) ? $post_cats[0]->name : 'Blog';
					$post_tags = get_the_tags();
					$read_time = get_post_meta(get_the_ID(), '_read_time', true);
					?>
					<a class="blog-card" href="<?php the_permalink(); ?>">
						<div class="blog-cover">
							<?php if ($cover): ?>
								<img src="<?php echo esc_url($cover); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
							<?php endif; ?>
						</div>
						<div class="blog-meta">
							<span class="blog-cat"><?php echo esc_html($post_cat); ?></span>
							<span>·</span>
							<span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
							<?php if ($read_time): ?>
								<span>·</span>
								<span><?php echo esc_html($read_time); ?> min read</span>
							<?php endif; ?>
						</div>
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html(get_the_excerpt()); ?></p>
						<?php if ($post_tags): ?>
							<div class="blog-tags">
								<?php foreach ($post_tags as $t): ?>
									<span class="blog-tag"><?php echo esc_html($t->name); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</a>
				<?php endwhile; else: ?>
					<p class="comment-empty">No posts found.</p>
				<?php endif; ?>
			</div>

		</div>
	</article>
</div>

<?php get_footer(); ?>
