<?php
/**
 * Template for displaying all single posts - NovaAPI-style
 *
 * Only applies to blog posts (post type `post`). All other single views
 * (pages, products, forums, etc.) fall back to the Blocksy parent theme
 * rendering so the blog-only share/CTA blocks never leak into them.
 */

if (! is_singular('post')) {
	get_header();

	if (
		! function_exists('elementor_theme_do_location')
		||
		! elementor_theme_do_location('single')
	) {
		get_template_part('template-parts/single');
	}

	get_footer();
	return;
}

get_header();

if (have_posts()) {
	the_post();
}

$post_id = get_the_ID();
$cover = get_the_post_thumbnail_url($post_id, 'full');
$read_time = get_post_meta($post_id, '_read_time', true) ?: '';
$categories = get_the_category();
$cat_title = !empty($categories) ? $categories[0]->name : 'Blog';
$tags = get_the_tags();
$date = get_the_date('Y/m/d');
?>

<div class="ct-container-full" data-content="normal" data-vertical-spacing="top:bottom">
	<article id="post-<?php the_ID(); ?>" <?php post_class('nova-post-wrap'); ?>>

		<nav class="breadcrumb" aria-label="Breadcrumb">
			<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
			<span>/</span>
			<a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a>
			<span>/</span>
			<span class="crumb-current"><?php echo esc_html(get_the_title()); ?></span>
		</nav>

		<div class="post-wrap">
			<?php if ($cover): ?>
				<div class="post-cover">
					<img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
				</div>
			<?php endif; ?>

			<h1 class="post-title"><?php echo esc_html(get_the_title()); ?></h1>

			<div class="blog-meta post-meta">
				<span><?php echo esc_html($date); ?></span>
				<span>·</span>
				<?php if ($read_time): ?>
					<span><?php echo esc_html($read_time); ?> min read</span>
					<span>·</span>
				<?php endif; ?>
				<?php if ($tags): foreach ($tags as $t): ?>
					<span class="blog-tag"><?php echo esc_html($t->name); ?></span>
				<?php endforeach; endif; ?>
			</div>

			<div class="post-body">
				<?php the_content(); ?>
			</div>

			<!-- Share buttons -->
			<div class="share-wrap">
				<span class="share-label">Share this post:</span>
				<div class="share-btns">
					<?php
					$share_url = get_permalink();
					$share_title = rawurlencode(get_the_title());
					$share_enc = rawurlencode($share_url);
					$desc = rawurlencode(wp_strip_all_tags(get_the_excerpt()));
					$channels = array(
						array('whatsapp', 'WhatsApp', 'whatsapp', "https://wa.me/?text={$share_title}%20{$share_enc}"),
						array('facebook', 'Facebook', 'facebook', "https://www.facebook.com/sharer/sharer.php?u={$share_enc}"),
						array('x', 'X', 'x', "https://twitter.com/intent/tweet?url={$share_enc}&text={$share_title}"),
						array('telegram', 'Telegram', 'telegram', "https://t.me/share/url?url={$share_enc}&text={$share_title}"),
						array('instagram', 'Instagram', 'instagram', ''),
						array('tiktok', 'TikTok', 'tiktok', ''),
						array('pinterest', 'Pinterest', 'pinterest', "https://pinterest.com/pin/create/button/?url={$share_enc}&description={$desc}"),
						array('youtube', 'YouTube', 'youtube', 'https://www.youtube.com/@daqigroup'),
						array('line', 'Line', 'line', "https://social-plugins.line.me/lineit/share?url={$share_enc}&text={$share_title}"),
						array('kakaotalk', 'KakaoTalk', 'kakaotalk', "https://story.kakao.com/share?url={$share_enc}", true),
						array('vk', 'VK', 'vk', "https://vk.com/share.php?url={$share_enc}&title={$share_title}"),
						array('ok', 'OK', 'ok', "https://connect.ok.ru/offer?url={$share_enc}&title={$share_title}"),
						array('naver', 'Naver', 'naver', "https://share.naver.com/web/shareView?url={$share_enc}&title={$share_title}"),
						array('zalo', 'Zalo', 'zalo', ''),
						array('mixi', 'Mixi', 'mixi', ''),
						array('linkedin', 'LinkedIn', 'linkedin', "https://www.linkedin.com/sharing/share-offsite/?url={$share_enc}"),
						array('reddit', 'Reddit', 'reddit', "https://www.reddit.com/submit?url={$share_enc}&title={$share_title}"),
						array('tumblr', 'Tumblr', 'tumblr', "https://www.tumblr.com/widgets/share/tool?canonicalUrl={$share_enc}&posttype=link&title={$share_title}&caption={$desc}"),
						array('snapchat', 'Snapchat', 'snapchat', '', true),
						array('email', 'Email', 'email', "mailto:?subject={$share_title}&body={$share_title}%20{$share_enc}"),
						array('copylink', 'Copy link', 'copylink', '', false, true),
					);
					$slogofile = array('email' => 'gmail');
					$slogobase = get_stylesheet_directory_uri() . '/assets/social/';
					foreach ($channels as $ch):
						$dark = !empty($ch[4]) ? ' dark' : '';
						$copy = ($ch[3] === '' || !empty($ch[5])) ? 'data-copy="1"' : 'data-href="' . esc_url($ch[3]) . '"';
						$fname = isset($slogofile[$ch[0]]) ? $slogofile[$ch[0]] : $ch[0];
						$img = $slogobase . $fname . '.svg';
						?>
						<button class="share-btn<?php echo esc_attr($dark); ?>" data-share="<?php echo esc_attr($ch[0]); ?>" <?php echo $copy; ?> title="<?php echo esc_attr($ch[1]); ?>"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($ch[1]); ?>" loading="lazy"></button>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Multilingual comment CTA -->
			<section class="cta-card">
				<span class="cta-title">Share your thoughts</span>
				<div class="cta-langs">
					<?php
					$langs = array(
						'en' => 'English', 'es' => 'Español', 'zh' => '中文', 'de' => 'Deutsch',
						'fr' => 'Français', 'pt' => 'Português', 'nl' => 'Nederlands', 'pl' => 'Polski',
						'ru' => 'Русский', 'ar' => 'العربية', 'ja' => '日本語', 'ko' => '한국어',
						'th' => 'ไทย', 'he' => 'עברית', 'vi' => 'Tiếng Việt', 'id' => 'Indonesia', 'kk' => 'Қазақша',
					);
					$cta_text = array(
						'en' => array('Did this post help you? Leave a comment below — we read every single one and reply to as many as we can.', 'Comments are moderated and published after a quick review.'),
						'es' => array('¿Te ayudó esta publicación? Deja un comentario abajo: leemos todos y respondemos a la mayoría.', 'Los comentarios se moderan y se publican tras una revisión rápida.'),
						'zh' => array('这篇文章对您有帮助吗？请在下方留言——我们会阅读每一条评论并尽可能回复。', '评论需经过审核后才会显示。'),
						'de' => array('Hat dir dieser Beitrag geholfen? Hinterlasse unten einen Kommentar – wir lesen jeden einzelnen und antworten so vielen wie möglich.', 'Kommentare werden moderiert und nach einer kurzen Prüfung veröffentlicht.'),
						'fr' => array('Cet article vous a-t-il aidé ? Laissez un commentaire ci-dessous – nous les lisons tous et répondons au plus grand nombre.', 'Les commentaires sont modérés et publiés après une vérification rapide.'),
						'pt' => array('Este artigo ajudou você? Deixe um comentário abaixo – lemos todos e respondemos à maioria.', 'Os comentários são moderados e publicados após uma revisão rápida.'),
						'nl' => array('Heeft dit bericht je geholpen? Laat hieronder een reactie achter – we lezen ze allemaal en beantwoorden zoveel mogelijk.', 'Reacties worden gemodereerd en na een korte controle gepubliceerd.'),
						'pl' => array('Czy ten post Ci pomógł? Zostaw komentarz poniżej – czytamy każdy i odpowiadamy na tyle, na ile możemy.', 'Komentarze są moderowane i publikowane po szybkiej weryfikacji.'),
						'ru' => array('Эта статья помогла вам? Оставьте комментарий ниже — мы читаем каждый и отвечаем на большинство.', 'Комментарии модерируются и публикуются после быстрой проверки.'),
						'ar' => array('هل ساعدك هذا المقال؟ اترك تعليقًا أدناه — نقرأ كل تعليق ونرد على أكبر عدد ممكن.', 'تتم مراجعة التعليقات وتنشر بعد التحقق السريع.'),
						'ja' => array('この記事は役に立ちましたか？下のコメント欄にぜひ投稿してください。すべて読んで、できる限り返信します。', 'コメントはモデレーション後に公開されます。'),
						'ko' => array('이 글이 도움이 되었나요? 아래에 댓글을 남겨주세요 — 모든 댓글을 읽고 최대한 많이 답변합니다.', '댓글은 검토 후 게시됩니다.'),
						'th' => array('บทความนี้ช่วยคุณได้ไหม? แสดงความคิดเห็นด้านล่าง — เราอ่านทุกความเห็นและตอบกลับให้มากที่สุด', 'ความคิดเห็นจะถูกตรวจสอบและเผยแพร่หลังจากการพิจารณา'),
						'he' => array('האם המאמר עזר לכם? השאירו תגובה למטה — אנחנו קוראים כל תגובה ועונים לרובן.', 'תגובות עוברות ניהול ומתפרסמות לאחר בדיקה מהירה.'),
						'vi' => array('Bài viết này có giúp bạn không? Hãy để lại bình luận bên dưới — chúng tôi đọc từng bình luận và phản hồi nhiều nhất có thể.', 'Bình luận được kiểm duyệt và xuất hiện sau khi xét duyệt nhanh.'),
						'id' => array('Apakah artikel ini membantu Anda? Tinggalkan komentar di bawah — kami membaca semuanya dan membalas sebanyak mungkin.', 'Komentar dimoderasi dan dipublikasikan setelah pemeriksaan cepat.'),
						'kk' => array('Бұл мақала сізге көмектесті ме? Төменде пікір қалдырыңыз — біз әрқайсысын оқып, мүмкіндігінше көбіне жауап береміз.', 'Пікірлер модерациядан өтіп, тексерілгеннен кейін жарияланады.'),
					);
					foreach ($langs as $code => $label):
						$active = $code === 'en' ? ' active' : '';
						?>
						<button class="cta-lang<?php echo esc_attr($active); ?>" data-lang="<?php echo esc_attr($code); ?>"><?php echo esc_html($label); ?></button>
					<?php endforeach; ?>
				</div>
				<div class="cta-all">
					<?php foreach ($langs as $code => $label):
						$active = $code === 'en' ? ' row-active' : '';
						$dir = ($code === 'ar' || $code === 'he') ? 'rtl' : 'ltr';
						?>
						<div class="cta-row<?php echo esc_attr($active); ?>" data-lang-row="<?php echo esc_attr($code); ?>" dir="<?php echo esc_attr($dir); ?>">
							<div class="cta-lang-body">
								<p class="cta-text"><?php echo esc_html($cta_text[$code][0]); ?></p>
								<p class="cta-hint"><?php echo esc_html($cta_text[$code][1]); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</section>

			<!-- Comments -->
			<section id="comments" class="comments-wrap">
				<div class="comments-head">
					<h2>Comments (<?php echo get_comments_number(); ?>)</h2>
				</div>
				<div class="comment-list">
					<?php
					$comments = get_comments(array('post_id' => $post_id, 'status' => 'approve'));
					if ($comments):
						foreach ($comments as $c):
							$children = get_comments(array('post_id' => $post_id, 'status' => 'approve', 'parent' => $c->comment_ID));
							?>
							<div class="comment">
								<div class="comment-head">
									<b><?php echo esc_html($c->comment_author); ?></b>
									<span class="comment-time"><?php echo esc_html(date_i18n('Y/n/j', strtotime($c->comment_date))); ?></span>
								</div>
								<div class="comment-body"><?php echo wp_kses_post($c->comment_content); ?></div>
								<?php if ($children): foreach ($children as $child): ?>
									<div class="comment-reply">
										<b><?php echo esc_html($child->comment_author); ?></b>
										<p><?php echo wp_kses_post($child->comment_content); ?></p>
									</div>
								<?php endforeach; endif; ?>
							</div>
						<?php endforeach;
					else: ?>
						<p class="comment-empty">No comments yet. Be the first to share your thoughts!</p>
					<?php endif; ?>
				</div>

				<form class="comment-form" id="commentform" action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="post">
					<div class="form-row">
						<input class="input" aria-label="Name" name="author" type="text" placeholder="Name *" maxlength="60" required>
						<input class="input" aria-label="Email" name="email" type="email" placeholder="Email (optional)">
					</div>
					<textarea class="input textarea" aria-label="Comment" name="comment" placeholder="Your comment..." rows="4" maxlength="1000" required></textarea>
					<button class="btn-buy" type="submit" style="max-width:220px">Post comment</button>
					<input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($post_id); ?>">
					<input type="hidden" name="comment_parent" id="comment_parent" value="0">
					<?php do_action('comment_form', $post_id); ?>
				</form>
			</section>
		</div>
	</article>
</div>

<?php get_footer(); ?>
