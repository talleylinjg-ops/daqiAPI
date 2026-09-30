<?php
/**
 * Template Name: TOKEN Landing
 *
 * Custom landing page for TOKEN top-up products.
 * Design inspired by the standalone TOKEN store page.
 * Buy buttons link to WooCommerce add-to-cart.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Top Up TOKEN Credits | DaqiToken - Unified LLM Gateway</title>
<meta name="description" content="Buy TOKEN credits for the unified LLM gateway. Simple prepaid packages, instant credit after payment. Access OpenAI, Anthropic, and more through one API.">
<link rel="canonical" href="<?php echo esc_url(home_url()); ?>/token/">
<meta property="og:type" content="website">
<meta property="og:title" content="Top Up TOKEN Credits | DaqiToken - Unified LLM Gateway">
<meta property="og:description" content="Buy TOKEN credits for the unified LLM gateway. Simple prepaid packages, instant credit after payment. Access OpenAI, Anthropic, and more through one API.">
<meta property="og:url" content="<?php echo esc_url(home_url()); ?>/token/">
<meta property="og:site_name" content="DaqiToken">
<meta property="og:image" content="<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/og-share.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Top Up TOKEN Credits | DaqiToken - Unified LLM Gateway">
<meta name="twitter:description" content="Buy TOKEN credits for the unified LLM gateway. Simple prepaid packages, instant credit after payment. Access OpenAI, Anthropic, and more through one API.">
<meta name="twitter:image" content="<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/og-share.png">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "<?php echo esc_url(home_url()); ?>/token/#webpage",
      "url": "<?php echo esc_url(home_url()); ?>/token/",
      "name": "Top Up TOKEN Credits | DaqiToken - Unified LLM Gateway",
      "description": "Buy TOKEN credits for the unified LLM gateway. Simple prepaid packages, instant credit after payment. Access OpenAI, Anthropic, and more through one API.",
      "isPartOf": { "@id": "<?php echo esc_url(home_url()); ?>/#website" },
      "about": { "@id": "<?php echo esc_url(home_url()); ?>/#organization" }
    },
    {
      "@type": "WebSite",
      "@id": "<?php echo esc_url(home_url()); ?>/#website",
      "url": "<?php echo esc_url(home_url()); ?>/",
      "name": "DaqiToken"
    },
    {
      "@type": "Organization",
      "@id": "<?php echo esc_url(home_url()); ?>/#organization",
      "name": "DaqiToken",
      "alternateName": "Daqi",
      "url": "<?php echo esc_url(home_url()); ?>/",
      "logo": {
        "@type": "ImageObject",
        "url": "<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/daqitoken-icon.png",
        "width": 512,
        "height": 512
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer support",
        "email": "support@daqitoken.com",
        "url": "<?php echo esc_url(home_url()); ?>/contact/",
        "availableLanguage": "English"
      },
      "sameAs": [
        "<?php echo esc_url(home_url()); ?>/forum/",
        "<?php echo esc_url(home_url()); ?>/blog/"
      ]
    },
    {
      "@type": "FAQPage",
      "@id": "<?php echo esc_url(home_url()); ?>/token/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "What is a TOKEN credit?",
          "acceptedAnswer": { "@type": "Answer", "text": "Credits are prepaid balance on your gateway account. Every API request consumes credits based on the model's pricing. 1 USD equals 500,000 credits." }
        },
        {
          "@type": "Question",
          "name": "How fast will my account be credited?",
          "acceptedAnswer": { "@type": "Answer", "text": "Automatically, usually within 1-2 minutes of payment confirmation. No manual processing needed." }
        },
        {
          "@type": "Question",
          "name": "Do credits expire?",
          "acceptedAnswer": { "@type": "Answer", "text": "No. Your balance never expires. Use it whenever you need." }
        },
        {
          "@type": "Question",
          "name": "Which payment methods are accepted?",
          "acceptedAnswer": { "@type": "Answer", "text": "All major credit/debit cards and PayPal through our secure checkout." }
        },
        {
          "@type": "Question",
          "name": "What if I entered the wrong account?",
          "acceptedAnswer": { "@type": "Answer", "text": "Credits are tied to your logged-in DaqiToken account at checkout, so they always land in the right place. Contact support if you need help." }
        },
        {
          "@type": "Question",
          "name": "Can I get a refund?",
          "acceptedAnswer": { "@type": "Answer", "text": "Yes, within 14 days if your credits remain unused. Refunds are processed back to the original payment method." }
        }
      ]
    }
  ]
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #0b1020;
    --bg-soft: #121a33;
    --card: #151e3a;
    --border: #243052;
    --text: #e8ecf8;
    --muted: #93a0c4;
    --accent: #4f7cff;
    --accent-hover: #3a66e8;
    --green: #2bd97c;
    --radius: 16px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html { scroll-behavior: smooth; }
  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
  }
  .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

  header {
    padding: 18px 0;
    border-bottom: 1px solid var(--border);
    background: rgba(11,16,32,0.85);
    position: sticky;
    top: 0;
    z-index: 10;
    backdrop-filter: blur(8px);
  }
  .header-inner { display: flex; align-items: center; justify-content: space-between; }
  .brand { font-weight: 800; font-size: 18px; letter-spacing: 0.3px; color: var(--text); text-decoration: none; }
  .brand span { color: var(--accent); }
  .nav-links a {
    color: var(--muted);
    text-decoration: none;
    margin-left: 24px;
    font-size: 14px;
    font-weight: 500;
    transition: color 0.2s;
  }
  .nav-links a:hover { color: var(--text); }

  .model-strip {
    padding: 20px 0;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, rgba(79,124,255,0.06), transparent);
  }
  .model-logos {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
  }
  .model-logo {
    padding: 6px;
    text-decoration: none;
    transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s;
    display: inline-block;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }
  .model-logo:hover {
    opacity: .9;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.14);
  }
  .model-logo-img {
    width: 44px;
    height: 44px;
    display: block;
    border-radius: 8px;
  }
  @media (max-width: 640px) {
    .model-logos { gap: 10px; }
    .model-logo { padding: 4px; border-radius: 12px; }
    .model-logo-img { width: 36px; height: 36px; border-radius: 6px; }
  }

  .hero {
    padding: 84px 0 60px;
    text-align: center;
    background:
      radial-gradient(800px 400px at 50% -10%, rgba(79,124,255,0.18), transparent 70%);
  }
  .hero h1 {
    font-size: 44px;
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1.15;
    margin-bottom: 18px;
  }
  .hero h1 em { font-style: normal; color: var(--accent); }
  .hero p {
    color: var(--muted);
    font-size: 18px;
    max-width: 640px;
    margin: 0 auto 12px;
  }
  .hero .sub { font-size: 15px; color: var(--muted); opacity: 0.8; }
  .hero .discount {
    font-style: normal;
    color: #ffd166;
    font-weight: 800;
    background: rgba(255, 209, 102, 0.15);
    padding: 2px 10px;
    border-radius: 8px;
  }

  .trust-strip {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    margin-top: 32px;
  }
  .trust-strip span {
    font-size: 13px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .trust-strip .dot { color: var(--green); font-size: 16px; }

  .plans { padding: 60px 0; }
  .section-title {
    text-align: center;
    font-size: 30px;
    font-weight: 800;
    margin-bottom: 40px;
    letter-spacing: -0.5px;
  }
  .plan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    align-items: stretch;
  }
  .plan {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 32px 28px;
    display: flex;
    flex-direction: column;
    position: relative;
    transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
  }
  .plan:hover {
    transform: translateY(-4px);
    border-color: rgba(79,124,255,0.5);
    box-shadow: 0 16px 40px rgba(0,0,0,0.35);
  }
  .plan.popular { border-color: var(--accent); }
  .plan .badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--accent);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 20px;
    letter-spacing: 0.3px;
  }
  .plan .amount { font-size: 40px; font-weight: 800; letter-spacing: -1px; }
  .plan .amount small { font-size: 18px; font-weight: 600; color: var(--muted); }
  .plan .desc { color: var(--muted); font-size: 14px; margin: 8px 0 20px; }
  .plan ul { list-style: none; margin-bottom: 28px; flex: 1; }
  .plan ul li {
    font-size: 14px;
    color: var(--text);
    padding: 6px 0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
  }
  .plan ul li::before { content: "✓"; color: var(--green); font-weight: 700; }
  .buy-btn {
    display: block;
    width: 100%;
    text-align: center;
    background: var(--accent);
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    padding: 14px 20px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
  }
  .buy-btn:hover { background: var(--accent-hover); color: #fff; }

  .how-it-works {
    padding: 60px 0;
    background: var(--bg-soft);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
  }
  .steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 28px;
  }
  .step { text-align: center; }
  .step .num {
    width: 44px;
    height: 44px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: rgba(79,124,255,0.15);
    color: var(--accent);
    font-weight: 800;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .step h3 { font-size: 16px; margin-bottom: 8px; }
  .step p { color: var(--muted); font-size: 14px; }

  .account-box {
    margin-top: 40px;
    max-width: 560px;
    margin-left: auto;
    margin-right: auto;
    background: var(--card);
    border: 1px dashed var(--accent);
    border-radius: var(--radius);
    padding: 24px;
  }
  .account-box h4 { font-size: 15px; margin-bottom: 12px; }
  .account-box p { font-size: 13px; color: var(--muted); margin-bottom: 14px; }
  .account-box a { color: var(--accent); }

  .faq { padding: 60px 0; }
  .faq-list { max-width: 760px; margin: 0 auto; }
  .faq-item {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    margin-bottom: 14px;
    overflow: hidden;
  }
  .faq-q {
    padding: 18px 22px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
  }
  .faq-q::after { content: "+"; font-size: 20px; color: var(--accent); }
  .faq-item.open .faq-q::after { content: "–"; }
  .faq-a {
    display: none;
    padding: 0 22px 18px;
    color: var(--muted);
    font-size: 14px;
  }
  .faq-item.open .faq-a { display: block; }

  footer {
    padding: 36px 0;
    border-top: 1px solid var(--border);
    color: var(--muted);
    font-size: 13px;
    text-align: center;
  }
  footer a { color: var(--muted); }

  @media (max-width: 640px) {
    .hero h1 { font-size: 32px; }
    .nav-links { display: none; }
  }
</style>
</head>
<body>

<header>
  <div class="container header-inner">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">Daqi<span>Token</span></a>
    <nav class="nav-links">
      <a href="#plans">Pricing</a>
      <a href="#how-it-works">How it works</a>
      <a href="#faq">FAQ</a>
      <a href="<?php echo esc_url(home_url('/shop/')); ?>">Shop</a>
    </nav>
  </div>
</header>

<section class="model-strip">
  <div class="container">
    <div class="model-logos">
      <?php $token_link = esc_url(add_query_arg('product_cat', 'token', home_url('/shop/')));
      $models = array(
        array('DeepSeek', 'deepseek.svg'), array('Kimi', 'moonshot.png'), array('Qwen', 'qwen.svg'), array('GLM', 'glm.png'), array('Doubao', 'doubao.png'),
        array('OpenAI', 'openai.svg'), array('Anthropic', 'anthropic.svg'), array('Google', 'google.svg'), array('Meta', 'meta.svg'), array('xAI', 'xai.svg'),
      );
      $mbase = get_stylesheet_directory_uri() . '/assets/models/';
      foreach ($models as $m): ?>
        <a class="model-logo" href="<?php echo $token_link; ?>" title="Buy TOKEN credits for <?php echo esc_attr($m[0]); ?>"><img src="<?php echo esc_url($mbase . $m[1]); ?>" alt="<?php echo esc_attr($m[0]); ?>" class="model-logo-img"></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="hero">
  <div class="container">
    <h1>Top up TOKEN credits<br>for the <em>Unified LLM Gateway</em></h1>
    <p>DaqiToken API key to access DeepSeek, Kimi, OpenAI, Anthropic, Google, Meta and 100+ models. Prepaid credits, instant delivery, no subscription. Max. <em class="discount">50% discount</em>.</p>
    <p class="sub">Pay once, use credits. Balance never expires.</p>
    <div class="trust-strip">
      <span><span class="dot">●</span> Instant top-up after payment</span>
      <span><span class="dot">●</span> Secure checkout</span>
      <span><span class="dot">●</span> Balance never expires</span>
      <span><span class="dot">●</span> Worldwide card &amp; PayPal</span>
    </div>
  </div>
</section>

<section class="plans" id="plans">
  <div class="container">
    <h2 class="section-title">Choose your package</h2>
    <div class="plan-grid">
      <div class="plan">
        <div class="amount"><small>$</small>10</div>
        <p class="desc">For trying out the gateway and light usage.</p>
        <ul>
          <li>5,000,000 credits</li>
          <li>Access to all models</li>
          <li>Instant credit after payment</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('add-to-cart', '27', home_url('/cart/'))); ?>">Buy $10 package</a>
      </div>

      <div class="plan popular">
        <span class="badge">Most popular</span>
        <div class="amount"><small>$</small>50</div>
        <p class="desc">Best value for regular production workloads.</p>
        <ul>
          <li>25,000,000 credits</li>
          <li>Access to all models</li>
          <li>Instant credit after payment</li>
          <li>Priority support</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('add-to-cart', '28', home_url('/cart/'))); ?>">Buy $50 package</a>
      </div>

      <div class="plan">
        <div class="amount"><small>$</small>100</div>
        <p class="desc">For teams and heavy production usage.</p>
        <ul>
          <li>50,000,000 credits</li>
          <li>Access to all models</li>
          <li>Instant credit after payment</li>
          <li>Priority support</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('add-to-cart', '29', home_url('/cart/'))); ?>">Buy $100 package</a>
      </div>
    </div>
  </div>
</section>

<section class="how-it-works" id="how-it-works">
  <div class="container">
    <h2 class="section-title">How it works</h2>
    <div class="steps">
      <div class="step">
        <div class="num">1</div>
        <h3>Create your account</h3>
        <p>Sign up on DaqiToken and get access to your gateway dashboard.</p>
      </div>
      <div class="step">
        <div class="num">2</div>
        <h3>Choose a package</h3>
        <p>Select a prepaid package above and complete secure checkout.</p>
      </div>
      <div class="step">
        <div class="num">3</div>
        <h3>Get instant credits</h3>
        <p>Your account is credited automatically within minutes of payment.</p>
      </div>
    </div>

    <div class="account-box">
      <h4>Already have an account?</h4>
      <p>Your API key and balance are shown in your <a href="<?php echo esc_url(home_url('/my-account/')); ?>">account dashboard</a>. Credits are tied to your DaqiToken account automatically.</p>
      <p>No account yet? <a href="<?php echo esc_url(home_url('/my-account/')); ?>">Create one free</a> — you get starter credits to explore.</p>
    </div>
  </div>
</section>

<section class="faq" id="faq">
  <div class="container">
    <h2 class="section-title">FAQ</h2>
    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-q">What is a TOKEN credit?</div>
        <div class="faq-a">Credits are prepaid balance on your gateway account. Every API request consumes credits based on the model's pricing. 1 USD equals 500,000 credits.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">How fast will my account be credited?</div>
        <div class="faq-a">Automatically, usually within 1-2 minutes of payment confirmation. No manual processing needed.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Do credits expire?</div>
        <div class="faq-a">No. Your balance never expires. Use it whenever you need.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Which payment methods are accepted?</div>
        <div class="faq-a">All major credit/debit cards and PayPal through our secure checkout.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">What if I entered the wrong account?</div>
        <div class="faq-a">Credits are tied to your logged-in DaqiToken account at checkout, so they always land in the right place. Contact support if you need help.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Can I get a refund?</div>
        <div class="faq-a">Yes, within 14 days if your credits remain unused. Refunds are processed back to the original payment method.</div>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="container">
    <p>DaqiToken Store &#8212; Unified LLM Gateway. <a href="<?php echo esc_url(home_url('/about/')); ?>">About</a> · <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></p>
  </div>
</footer>

<script>
  document.querySelectorAll('.faq-q').forEach(function (q) {
    q.addEventListener('click', function () {
      q.parentElement.classList.toggle('open');
    });
  });
</script>
</body>
</html>
