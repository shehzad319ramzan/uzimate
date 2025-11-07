<x-layouts.guest page-title="Uzimate - Loyalty Simplified. Growth Amplified">
    <!-- Hero Section -->
    <section id="home" class="hero-section" style="margin-top: 80px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content fade-in-up">
                        <h1 class="display-heading">
                            <span class="highlight-yellow">Loyalty</span> <span class="highlight-purple">Simplified.</span><br>
                            <span class="highlight-purple">Growth</span> <span class="highlight-yellow">Amplified</span>
                        </h1>
                        <p class="lead">
                            Uzimate, Your gateway to lasting customer relationships. At Uzimate, we say, 'Give us your loyalty, and we'll reward you royally.' We aim to help businesses of all sizes connect genuinely with their customers, fostering true loyalty. More than just an app, we're your digital ally, turning everyday transactions into memorable experiences.
                        </p>
                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <a href="{{ route('register') }}" class="btn btn-uzimate-yellow btn-lg">Start for Free</a>
                            <a href="#contact" class="btn btn-uzimate-purple btn-lg">Book a demo</a>
                        </div>
                        <ul class="hero-features">
                            <li><i class="fas fa-check-circle"></i> No credit card required</li>
                            <li><i class="fas fa-check-circle"></i> 7 days trial</li>
                            <li><i class="fas fa-check-circle"></i> No commitments</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="video-container fade-in-up">
                        <video autoplay muted loop playsinline style="width: 100%; height: auto; border-radius: 10px;">
                            <source src="{{ asset('frontend/assets/image/vidio/food.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="video-languages">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-video text-danger" style="font-size: 1.5rem;"></i>
                                    <span class="text-uzimate-purple fw-bold">Watch in different languages:</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="#" class="lang-btn">French</a>
                                    <a href="#" class="lang-btn">Italian</a>
                                    <a href="#" class="lang-btn">Arabic</a>
                                    <a href="#" class="lang-btn">Urdu</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Discover Uzimate Section -->
    <section id="about" class="section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-heading">
                        <span class="text-uzimate-yellow">Discover</span> <span class="text-uzimate-purple">Uzimate</span>
                    </h2>
                    <p class="lead">
                        Uzimate is a comprehensive loyalty platform designed to help businesses build stronger relationships with their customers. Our innovative approach combines cutting-edge technology with user-friendly design, making it easy for businesses of all sizes to implement and manage effective loyalty programs.
                    </p>
                    <p>
                        Whether you're a small independent retailer or a growing business, Uzimate provides the tools you need to reward your loyal customers and incentivize new ones. Our platform is designed to be flexible, scalable, and easy to use, ensuring that you can focus on what matters most - growing your business.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="mobile-mockup">
                        <img src="{{ asset('frontend/assets/image/1.webp') }}" alt="Uzimate Mobile App - Lock Screen Notification" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seamless Loyalty Section -->
    <section class="section section-alt">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2">
                    <h2 class="section-heading">
                        <span class="text-uzimate-yellow">Seamless Loyalty</span> <span class="text-danger">❤️</span>, <span class="text-uzimate-purple">Effortlessly Yours!</span>
                    </h2>
                    <p class="lead">
                        With Uzimate, managing your loyalty program has never been easier. Our intuitive platform allows you to set up, customize, and manage your loyalty program with just a few clicks. No technical expertise required - just simple, powerful tools that work for you.
                    </p>
                    <p>
                        Your customers can join, earn points, and redeem rewards effortlessly through our user-friendly mobile app. The entire process is designed to be seamless, ensuring a positive experience for both you and your customers.
                    </p>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <div class="mobile-mockup">
                        <img src="{{ asset('frontend/assets/image/1.webp') }}" alt="Uzimate Mobile App - Lock Screen Notification" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Revenue Calculator Section -->
    <section class="section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-heading">
                        <span class="text-uzimate-yellow">Let's Talk</span> <span class="text-uzimate-purple">Numbers</span>
                    </h2>
                    <div class="revenue-calculator">
                        <form id="revenueCalculator">
                            <div class="mb-3">
                                <label for="businessSector" class="form-label">Business Sector</label>
                                <select class="form-select" id="businessSector" required>
                                    <option value="">Select your sector</option>
                                    <option value="retail">Retail</option>
                                    <option value="hospitality">Hospitality</option>
                                    <option value="food">Food & Beverage</option>
                                    <option value="beauty">Beauty & Personal Care</option>
                                    <option value="fitness">Fitness & Wellness</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="annualRevenue" class="form-label">Annual Revenue (£)</label>
                                <input type="number" class="form-control" id="annualRevenue" placeholder="Enter annual revenue" required>
                            </div>
                            <div class="mb-3">
                                <label for="customerJoin" class="form-label">Customer Join Percentage (%)</label>
                                <input type="number" class="form-control" id="customerJoin" placeholder="Enter percentage" min="0" max="100" required>
                            </div>
                            <button type="submit" class="btn btn-uzimate-purple w-100">Calculate Revenue</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="revenue-display">
                        <h2 class="text-uzimate-purple" id="revenueAmount">Up to £0</h2>
                        <p class="lead">Potential Extra Annual Revenue</p>
                        <p class="text-uzimate-light">
                            Calculate how much additional revenue you could generate with Uzimate's loyalty program. Our platform helps you retain customers and increase their lifetime value.
                        </p>
                        <a href="#contact" class="btn btn-uzimate-purple btn-lg mt-3">Book a demo</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Master the 3Rs Section -->
    <section id="features" class="section section-alt">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Master the 3Rs</span> <span class="text-uzimate-purple">Rewards, Recognition, Relevance!</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Rewards</h3>
                        <p>Create attractive reward programs that motivate your customers to return. Customize rewards based on your business needs and customer preferences.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Recognition</h3>
                        <p>Make your customers feel valued with personalized recognition. Celebrate milestones and special occasions to build emotional connections.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Relevance</h3>
                        <p>Deliver targeted offers and communications that resonate with each customer. Use data insights to provide relevant experiences.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Affordable Loyalty Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Affordable Loyalty</span> <span class="text-danger">❤️</span>, <span class="text-uzimate-purple">Priceless Returns</span> <span class="text-success">📈</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Economical Engagement</h3>
                        <p>Start with a free trial and affordable pricing plans that scale with your business. No hidden fees, no surprises.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Boosted CLV</h3>
                        <p>Increase Customer Lifetime Value through effective loyalty programs that keep customers coming back for more.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Returns Beyond Revenue</h3>
                        <p>Gain valuable insights, customer data, and marketing opportunities that go beyond simple revenue generation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Digital Sidekick Section -->
    <section class="section section-alt">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Every Retailer's</span> <span>🛍️</span> <span class="text-uzimate-purple">Digital Sidekick</span> <span>💻</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-bars"></i>
                        </div>
                        <h3>Primary Independent Retailers/Tier 1</h3>
                        <p>Perfect for established retailers looking to enhance customer loyalty and drive repeat business.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-bars"></i>
                        </div>
                        <h3>Medium-Sized Retailers/Tier 2</h3>
                        <p>Ideal for growing businesses that want to compete with larger retailers through superior customer engagement.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-bars"></i>
                        </div>
                        <h3>Start-Ups</h3>
                        <p>Get started with an affordable loyalty program that helps you build a customer base from day one.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-bars"></i>
                        </div>
                        <h3>General Users</h3>
                        <p>Simple, user-friendly loyalty programs for businesses of all types and sizes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Get Ahead Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Get Ahead, Stay Ahead</span> <span>🚀</span> <span class="text-uzimate-purple">That's the Uzimate Way!</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Control</h3>
                        <p>Take full control of your loyalty program with our intuitive dashboard and management tools.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Recognition</h3>
                        <p>Recognize and reward your customers in meaningful ways that build lasting relationships.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Rewards and Offers</h3>
                        <p>Create and manage attractive rewards and offers that drive customer engagement and sales.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Customer Retention</h3>
                        <p>Keep your customers coming back with personalized experiences and meaningful rewards.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Relevance</h3>
                        <p>Deliver relevant offers and communications that resonate with each individual customer.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Enhanced Customer Experience</h3>
                        <p>Provide a seamless, enjoyable experience that makes customers want to return again and again.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section id="watch" class="section section-alt">
        <div class="container text-center">
            <h2 class="section-heading mb-4">
                <span class="text-uzimate-purple">Results Speak Louder!</span> <span>📊</span>
            </h2>
            <p class="lead mb-4">
                Join thousands of businesses that have transformed their customer relationships with Uzimate.
            </p>
            <a href="#home" class="btn btn-uzimate-outline btn-lg">Experience the Uzimate Advantage Now!</a>
        </div>
    </section>

    @push('scripts')
    <script>
        // Revenue Calculator
        document.getElementById('revenueCalculator').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const annualRevenue = parseFloat(document.getElementById('annualRevenue').value);
            const customerJoin = parseFloat(document.getElementById('customerJoin').value);
            
            if (annualRevenue && customerJoin) {
                // Simple calculation: 20% of revenue from loyal customers
                const extraRevenue = (annualRevenue * (customerJoin / 100)) * 0.20;
                document.getElementById('revenueAmount').textContent = `Up to £${extraRevenue.toLocaleString('en-GB', {maximumFractionDigits: 0})}`;
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-uzimate');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
            } else {
                navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            }
        });

        // Hover dropdown functionality
        document.querySelectorAll('.dropdown-hover').forEach(function(dropdown) {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            let hoverTimeout;
            
            function showDropdown() {
                clearTimeout(hoverTimeout);
                dropdownMenu.style.display = 'block';
                setTimeout(function() {
                    dropdownMenu.style.opacity = '1';
                    dropdownMenu.style.visibility = 'visible';
                }, 10);
            }
            
            function hideDropdown() {
                hoverTimeout = setTimeout(function() {
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.visibility = 'hidden';
                    setTimeout(function() {
                        dropdownMenu.style.display = 'none';
                    }, 200);
                }, 300); // Increased timeout to 300ms for better hover experience
            }
            
            dropdown.addEventListener('mouseenter', showDropdown);
            dropdownMenu.addEventListener('mouseenter', showDropdown);
            dropdown.addEventListener('mouseleave', hideDropdown);
            dropdownMenu.addEventListener('mouseleave', hideDropdown);
        });
    </script>
@endpush
</x-layouts.guest>

