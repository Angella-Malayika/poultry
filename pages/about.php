<?php 
// pages/about.php – Fixed paths using BASE_URL from config.php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth_required.php'; 

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/styles.css">
    <style>
        .section-heading h2 {
            color: #2e7d32;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .section-kicker {
            display: inline-block;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            background: rgba(46, 125, 50, 0.1);
            color: #2e7d32;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 0.9rem;
        }
        .section-heading p {
            max-width: 760px;
            margin: 0 auto;
            color: #556;
        }
        .story-shell {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbf3 100%);
            border: 1px solid rgba(46, 125, 50, 0.08);
            border-radius: 28px;
            padding: 1.25rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.06);
        }
        .story-visual {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            min-height: 520px;
            background: #e9f3e7;
        }
        .story-image {
            width: 100%;
            height: 520px;
            object-fit: cover;
            display: block;
        }
        .story-badge {
            position: absolute;
            left: 18px;
            bottom: 18px;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(8px);
            border-radius: 18px;
            padding: 0.95rem 1rem;
            display: flex;
            gap: 0.8rem;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
        .story-badge-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: #2e7d32;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 800;
        }
        .story-badge-text {
            color: #1a1a1a;
            font-weight: 700;
            line-height: 1.3;
        }
        .story-content {
            padding: 0.5rem 0.5rem 0.5rem 1rem;
        }
        .story-content h3 {
            color: #2e7d32;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .story-content p {
            color: #334;
            margin-bottom: 1rem;
        }
        .story-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .story-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            background: #f1f8e9;
            color: #2e7d32;
            font-weight: 700;
            font-size: 0.92rem;
        }
        .story-quote {
            margin: 1.5rem 0 1.25rem;
            padding: 1.15rem 1.2rem;
            border-left: 4px solid #2e7d32;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
        }
        .story-quote i {
            color: #2e7d32;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .story-quote blockquote {
            margin: 0;
            font-style: italic;
            color: #2f3b2f;
            font-size: 1.05rem;
        }
        .story-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.9rem;
        }
        .story-stat {
            background: #fff;
            border: 1px solid rgba(46, 125, 50, 0.08);
            border-radius: 18px;
            padding: 1rem;
            text-align: center;
        }
        .story-stat strong {
            display: block;
            color: #2e7d32;
            font-size: 1.4rem;
            line-height: 1.1;
        }
        .story-stat span {
            display: block;
            color: #556;
            font-size: 0.92rem;
            margin-top: 0.35rem;
        }
        @media (max-width: 991px) {
            .story-content { padding: 0.25rem; }
            .story-visual, .story-image { min-height: 380px; height: 380px; }
        }
        @media (max-width: 576px) {
            .story-stats { grid-template-columns: 1fr; }
            .story-badge { left: 12px; right: 12px; }
            .story-badge-number { width: 56px; height: 56px; flex: 0 0 56px; }
        }
    </style>
</head>
<body>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>
    <main class="about-page">
        <!-- Hero Section -->
        <section class="about-hero py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="about-hero-content">
                            <span class="badge bg-success mb-2">Established 2015</span>
                            <h1 style="color: #2e7d32;">Empowering Farmers, Nourishing Communities</h1>
                            <p class="text-muted" style="color: black;">
                                At Kalungu Quality Feeds, we bridge the gap between traditional farming and modern innovation.
                                Our commitment to quality feeds, expert consultancy, and reliable service has made us the trusted partner
                                for farmers across Uganda.
                            </p>
                            <div class="about-hero-actions">
                                <a class="btn btn-sm rounded-4 border border-2 border-success text-success" href="<?php echo BASE_URL; ?>/product.php#feeds">
                                    <i class="fas fa-seedling me-2"></i>Explore Our Products
                                </a>
                                <a class="btn btn-sm rounded-5 border border-2 border-success text-success" href="<?php echo BASE_URL; ?>/contact.php">
                                    <i class="fas fa-phone me-2"></i>Contact Us
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="about-hero-media">
                            <img src="<?php echo BASE_URL; ?>/images/white hen.jpg" alt="Kalungu Quality Feeds - Supporting Farmers" class="img-fluid rounded-3 shadow w-100" style="object-fit: cover; height: 400px; margin-left: 200px;">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="our-story py-5">
            <div class="container">
                <div class="section-heading text-center mb-5">
                    <span class="section-kicker">Our Story</span>
                    <h2>From a Small Feed Shop to a Trusted Farming Partner</h2>
                    <p>Built with local farmers, for local farmers.</p>
                </div>
                <div class="story-shell">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <div class="story-visual">
                                <img src="<?php echo BASE_URL; ?>/images/lydia.jpeg" alt="Our Journey" class="img-fluid story-image">
                                <div class="story-badge">
                                    <span class="story-badge-number">2014</span>
                                    <span class="story-badge-text">Founded in Kalungu</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="story-content">
                                <div class="story-meta">
                                    <div class="story-chip"><i class="fas fa-seedling"></i> Local roots</div>
                                    <div class="story-chip"><i class="fas fa-users"></i> Farmer-first service</div>
                                </div>
                                <h3>How It All Began</h3>
                                <p>
                                    Founded in 2014 by passionate agricultural experts and local farmers, Kalungu Quality Feeds
                                    started with a mission: provide accessible, high-quality livestock nutrition to our community.
                                </p>
                                <p>
                                    From a small feed distribution center on Kyakumpi Viira Road, we have grown into a
                                    comprehensive agricultural solutions provider, serving over 1,200 farmers across the region.
                                    Our growth led to two branches so farmers can reach us faster and get support more easily.
                                </p>
                                <p>
                                    Our first branch remains at Kyakumpi Viira Road, where the journey began. Our second branch
                                    is along Kitovu Road, slightly opposite Rubbis Gas Station as you head toward Kitovu Cathedral.
                                    Together, these locations help us serve the community with better access and faster response.
                                </p>
                                <div class="story-quote">
                                    <i class="fas fa-quote-left"></i>
                                    <blockquote>Our commitment to farmers goes beyond selling products. We are invested in long-term success.</blockquote>
                                </div>
                                <div class="story-stats">
                                    <div class="story-stat"><strong>500+</strong><span>Farmers served</span></div>
                                    <div class="story-stat"><strong>2</strong><span>Convenient branches</span></div>
                                    <div class="story-stat"><strong>10+</strong><span>Years of service</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="about-why py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Why Choose Kalungu Quality Feeds?</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="why-card">
                            <i class="fas fa-shield-alt" style="color: #2e7d32;"></i>
                            <h4 style="color: #2e7d32;">Quality Assurance</h4>
                            <p style="color: #333;">All our feeds undergo rigorous testing to ensure nutritional excellence and safety.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="why-card">
                            <i class="fas fa-truck" style="color: #2e7d32;"></i>
                            <h4 style="color: #2e7d32;">Reliable Delivery</h4>
                            <p style="color: #333;">Timely delivery across Kalungu and surrounding areas with dedicated logistics support.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="why-card">
                            <i class="fas fa-users" style="color: #2e7d32;"></i>
                            <h4 style="color: #2e7d32;">Expert Guidance</h4>
                            <p style="color: #333;">Access to agricultural specialists for personalized farming advice and solutions.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="why-card">
                            <i class="fas fa-leaf" style="color: #2e7d32;"></i>
                            <h4 style="color: #2e7d32;">Sustainable Practices</h4>
                            <p style="color: #333;">Committed to environmentally friendly farming methods and community development.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="why-card">
                            <i class="fas fa-headset" style="color: #2e7d32;"></i>
                            <h4 style="color: #2e7d32;">Customer Care</h4>
                            <p style="color: #333;">Dedicated support team available to assist farmers with any queries or concerns.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="about-stats py-5" style="background-color: #f9fbe7;">
            <div class="container">
                <h2 class="text-center mb-5" style="color: #2e7d32;">Our Impact in Numbers</h2>
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-4"><div class="stat-card"><div class="stat-number" style="color: #2e7d32;">500+</div><div class="stat-label" style="color: #333;">Farmers Served</div></div></div>
                    <div class="col-md-3 col-6 mb-4"><div class="stat-card"><div class="stat-number" style="color: #2e7d32;">98%</div><div class="stat-label" style="color: #333;">Product Varieties</div></div></div>
                    <div class="col-md-3 col-6 mb-4"><div class="stat-card"><div class="stat-number" style="color: #2e7d32;">24hrs</div><div class="stat-label" style="color: #333;">Avg. Delivery Time</div></div></div>
                    <div class="col-md-3 col-6 mb-4"><div class="stat-card"><div class="stat-number" style="color: #2e7d32;">98%</div><div class="stat-label" style="color: #333;">Customer Satisfaction</div></div></div>
                </div>
            </div>
        </section>

        <!-- Mission, Vision, Values -->
        <section class="about-mvv py-5">
            <div class="container">
                <h2 class="text-center mb-5" style="color: #2e7d32;">Our Core Values</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="mvv-card card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="mvv-icon mb-3"><i class="fas fa-bullseye fa-3x" style="color: #2e7d32;"></i></div>
                                <h4 style="color: #2e7d32;">Our Mission</h4>
                                <p class="text-muted small">To provide top-notch animal feeds that enhance the health, productivity, and profitability of our customers' livestock, while promoting sustainable agricultural practices and contributing to food security.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mvv-card card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="mvv-icon mb-3"><i class="fas fa-eye fa-3x" style="color: #2e7d32;"></i></div>
                                <h4 style="color: #2e7d32;">Our Vision</h4>
                                <p class="text-muted small">To be a leading supplier of high-quality animal feeds in Uganda and beyond, improving customer satisfaction through innovative products and services.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mvv-card card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="mvv-icon mb-3"><i class="fas fa-bullseye fa-3x" style="color: #2e7d32;"></i></div>
                                <h4 style="color: #2e7d32;">Our Objectives</h4>
                                <p class="text-muted small">To ensure all products meet the highest standards of quality, safety, and nutritional value.</p>
                                <p class="text-muted small">To build strong relationships with customers through responsive service, technical support, and tailored solutions.</p>
                                <p class="text-muted small">To continuously develop new products and improve existing ones through research and development.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mvv-card card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="mvv-icon mb-3"><i class="fas fa-heart fa-3x" style="color: #2e7d32;"></i></div>
                                <h4 style="color: #2e7d32;">Core of Conduct</h4>
                                <p class="text-muted small">Operating with transparency, honesty, and ethics in all business dealings.</p>
                                <p class="text-muted small">Maintaining rigorous quality control measures throughout production and distribution.</p>
                                <p class="text-muted small">Prioritizing customer needs, provide excellent service, and ensure timely delivery.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Timeline Section -->
        <section class="about-timeline py-5" style="background-color: #f9fbe7;">
            <div class="container text-center">
                <h2 class="text-center mb-5" style="color: #2e7d32;">Our Journey & Support Process</h2>
                <div class="timeline-container">
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker" style="background-color: #2e7d32;"></div>
                        <div class="timeline-content">
                            <h4 style="color: #2e7d32;">Initial Consultation</h4>
                            <p style="color: #333;">We assess our farmers' needs through personalized consultations and farm visits.</p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker" style="background-color: #2e7d32;"></div>
                        <div class="timeline-content">
                            <h4 style="color: #2e7d32;">Customized Solutions</h4>
                            <p style="color: #333;">Receive tailored feed formulations and chick supplies based on your specific requirements.</p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker" style="background-color: #2e7d32;"></div>
                        <div class="timeline-content">
                            <h4 style="color: #2e7d32;">Ongoing Training</h4>
                            <p style="color: #333;">Access continuous education on best practices, vaccination schedules, and farm management.</p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker" style="background-color: #2e7d32;"></div>
                        <div class="timeline-content">
                            <h4 style="color: #2e7d32;">Continuous Support</h4>
                            <p style="color: #333;">Enjoy 24/7 assistance, timely deliveries, and performance monitoring for optimal results.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certifications & Quality Standards -->
        <section class="certifications py-5">
            <div class="container">
                <h2 class="text-center mb-5" style="color: #2e7d32;">Certifications & Quality Standards</h2>
                <div class="row g-4">
                    <div class="col-md-3 text-center">
                        <div class="cert-card p-4 rounded shadow-sm h-100" style="background-color: #f9fbe7;">
                            <i class="fas fa-check-circle fa-3x mb-3" style="color: #2e7d32;"></i>
                            <h5 style="color: #2e7d32;">UNBS Approved</h5>
                            <p class="small" style="color: #333;">Uganda National Bureau of Standards certified products</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <h4 class="text-center mb-4" style="color: #2e7d32;">Our Quality Commitments</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2" style="color: #333;"><i class="fas fa-check me-2" style="color: #2e7d32;"></i>100% traceability of all ingredients</li>
                                <li class="mb-2" style="color: #333;"><i class="fas fa-check me-2" style="color: #2e7d32;"></i>Regular third-party quality audits</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2" style="color: #333;"><i class="fas fa-check me-2" style="color: #2e7d32;"></i>Freshness guarantee on all products</li>
                                <li class="mb-2" style="color: #333;"><i class="fas fa-check me-2" style="color: #2e7d32;"></i>Transparent nutritional labeling</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Community Involvement / CSR -->
        <section class="community-involvement py-5" style="background-color: #f9fbe7;">
            <div class="container">
                <h2 class="text-center mb-5" style="color: #2e7d32;">Community Impact & Activities</h2>
                <p class="text-center lead mb-5" style="color: #333;">We believe in giving back to the community that has supported our growth</p>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="mb-3"><i class="fas fa-graduation-cap fa-3x" style="color: #2e7d32;"></i></div>
                                <h4 style="color: #2e7d32;">Farmer Training Programs</h4>
                                <p style="color: #000000ff;">Monthly workshops on modern farming techniques, disease prevention, and business management for local farmers.</p>
                                <ul class="small" style="color: #000000ff;">
                                    <li>Over 100 farmers trained annually</li>
                                    <li>Topics: poultry management, nutrition</li>
                                    <li>Hands-on demonstrations and field visits</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="mb-3"><i class="fas fa-hands-helping fa-3x" style="color: #2e7d32;"></i></div>
                                <h4 style="color: #2e7d32;">Youth Empowerment</h4>
                                <p style="color: #000000ff;">Supporting young entrepreneurs to start their own poultry businesses through mentorship and training programs.</p>
                                <ul class="small" style="color: #000000ff;">
                                    <li>5+ youth-led farms established</li>
                                    <li>Free consultancy for first-time farmers</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="mb-3"><i class="fas fa-heart fa-3x" style="color: #2e7d32;"></i></div>
                                <h4 style="color: #2e7d32;">Community Support</h4>
                                <p style="color: #000000ff;">Active participation in local development initiatives and support for vulnerable farming families.</p>
                                <ul class="small" style="color: #000000ff;">
                                    <li>Support for widows and single-parent farmers</li>
                                    <li>Environmental conservation efforts</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-5" style="background-color: white;">
            <div class="container text-center">
                <h2 style="color:#2e7d32;">Join Our Growing Community of Successful Farmers</h2>
                <p class="lead mb-4" style="color: #000000ff;">Ready to elevate your farming operation? Let's discuss how we can support your goals.</p>
                <div class="cta-buttons">
                    <a class="btn btn-lg me-3" href="<?php echo BASE_URL; ?>/pages/order.php" style="background-color:#f9fbe7 ; color: #2e7d32; border: none;">
                        <i class="fas fa-shopping-cart me-2"></i>Start Your Order
                    </a>
                    <a class="btn btn-lg" href="<?php echo BASE_URL; ?>/pages/contact.php" style="background-color: white; color: #2e7d32; border: 2px solid #2e7d32;">
                        <i class="fas fa-calendar me-2"></i>Schedule Consultation
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include dirname(__DIR__) . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>