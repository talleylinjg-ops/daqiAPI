<?php
/**
 * Template Name: VPN Landing
 *
 * Cyber-security themed landing page for VPN products.
 * Buy buttons link to the WooCommerce VPN category.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DaqiToken VPN | Secure &amp; Private - Your Own Network</title>
<meta name="description" content="Private, secure VPN with your own dedicated nodes. Blazing fast WireGuard, zero logs, multiple regions, one account. Take back your privacy.">
<link rel="canonical" href="<?php echo esc_url(home_url()); ?>/vpn/">
<meta property="og:type" content="website">
<meta property="og:title" content="DaqiToken VPN | Secure &amp; Private - Your Own Network">
<meta property="og:description" content="Private, secure VPN with your own dedicated nodes. Blazing fast WireGuard, zero logs, multiple regions, one account. Take back your privacy.">
<meta property="og:url" content="<?php echo esc_url(home_url()); ?>/vpn/">
<meta property="og:site_name" content="DaqiToken">
<meta property="og:image" content="<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/og-share.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="DaqiToken VPN | Secure &amp; Private - Your Own Network">
<meta name="twitter:description" content="Private, secure VPN with your own dedicated nodes. Blazing fast WireGuard, zero logs, multiple regions, one account. Take back your privacy.">
<meta name="twitter:image" content="<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/og-share.png">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "<?php echo esc_url(home_url()); ?>/vpn/#webpage",
      "url": "<?php echo esc_url(home_url()); ?>/vpn/",
      "name": "DaqiToken VPN | Secure &amp; Private - Your Own Network",
      "description": "Private, secure VPN with your own dedicated nodes. Blazing fast WireGuard, zero logs, multiple regions, one account. Take back your privacy.",
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
        "url": "<?php echo esc_url(home_url()); ?>/wp-content/uploads/2026/08/og-share.png",
        "width": 1200,
        "height": 630
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
      "@id": "<?php echo esc_url(home_url()); ?>/vpn/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "What is a dedicated node VPN?",
          "acceptedAnswer": { "@type": "Answer", "text": "Unlike shared commercial VPNs, every DaqiToken plan provisions nodes that only you use. You get a private WireGuard setup with no noisy neighbours and no rate throttling." }
        },
        {
          "@type": "Question",
          "name": "Do you keep logs?",
          "acceptedAnswer": { "@type": "Answer", "text": "No. We operate a strict zero-log policy and publish our configuration for review. Your traffic history stays on your device only." }
        },
        {
          "@type": "Question",
          "name": "Which devices can I use?",
          "acceptedAnswer": { "@type": "Answer", "text": "WireGuard clients run on Windows, macOS, Linux, iOS and Android. One plan covers multiple devices, up to the limit of your chosen tier." }
        },
        {
          "@type": "Question",
          "name": "How fast is the VPN?",
          "acceptedAnswer": { "@type": "Answer", "text": "WireGuard adds very little overhead, so you typically keep most of your ISP line speed. Real-world results across regions are published in our VPN speed test." }
        },
        {
          "@type": "Question",
          "name": "Can I get a refund?",
          "acceptedAnswer": { "@type": "Answer", "text": "Yes, within 14 days of purchase if the service is unused. Refunds are processed back to your original payment method." }
        }
      ]
    }
  ]
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #0a0a12;
    --bg-soft: #12121f;
    --card: #171726;
    --border: #26263a;
    --text: #e8e8f2;
    --muted: #8a8aa3;
    --accent: #a855f7;
    --accent2: #22d3ee;
    --green: #4ade80;
    --radius: 14px;
    --mono: 'JetBrains Mono', monospace;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html { scroll-behavior: smooth; }
  body {
    font-family: 'Space Grotesk', -apple-system, 'Segoe UI', sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.6;
    overflow-x: hidden;
  }
  .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
  .grid-bg {
    position: fixed;
    inset: 0;
    z-index: -1;
    background-image:
      linear-gradient(rgba(168,85,247,0.05) 1px, transparent 1px),
      linear-gradient(90deg, rgba(168,85,247,0.05) 1px, transparent 1px);
    background-size: 42px 42px;
  }

  header {
    padding: 18px 0;
    border-bottom: 1px solid var(--border);
    background: rgba(10,10,18,0.85);
    position: sticky;
    top: 0;
    z-index: 10;
    backdrop-filter: blur(8px);
  }
  .header-inner { display: flex; align-items: center; justify-content: space-between; }
  .brand {
    font-weight: 700;
    font-size: 18px;
    letter-spacing: 0.3px;
    color: var(--text);
    text-decoration: none;
    font-family: var(--mono);
  }
  .brand span { color: var(--accent); }
  .nav-links a {
    color: var(--muted);
    text-decoration: none;
    margin-left: 24px;
    font-size: 14px;
    font-weight: 500;
    font-family: var(--mono);
    transition: color 0.2s;
  }
  .nav-links a:hover { color: var(--accent2); }

  .disclaimer {
    padding: 16px 0;
    background: rgba(168,85,247,0.08);
    border-bottom: 1px solid var(--border);
  }
  .disclaimer p {
    font-size: 13px;
    color: var(--muted);
    line-height: 1.6;
  }
  .disclaimer strong {
    color: var(--accent);
  }
  .hero {
    padding: 44px 0 60px;
    text-align: center;
    position: relative;
  }
  .hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
      radial-gradient(600px 300px at 50% 0%, rgba(168,85,247,0.22), transparent 70%);
    z-index: -1;
  }
  .hero .shield {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 72px;
    height: 72px;
    border-radius: 20px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    font-size: 34px;
    margin-bottom: 24px;
    box-shadow: 0 0 40px rgba(168,85,247,0.4);
  }
  .hero h1 {
    font-size: 46px;
    font-weight: 700;
    letter-spacing: -1px;
    line-height: 1.12;
    margin-bottom: 16px;
  }
  .hero h1 em {
    font-style: normal;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }
  .hero p {
    color: var(--muted);
    font-size: 17px;
    max-width: 640px;
    margin: 0 auto;
  }
  .terminal {
    max-width: 560px;
    margin: 36px auto 0;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-align: left;
    font-family: var(--mono);
    font-size: 13px;
    overflow: hidden;
  }
  .terminal .bar {
    display: flex;
    gap: 6px;
    padding: 10px 14px;
    background: var(--bg-soft);
    border-bottom: 1px solid var(--border);
  }
  .terminal .bar i { width: 10px; height: 10px; border-radius: 50%; display: block; }
  .terminal .bar i:nth-child(1) { background: #f87171; }
  .terminal .bar i:nth-child(2) { background: #fbbf24; }
  .terminal .bar i:nth-child(3) { background: #4ade80; }
  .terminal pre { padding: 16px; color: #b6b6ce; white-space: pre-wrap; }
  .terminal .ok { color: var(--green); }
  .terminal .cmd { color: var(--accent2); }

  .stats {
    display: flex;
    justify-content: center;
    gap: 48px;
    flex-wrap: wrap;
    padding: 36px 0 8px;
  }
  .stat { text-align: center; }
  .stat .num {
    font-size: 32px;
    font-weight: 700;
    font-family: var(--mono);
    color: var(--text);
  }
  .stat .num span { color: var(--accent); }
  .stat .label { font-size: 13px; color: var(--muted); margin-top: 4px; }

  .features { padding: 56px 0; }
  .section-title {
    text-align: center;
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 40px;
    letter-spacing: -0.5px;
  }
  .feat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 22px;
  }
  .feat {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 26px 24px;
    transition: border-color 0.2s, transform 0.2s;
  }
  .feat:hover { border-color: rgba(168,85,247,0.6); transform: translateY(-3px); }
  .feat .icon { font-size: 26px; margin-bottom: 12px; }
  .feat h3 { font-size: 17px; margin-bottom: 8px; }
  .feat p { color: var(--muted); font-size: 14px; }

  .regions { padding: 56px 0; background: var(--bg-soft); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
  .region-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    max-width: 860px;
    margin: 0 auto;
  }
  .region {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px 18px;
    font-family: var(--mono);
    font-size: 14px;
  }
  .region .ping { color: var(--green); }
  .region .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--green); display: inline-block; margin-right: 8px; }

  .plans { padding: 56px 0; }
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
    padding: 30px 26px;
    display: flex;
    flex-direction: column;
    position: relative;
    transition: border-color 0.2s, transform 0.2s;
  }
  .plan:hover { border-color: rgba(34,211,238,0.5); transform: translateY(-3px); }
  .plan.popular { border-color: var(--accent); }
  .plan .badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 14px;
    border-radius: 20px;
    font-family: var(--mono);
  }
  .plan .plan-name { font-family: var(--mono); font-size: 14px; color: var(--accent2); margin-bottom: 8px; }
  .plan .amount { font-size: 38px; font-weight: 700; letter-spacing: -1px; }
  .plan .amount small { font-size: 16px; color: var(--muted); font-weight: 500; }
  .plan .desc { color: var(--muted); font-size: 14px; margin: 6px 0 20px; }
  .plan ul { list-style: none; margin-bottom: 26px; flex: 1; }
  .plan ul li {
    font-size: 14px;
    padding: 6px 0;
    display: flex;
    gap: 10px;
    font-family: var(--mono);
    font-size: 13px;
  }
  .plan ul li::before { content: "▸"; color: var(--accent); font-weight: 700; }
  .buy-btn {
    display: block;
    width: 100%;
    text-align: center;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    color: #0a0a12;
    text-decoration: none;
    font-weight: 700;
    font-size: 15px;
    padding: 13px 20px;
    border-radius: 10px;
    font-family: var(--mono);
    transition: opacity 0.2s, transform 0.2s;
  }
  .buy-btn:hover { opacity: 0.9; transform: translateY(-1px); }

  .promo {
    padding: 56px 0;
    text-align: center;
  }
  .promo .big {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 10px;
  }
  .promo p { color: var(--muted); font-size: 15px; max-width: 560px; margin: 0 auto 22px; }
  .btn-primary {
    display: inline-block;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    color: #0a0a12;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    padding: 14px 34px;
    border-radius: 12px;
    font-family: var(--mono);
    transition: opacity 0.2s, transform 0.2s;
  }
  .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); }

  footer {
    padding: 32px 0;
    border-top: 1px solid var(--border);
    color: var(--muted);
    font-size: 13px;
    text-align: center;
    font-family: var(--mono);
  }
  footer a { color: var(--muted); }

  @media (max-width: 640px) {
    .hero h1 { font-size: 32px; }
    .nav-links { display: none; }
  }

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
  .faq-item.open .faq-q::after { content: "\2013"; }
  .faq-a {
    display: none;
    padding: 0 22px 18px;
    color: var(--muted);
    font-size: 14px;
  }
  .faq-item.open .faq-a { display: block; }
</style>
</head>
<body>
<div class="grid-bg"></div>

<header>
  <div class="container header-inner">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">Daqi<span>Token</span></a>
    <nav class="nav-links">
      <a href="#features">Features</a>
      <a href="#regions">Regions</a>
      <a href="#plans">Pricing</a>
      <a href="<?php echo esc_url(home_url('/shop/')); ?>">Shop</a>
    </nav>
  </div>
</header>

<section class="disclaimer">
  <div class="container">
    <p><strong>重要法律声明 / Legal Notice：</strong>依据中华人民共和国相关法律法规，任何组织或个人未经许可不得在中国境内提供或使用网络虚拟专用网络（VPN）服务。本店销售对象仅限中国大陆以外的个人与企业，不得用于在中国境内访问境外网络。中国大陆地区用户请勿购买本产品。</p>
  </div>
</section>

<section class="hero">
  <div class="container">
    <div class="shield">🛡️</div>
    <h1>Your traffic.<br>Your rules. <em>Zero logs.</em></h1>
    <p>A private VPN built on your own dedicated nodes. WireGuard speed, no bandwidth caps, multiple regions — one account to rule them all.</p>

    <div class="terminal">
      <div class="bar"><i></i><i></i><i></i><span style="margin-left:8px;color:#8a8aa3;">wireguard — daqi vpn</span></div>
      <pre><span class="cmd">$</span> daqi vpn connect --region=primary
<span class="ok">✓ Handshake completed in 12ms</span>
<span class="ok">✓ Your IP is now protected</span>
<span class="ok">✓ Tunnel status: ACTIVE</span>
<span class="cmd">$</span> speedtest --server=auto
Download: <span class="ok">238 Mbps</span>   Upload: <span class="ok">112 Mbps</span></pre>
    </div>

    <div class="stats">
      <div class="stat"><div class="num">200+<span>ms</span></div><div class="label">Handshake</div></div>
      <div class="stat"><div class="num">0<span>logs</span></div><div class="label">Data kept</div></div>
      <div class="stat"><div class="num">2<span>regions</span></div><div class="label">Dedicated nodes</div></div>
      <div class="stat"><div class="num">24/7<span></span></div><div class="label">Your nodes</div></div>
    </div>
  </div>
</section>

<section class="features" id="features">
  <div class="container">
    <h2 class="section-title">Built for privacy</h2>
    <div class="feat-grid">
      <div class="feat">
        <div class="icon">🚀</div>
        <h3>WireGuard speed</h3>
        <p>Modern protocol with minimal overhead. Near-native speeds for streaming, gaming and downloads.</p>
      </div>
      <div class="feat">
        <div class="icon">🔒</div>
        <h3>Your own nodes</h3>
        <p>No shared servers, no crowded IPs. Every node is dedicated to you and provisioned on your own hardware.</p>
      </div>
      <div class="feat">
        <div class="icon">🗑️</div>
        <h3>Zero logs</h3>
        <p>No activity logs, no connection timestamps, nothing to hand over. Your browsing stays yours.</p>
      </div>
      <div class="feat">
        <div class="icon">🌐</div>
        <h3>Multi-region</h3>
        <p>Switch between primary and backup nodes across regions with a single command or tap.</p>
      </div>
      <div class="feat">
        <div class="icon">📱</div>
        <h3>All your devices</h3>
        <p>Native WireGuard clients for iOS, Android, macOS, Windows and Linux. Up to 5 devices per account.</p>
      </div>
      <div class="feat">
        <div class="icon">⚡</div>
        <h3>No caps</h3>
        <p>Unlimited bandwidth on every plan. Your nodes, your pipe, your limit — if any.</p>
      </div>
    </div>
  </div>
</section>

<section class="regions" id="regions">
  <div class="container">
    <h2 class="section-title">Node status</h2>
    <div class="region-list">
      <div class="region"><span><span class="dot"></span>primary · us-west</span><span class="ping">12ms</span></div>
      <div class="region"><span><span class="dot"></span>backup · us-central</span><span class="ping">21ms</span></div>
      <div class="region"><span><span class="dot"></span>asia · hk</span><span class="ping">8ms</span></div>
    </div>
  </div>
</section>

<section class="plans" id="plans">
  <div class="container">
    <h2 class="section-title">Choose your setup</h2>
    <div class="plan-grid">
      <div class="plan">
        <div class="plan-name">SINGLE_NODE</div>
        <div class="amount"><small>$</small>9<span style="font-size:14px;color:var(--muted);">/mo</span></div>
        <p class="desc">One dedicated node. Perfect for personal browsing.</p>
        <ul>
          <li>1 primary node</li>
          <li>5 devices</li>
          <li>Unlimited bandwidth</li>
          <li>Email support</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('product_cat', 'vpn', home_url('/shop/'))); ?>">Deploy node</a>
      </div>

      <div class="plan popular">
        <span class="badge">Recommended</span>
        <div class="plan-name">DUAL_NODE</div>
        <div class="amount"><small>$</small>15<span style="font-size:14px;color:var(--muted);">/mo</span></div>
        <p class="desc">Primary + backup across two regions. Automatic failover.</p>
        <ul>
          <li>2 dedicated nodes</li>
          <li>10 devices</li>
          <li>Unlimited bandwidth</li>
          <li>Auto failover</li>
          <li>Priority support</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('product_cat', 'vpn', home_url('/shop/'))); ?>">Deploy nodes</a>
      </div>

      <div class="plan">
        <div class="plan-name">TEAM_MESH</div>
        <div class="amount"><small>$</small>29<span style="font-size:14px;color:var(--muted);">/mo</span></div>
        <p class="desc">For teams and power users. Full mesh across nodes.</p>
        <ul>
          <li>Up to 3 nodes</li>
          <li>25 devices</li>
          <li>Unlimited bandwidth</li>
          <li>Mesh networking</li>
          <li>Dedicated support</li>
        </ul>
        <a class="buy-btn" href="<?php echo esc_url(add_query_arg('product_cat', 'vpn', home_url('/shop/'))); ?>">Deploy mesh</a>
      </div>
    </div>
  </div>
</section>

<section class="promo">
  <div class="container">
    <div class="big">Take back your privacy.</div>
    <p>Set up your own dedicated VPN nodes with our guided scripts and get secure browsing everywhere, today.</p>
    <a class="btn-primary" href="<?php echo esc_url(add_query_arg('product_cat', 'vpn', home_url('/shop/'))); ?>">Explore VPN plans</a>
  </div>
</section>

<section class="faq" id="faq">
  <div class="container">
    <h2 class="section-title">Frequently asked questions</h2>
    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-q">What is a dedicated node VPN?</div>
        <div class="faq-a">Unlike shared commercial VPNs, every DaqiToken plan provisions nodes that only you use. You get a private WireGuard setup with no noisy neighbours and no rate throttling.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Do you keep logs?</div>
        <div class="faq-a">No. We operate a strict zero-log policy and publish our configuration for review. Your traffic history stays on your device only.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Which devices can I use?</div>
        <div class="faq-a">WireGuard clients run on Windows, macOS, Linux, iOS and Android. One plan covers multiple devices, up to the limit of your chosen tier.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">How fast is the VPN?</div>
        <div class="faq-a">WireGuard adds very little overhead, so you typically keep most of your ISP line speed. Real-world results across regions are published in our VPN speed test.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Can I get a refund?</div>
        <div class="faq-a">Yes, within 14 days of purchase if the service is unused. Refunds are processed back to your original payment method.</div>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="container">
    <p>DaqiToken VPN &#8212; your own network. <a href="<?php echo esc_url(home_url('/about/')); ?>">About</a> · <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></p>
  </div>
</footer>

<script>
  document.querySelectorAll(".faq-q").forEach(function (q) {
    q.addEventListener("click", function () {
      q.parentElement.classList.toggle("open");
    });
  });
</script>

</body>
</html>
