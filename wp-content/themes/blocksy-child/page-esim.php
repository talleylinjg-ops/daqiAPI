<?php
/**
 * Template Name: eSIM Landing
 *
 * Bright travel-themed landing page for eSIM products.
 * Buy buttons link to the WooCommerce eSIM category.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<title>eSIM for Travel | DaqiToken - Instant Global Data</title>
<meta name="description" content="Get instant mobile data in 200+ countries with our eSIM. No roaming fees, QR delivery in minutes, best price comparison across providers.">
<link rel="canonical" href="<?php echo esc_url(home_url()); ?>/esim/">
<meta property="og:type" content="website">
<meta property="og:title" content="eSIM for Travel | DaqiToken - Instant Global Data">
<meta property="og:description" content="Get instant mobile data in 200+ countries with our eSIM. No roaming fees, QR delivery in minutes, best price comparison across providers.">
<meta property="og:url" content="<?php echo esc_url(home_url()); ?>/esim/">
<meta property="og:site_name" content="DaqiToken">
<meta property="og:image" content="<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/og-share.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="eSIM for Travel | DaqiToken - Instant Global Data">
<meta name="twitter:description" content="Get instant mobile data in 200+ countries with our eSIM. No roaming fees, QR delivery in minutes, best price comparison across providers.">
<meta name="twitter:image" content="<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/og-share.png">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "<?php echo esc_url(home_url()); ?>/esim/#webpage",
      "url": "<?php echo esc_url(home_url()); ?>/esim/",
      "name": "eSIM for Travel | DaqiToken - Instant Global Data",
      "description": "Get instant mobile data in 200+ countries with our eSIM. No roaming fees, QR delivery in minutes, best price comparison across providers.",
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
      "slogan": "Digital life, connected.",
      "description": "DaqiToken is an online-only digital connectivity store selling instant travel eSIM data plans for 200+ countries, private WireGuard VPN service on dedicated nodes, and prepaid TOKEN credits for a unified OpenAI-compatible LLM API gateway.",
      "knowsAbout": [
        "Travel eSIM",
        "eSIM data plans",
        "Mobile connectivity",
        "Virtual private network",
        "WireGuard",
        "Large language model API gateway",
        "AI token credits",
        "Roaming charges"
      ],
      "areaServed": {
        "@type": "Place",
        "name": "Worldwide"
      },
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
      "@id": "<?php echo esc_url(home_url()); ?>/esim/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "How does an eSIM work?",
          "acceptedAnswer": { "@type": "Answer", "text": "An eSIM is a digital SIM built into your phone. After checkout you receive a QR code, scan it with your camera, and your data plan activates on the spot. No physical SIM card or tray required." }
        },
        {
          "@type": "Question",
          "name": "Which phones are eSIM compatible?",
          "acceptedAnswer": { "@type": "Answer", "text": "eSIM works on iPhone XS and newer, recent Google Pixel models, Samsung Galaxy S20 and newer, plus most 2021+ flagships. Check your phone settings for an Add eSIM option to confirm." }
        },
        {
          "@type": "Question",
          "name": "How fast do I receive my eSIM?",
          "acceptedAnswer": { "@type": "Answer", "text": "Your QR code is delivered by email within minutes of payment confirmation, so you can buy right before boarding and connect when you land." }
        },
        {
          "@type": "Question",
          "name": "Will I lose my current phone number?",
          "acceptedAnswer": { "@type": "Answer", "text": "No. Your normal SIM and number stay active. The travel eSIM adds a second line of data on the same device, and you keep your usual calls and texts." }
        },
        {
          "@type": "Question",
          "name": "Can I get a refund if my trip changes?",
          "acceptedAnswer": { "@type": "Answer", "text": "Yes, within 14 days of purchase if the eSIM has not been activated. Refunds are processed back to your original payment method." }
        }
      ]
    }
  ]
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
  :root {
    --sky: #e8f7f4;
    --card: #ffffff;
    --ink: #12332e;
    --muted: #5b7a74;
    --teal: #0ea5a0;
    --teal-dark: #0b8a86;
    --green: #22c55e;
    --amber: #f59e0b;
    --radius: 20px;
    --shadow: 0 8px 30px rgba(18,51,46,0.08);
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html { scroll-behavior: smooth; }
  body {
    font-family: 'Nunito', -apple-system, 'Segoe UI', sans-serif;
    background: var(--sky);
    color: var(--ink);
    line-height: 1.65;
  }
  .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

  header {
    padding: 18px 0;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(8px);
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 1px solid #d7ece7;
  }
  .header-inner { display: flex; align-items: center; justify-content: space-between; }
  .brand { font-weight: 900; font-size: 20px; letter-spacing: -0.3px; color: var(--ink); text-decoration: none; }
  .brand span { color: var(--teal); }
  .nav-links a {
    color: var(--muted);
    text-decoration: none;
    margin-left: 24px;
    font-size: 15px;
    font-weight: 700;
    transition: color 0.2s;
  }
  .nav-links a:hover { color: var(--teal); }

  .country-hero {
    padding: 32px 0 8px;
  }
  .all-countries {
    padding: 40px 0 8px;
  }
  .country-group-title {
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--teal-dark);
    margin: 0 0 14px;
  }
  .country-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  .country-grid.major {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  .country-icon {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    line-height: 1.15;
  }
  .country-icon:hover {
    transform: scale(1.06);
  }
  .country-icon .ci-flag {
    width: 44px;
    height: 33px;
    object-fit: cover;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    display: block;
  }
  .country-icon .ci-name {
    margin-top: 4px;
    font-size: 11px;
    font-weight: 600;
    color: #1a2b28;
    text-align: center;
    white-space: nowrap;
    max-width: 88px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .country-grid.all .country-icon {
    width: auto;
  }
  .country-grid.major .country-icon {
    flex-direction: row;
    line-height: 0;
  }
  .country-grid.major .country-icon .ci-name {
    display: none;
  }
  @media (max-width: 640px) {
    .country-grid { gap: 8px; }
    .country-icon .ci-flag { width: 36px; height: 27px; }
  }

  .hero {
    padding: 76px 0 56px;
    text-align: center;
    background:
      radial-gradient(700px 360px at 50% -20%, rgba(14,165,160,0.16), transparent 70%);
  }
  .hero h1 {
    font-size: 44px;
    font-weight: 900;
    letter-spacing: -1px;
    line-height: 1.15;
    margin-bottom: 16px;
  }
  .hero h1 em { font-style: normal; color: var(--teal); }
  .hero p {
    color: var(--muted);
    font-size: 18px;
    max-width: 640px;
    margin: 0 auto;
  }
  .hero .tag-row {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 24px;
  }
  .tag-row .chip {
    background: var(--card);
    border: 1px solid #d7ece7;
    border-radius: 30px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 700;
    color: var(--teal-dark);
  }
  .tag-row .chip.hot { background: var(--amber); border-color: var(--amber); color: #fff; }

  .how {
    padding: 48px 0;
  }
  .section-title {
    text-align: center;
    font-size: 30px;
    font-weight: 900;
    margin-bottom: 36px;
    letter-spacing: -0.5px;
  }
  .steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 24px;
  }
  .step {
    background: var(--card);
    border-radius: var(--radius);
    padding: 28px 24px;
    text-align: center;
    box-shadow: var(--shadow);
  }
  .step .emoji {
    font-size: 34px;
    margin-bottom: 12px;
  }
  .step h3 { font-size: 17px; margin-bottom: 8px; }
  .step p { color: var(--muted); font-size: 14px; }

  .plans { padding: 48px 0; }
  .plan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 24px;
    align-items: stretch;
  }
  .plan {
    background: var(--card);
    border: 1px solid #e0efeb;
    border-radius: var(--radius);
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .plan:hover { transform: translateY(-4px); box-shadow: 0 14px 36px rgba(18,51,46,0.14); }
  .plan .flag { font-size: 32px; margin-bottom: 8px; }
  .plan h3 { font-size: 20px; margin-bottom: 4px; }
  .plan .meta { color: var(--muted); font-size: 14px; margin-bottom: 14px; }
  .plan .amount { font-size: 32px; font-weight: 900; color: var(--teal); margin-bottom: 14px; }
  .plan .amount small { font-size: 16px; color: var(--muted); font-weight: 600; }
  .plan ul { list-style: none; margin-bottom: 22px; flex: 1; }
  .plan ul li { font-size: 14px; padding: 5px 0; display: flex; gap: 8px; }
  .plan ul li::before { content: "✓"; color: var(--green); font-weight: 800; }
  .buy-btn {
    display: block;
    width: 100%;
    text-align: center;
    background: var(--teal);
    color: #fff;
    text-decoration: none;
    font-weight: 800;
    font-size: 15px;
    padding: 13px 18px;
    border-radius: 12px;
    transition: background 0.2s;
  }
  .buy-btn:hover { background: var(--teal-dark); color: #fff; }

  .compare {
    padding: 48px 0;
    background: #dff2ed;
  }
  .compare .grid {
    max-width: 860px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .cmp-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    align-items: center;
    background: var(--card);
    border-radius: 14px;
    padding: 16px 22px;
    font-size: 15px;
    box-shadow: 0 4px 16px rgba(18,51,46,0.06);
  }
  .cmp-row .route { font-weight: 800; }
  .cmp-row .price { font-weight: 800; color: var(--teal); }
  .cmp-row .best {
    background: var(--green);
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: 20px;
  }

  .promo {
    padding: 48px 0;
    text-align: center;
  }
  .promo .big {
    font-size: 26px;
    font-weight: 900;
    margin-bottom: 10px;
  }
  .promo p { color: var(--muted); font-size: 15px; max-width: 560px; margin: 0 auto 22px; }
  .btn-primary {
    display: inline-block;
    background: var(--teal);
    color: #fff;
    text-decoration: none;
    font-weight: 800;
    font-size: 16px;
    padding: 14px 34px;
    border-radius: 40px;
    transition: background 0.2s, transform 0.2s;
  }
  .btn-primary:hover { background: var(--teal-dark); color: #fff; transform: translateY(-2px); }

  footer {
    padding: 32px 0;
    border-top: 1px solid #d7ece7;
    color: var(--muted);
    font-size: 13px;
    text-align: center;
  }
  footer a { color: var(--muted); }

  .faq { padding: 60px 0; }
  .faq-list { max-width: 760px; margin: 0 auto; }
  .faq-item {
    background: var(--card);
    border: 1px solid #d7ece7;
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
  .faq-q::after { content: "+"; font-size: 20px; color: var(--teal); }
  .faq-item.open .faq-q::after { content: "\2013"; }
  .faq-a {
    display: none;
    padding: 0 22px 18px;
    color: var(--muted);
    font-size: 14px;
  }
  .faq-item.open .faq-a { display: block; }

  @media (max-width: 640px) {
    .hero h1 { font-size: 32px; }
    .nav-links { display: none; }
    .cmp-row { grid-template-columns: 1fr; gap: 4px; }
  }
</style>
</head>
<body>

<header>
  <div class="container header-inner">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">Daqi<span>Token</span></a>
    <nav class="nav-links">
      <a href="#how">How it works</a>
      <a href="#plans">Popular plans</a>
      <a href="#compare">Compare prices</a>
      <a href="<?php echo esc_url(home_url('/shop/')); ?>">Shop</a>
    </nav>
  </div>
</header>

<section class="country-hero">
  <div class="container">
    <div class="country-grid major">
      <?php
      $major = array(
        array('United States', 'united-states', 'us'),
        array('Japan', 'japan', 'jp'),
        array('United Kingdom', 'united-kingdom', 'gb'),
        array('Thailand', 'thailand', 'th'),
        array('Singapore', 'singapore', 'sg'),
        array('Hong Kong', 'hong-kong', 'hk'),
        array('South Korea', 'south-korea', 'kr'),
        array('Australia', 'australia', 'au'),
        array('France', 'france', 'fr'),
        array('Germany', 'germany', 'de'),
        array('Italy', 'italy', 'it'),
        array('Spain', 'spain', 'es'),
        array('Canada', 'canada', 'ca'),
        array('Mexico', 'mexico', 'mx'),
        array('Netherlands', 'netherlands', 'nl'),
        array('Switzerland', 'switzerland', 'ch'),
        array('United Arab Emirates', 'united-arab-emirates', 'ae'),
        array('Sweden', 'sweden', 'se'),
        array('Turkey', 'turkey', 'tr'),
      );
      foreach ($major as $c):
        $link = esc_url(add_query_arg('product_cat', $c[1], home_url('/shop/')));
        ?>
        <a class="country-icon" href="<?php echo $link; ?>">
          <img class="ci-flag" src="https://flagcdn.com/w80/<?php echo esc_attr($c[2]); ?>.png" srcset="https://flagcdn.com/w160/<?php echo esc_attr($c[2]); ?>.png 2x" alt="<?php echo esc_attr($c[0]); ?> flag" loading="lazy" width="80" height="60">
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="hero">
  <div class="container">
    <h1>Skip the roaming.<br>Stay connected <em>everywhere</em>.</h1>
    <p>Instant mobile data in 200+ countries with a DaqiToken eSIM. QR code in your inbox within minutes, prices compared across providers in real time.</p>
    <div class="tag-row">
      <span class="chip">🌏 200+ countries</span>
      <span class="chip">⚡ QR in minutes</span>
      <span class="chip hot">🏆 Best-price guarantee</span>
      <span class="chip">🔒 Works on iPhone &amp; Android</span>
    </div>
  </div>
</section>

<section class="how" id="how">
  <div class="container">
    <h2 class="section-title">Connected in 3 steps</h2>
    <div class="steps">
      <div class="step">
        <div class="emoji">📱</div>
        <h3>Pick your plan</h3>
        <p>Choose a country, data allowance and duration that fit your trip.</p>
      </div>
      <div class="step">
        <div class="emoji">💳</div>
        <h3>Pay &amp; get QR</h3>
        <p>Complete checkout and receive your eSIM QR code instantly by email.</p>
      </div>
      <div class="step">
        <div class="emoji">✈️</div>
        <h3>Scan &amp; go</h3>
        <p>Scan the QR on arrival and you're online. No SIM swap, no roaming bills.</p>
      </div>
    </div>
  </div>
</section>

<section class="plans" id="plans">
  <div class="container">
    <h2 class="section-title">Popular routes</h2>
    <div class="plan-grid">
      <div class="plan">
        <div class="flag">🇯🇵</div>
        <h3>Japan</h3>
        <p class="meta">1 GB · 7 days</p>
        <div class="amount"><small>from</small> $3.2</div>
        <ul>
          <li>4G/5G on major carriers</li>
          <li>Instant QR delivery</li>
          <li>eSIM compatible devices</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('product_cat', 'esim', home_url('/shop/'))); ?>">Find my Japan plan</a>
      </div>

      <div class="plan">
        <div class="flag">🇭🇰</div>
        <h3>Hong Kong</h3>
        <p class="meta">3 GB · 15 days</p>
        <div class="amount"><small>from</small> $4.5</div>
        <ul>
          <li>High-speed local network</li>
          <li>Instant QR delivery</li>
          <li>Data rollover on select plans</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('product_cat', 'esim', home_url('/shop/'))); ?>">Find my HK plan</a>
      </div>

      <div class="plan">
        <div class="flag">🇺🇸</div>
        <h3>United States</h3>
        <p class="meta">5 GB · 30 days</p>
        <div class="amount"><small>from</small> $8.9</div>
        <ul>
          <li>Nationwide 4G/5G coverage</li>
          <li>Instant QR delivery</li>
          <li>Hotspot sharing supported</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('product_cat', 'esim', home_url('/shop/'))); ?>">Find my US plan</a>
      </div>
    </div>
  </div>
</section>

<section class="compare" id="compare">
  <div class="container">
    <h2 class="section-title">Live price comparison</h2>
    <div class="grid">
      <div class="cmp-row"><span class="route">🇯🇵 Japan 1GB</span><span class="price">$3.2</span><span class="best">Best price</span></div>
      <div class="cmp-row"><span class="route">🇹🇭 Thailand 1GB</span><span class="price">$2.9</span><span class="best">Best price</span></div>
      <div class="cmp-row"><span class="route">🇰🇷 South Korea 1GB</span><span class="price">$3.5</span><span class="best">Best price</span></div>
      <div class="cmp-row"><span class="route">🇸🇬 Singapore 3GB</span><span class="price">$5.4</span><span class="best">Best price</span></div>
    </div>
  </div>
</section>

<section class="promo">
  <div class="container">
    <div class="big">Ready to travel light?</div>
    <p>Browse all eSIM plans. Prices are compared across multiple providers in real time, so you always get the best deal for your route.</p>
    <a class="btn-primary" href="<?php echo esc_url(add_query_arg('product_cat', 'esim', home_url('/shop/'))); ?>">Browse all eSIM plans</a>
  </div>
</section>

<section class="all-countries" id="countries">
  <div class="container">
    <h3 class="country-group-title">All countries</h3>
    <div class="country-grid all">
      <?php
      $all = array(
        array('United States', 'united-states', 'us'),
        array('Japan', 'japan', 'jp'),
        array('United Kingdom', 'united-kingdom', 'gb'),
        array('Thailand', 'thailand', 'th'),
        array('Singapore', 'singapore', 'sg'),
        array('Hong Kong', 'hong-kong', 'hk'),
        array('South Korea', 'south-korea', 'kr'),
        array('Australia', 'australia', 'au'),
        array('France', 'france', 'fr'),
        array('Germany', 'germany', 'de'),
        array('Italy', 'italy', 'it'),
        array('Spain', 'spain', 'es'),
        array('Canada', 'canada', 'ca'),
        array('Mexico', 'mexico', 'mx'),
        array('Brazil', 'brazil', 'br'),
        array('Argentina', 'argentina', 'ar'),
        array('Chile', 'chile', 'cl'),
        array('Netherlands', 'netherlands', 'nl'),
        array('Belgium', 'belgium', 'be'),
        array('Switzerland', 'switzerland', 'ch'),
        array('Austria', 'austria', 'at'),
        array('Sweden', 'sweden', 'se'),
        array('Norway', 'norway', 'no'),
        array('Denmark', 'denmark', 'dk'),
        array('Finland', 'finland', 'fi'),
        array('Portugal', 'portugal', 'pt'),
        array('Greece', 'greece', 'gr'),
        array('Ireland', 'ireland', 'ie'),
        array('Poland', 'poland', 'pl'),
        array('Czech Republic', 'czech-republic', 'cz'),
        array('Hungary', 'hungary', 'hu'),
        array('Turkey', 'turkey', 'tr'),
        array('United Arab Emirates', 'united-arab-emirates', 'ae'),
        array('Qatar', 'qatar', 'qa'),
        array('Saudi Arabia', 'saudi-arabia', 'sa'),
        array('Israel', 'israel', 'il'),
        array('Egypt', 'egypt', 'eg'),
        array('South Africa', 'south-africa', 'za'),
        array('Kenya', 'kenya', 'ke'),
        array('Morocco', 'morocco', 'ma'),
        array('India', 'india', 'in'),
        array('Indonesia', 'indonesia', 'id'),
        array('Malaysia', 'malaysia', 'my'),
        array('Vietnam', 'vietnam', 'vn'),
        array('Philippines', 'philippines', 'ph'),
        array('Taiwan', 'taiwan', 'tw'),
        array('New Zealand', 'new-zealand', 'nz'),
        array('Sri Lanka', 'sri-lanka', 'lk'),
        array('Nepal', 'nepal', 'np'),
        array('Maldives', 'maldives', 'mv'),
        array('Croatia', 'croatia', 'hr'),
        array('Ukraine', 'ukraine', 'ua'),
        array('China', '', 'cn'),
        array('Russia', '', 'ru'),
        array('Iceland', '', 'is'),
        array('Luxembourg', '', 'lu'),
        array('Malta', '', 'mt'),
        array('Cyprus', '', 'cy'),
        array('Estonia', '', 'ee'),
        array('Latvia', '', 'lv'),
        array('Lithuania', '', 'lt'),
        array('Romania', '', 'ro'),
        array('Bulgaria', '', 'bg'),
        array('Slovakia', '', 'sk'),
        array('Slovenia', '', 'si'),
        array('Serbia', '', 'rs'),
        array('Bosnia and Herzegovina', '', 'ba'),
        array('Montenegro', '', 'me'),
        array('North Macedonia', '', 'mk'),
        array('Albania', '', 'al'),
        array('Moldova', '', 'md'),
        array('Georgia', '', 'ge'),
        array('Armenia', '', 'am'),
        array('Azerbaijan', '', 'az'),
        array('Kazakhstan', '', 'kz'),
        array('Uzbekistan', '', 'uz'),
        array('Mongolia', '', 'mn'),
        array('Cambodia', '', 'kh'),
        array('Laos', '', 'la'),
        array('Myanmar', '', 'mm'),
        array('Bangladesh', '', 'bd'),
        array('Pakistan', '', 'pk'),
        array('Brunei', '', 'bn'),
        array('Iraq', '', 'iq'),
        array('Jordan', '', 'jo'),
        array('Lebanon', '', 'lb'),
        array('Kuwait', '', 'kw'),
        array('Bahrain', '', 'bh'),
        array('Oman', '', 'om'),
        array('Libya', '', 'ly'),
        array('Algeria', '', 'dz'),
        array('Tunisia', '', 'tn'),
        array('Sudan', '', 'sd'),
        array('Ethiopia', '', 'et'),
        array('Tanzania', '', 'tz'),
        array('Uganda', '', 'ug'),
        array('Ghana', '', 'gh'),
        array('Nigeria', '', 'ng'),
        array('Senegal', '', 'sn'),
        array('Ivory Coast', '', 'ci'),
        array('Cameroon', '', 'cm'),
        array('Angola', '', 'ao'),
        array('Mozambique', '', 'mz'),
        array('Zimbabwe', '', 'zw'),
        array('Botswana', '', 'bw'),
        array('Namibia', '', 'na'),
        array('Zambia', '', 'zm'),
        array('Rwanda', '', 'rw'),
        array('Madagascar', '', 'mg'),
        array('Mauritius', '', 'mu'),
        array('Seychelles', '', 'sc'),
        array('Colombia', '', 'co'),
        array('Peru', '', 'pe'),
        array('Venezuela', '', 've'),
        array('Ecuador', '', 'ec'),
        array('Bolivia', '', 'bo'),
        array('Paraguay', '', 'py'),
        array('Uruguay', '', 'uy'),
        array('Cuba', '', 'cu'),
        array('Jamaica', '', 'jm'),
        array('Dominican Republic', '', 'do'),
        array('Costa Rica', '', 'cr'),
        array('Panama', '', 'pa'),
        array('Guatemala', '', 'gt'),
        array('Honduras', '', 'hn'),
        array('Nicaragua', '', 'ni'),
        array('El Salvador', '', 'sv'),
        array('Trinidad and Tobago', '', 'tt'),
        array('Fiji', '', 'fj'),
        array('Papua New Guinea', '', 'pg'),
        array('Bahamas', '', 'bs'),
        array('Barbados', '', 'bb'),
      );
      foreach ($all as $c):
        $link = $c[1] !== ''
          ? esc_url(add_query_arg('product_cat', $c[1], home_url('/shop/')))
          : esc_url(add_query_arg('s', $c[0], home_url('/shop/')));
        ?>
        <a class="country-icon" href="<?php echo $link; ?>" title="<?php echo esc_attr($c[0]); ?>">
          <img class="ci-flag" src="https://flagcdn.com/w80/<?php echo esc_attr($c[2]); ?>.png" srcset="https://flagcdn.com/w160/<?php echo esc_attr($c[2]); ?>.png 2x" alt="<?php echo esc_attr($c[0]); ?> flag" loading="lazy" width="80" height="60">
          <span class="ci-name"><?php echo esc_html($c[0]); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="faq" id="faq">
  <div class="container">
    <h2 class="section-title">Frequently asked questions</h2>
    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-q">How does an eSIM work?</div>
        <div class="faq-a">An eSIM is a digital SIM built into your phone. After checkout you receive a QR code, scan it with your camera, and your data plan activates on the spot. No physical SIM card or tray required.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Which phones are eSIM compatible?</div>
        <div class="faq-a">eSIM works on iPhone XS and newer, recent Google Pixel models, Samsung Galaxy S20 and newer, plus most 2021+ flagships. Check your phone settings for an "Add eSIM" option to confirm.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">How fast do I receive my eSIM?</div>
        <div class="faq-a">Your QR code is delivered by email within minutes of payment confirmation, so you can buy right before boarding and connect when you land.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Will I lose my current phone number?</div>
        <div class="faq-a">No. Your normal SIM and number stay active. The travel eSIM adds a second line of data on the same device, and you keep your usual calls and texts.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Can I get a refund if my trip changes?</div>
        <div class="faq-a">Yes, within 14 days of purchase if the eSIM has not been activated. Refunds are processed back to your original payment method.</div>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="container">
    <p>DaqiToken eSIM &#8212; Instant global data. <a href="<?php echo esc_url(home_url('/about/')); ?>">About</a> · <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></p>
  </div>
</footer>

</body>
<script>
  document.querySelectorAll(".faq-q").forEach(function (q) {
    q.addEventListener("click", function () {
      q.parentElement.classList.toggle("open");
    });
  });
</script>
</html>
