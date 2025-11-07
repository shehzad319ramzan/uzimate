<x-layouts.guest page-title="How It Works - Uzimate">
    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 80px; padding: 80px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="hero-content fade-in-up">
                        <h1 class="display-heading">
                            <span class="text-uzimate-yellow">How Does</span> <span class="text-uzimate-purple">It Work?</span>
                        </h1>
                        <p class="lead">
                            Getting started with Uzimate is simple. Follow these easy steps to set up your loyalty program and start rewarding your customers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="video-container fade-in-up">
                        <video autoplay muted loop playsinline style="width: 100%; height: auto; border-radius: 10px;">
                            <source src="{{ asset('frontend/assets/image/vidio/food.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Steps Section -->
    <section class="section section-alt">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Simple Steps to</span> <span class="text-uzimate-purple">Get Started</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon" style="width: 80px; height: 80px; margin: 0 auto 1.5rem;">
                            <span style="font-size: 2rem; font-weight: 800; color: var(--uzimate-purple);">1</span>
                        </div>
                        <h3>Sign Up</h3>
                        <p>Create your free account in minutes. No credit card required. Start your 7-day free trial today.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon" style="width: 80px; height: 80px; margin: 0 auto 1.5rem;">
                            <span style="font-size: 2rem; font-weight: 800; color: var(--uzimate-purple);">2</span>
                        </div>
                        <h3>Set Up</h3>
                        <p>Configure your loyalty program with our easy-to-use dashboard. Customize rewards, points, and offers.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon" style="width: 80px; height: 80px; margin: 0 auto 1.5rem;">
                            <span style="font-size: 2rem; font-weight: 800; color: var(--uzimate-purple);">3</span>
                        </div>
                        <h3>Launch</h3>
                        <p>Invite your customers to join. They can sign up easily through the mobile app or web portal.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon" style="width: 80px; height: 80px; margin: 0 auto 1.5rem;">
                            <span style="font-size: 2rem; font-weight: 800; color: var(--uzimate-purple);">4</span>
                        </div>
                        <h3>Grow</h3>
                        <p>Watch your customer loyalty grow as they earn points, redeem rewards, and keep coming back.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seamless Loyalty Section -->
    <section class="section">
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
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-uzimate-yellow btn-lg me-3">Get Started</a>
                        <a href="{{ route('features') }}" class="btn btn-uzimate-purple btn-lg">View Features</a>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <div class="mobile-mockup">
                        <img src="{{ asset('frontend/assets/image/1.webp') }}" alt="Uzimate Mobile App" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Benefits Section -->
    <section class="section section-alt">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Why Choose</span> <span class="text-uzimate-purple">Uzimate?</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3>Quick Setup</h3>
                        <p>Get your loyalty program up and running in minutes, not days. Our streamlined setup process gets you started fast.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Mobile-First</h3>
                        <p>Your customers can access their loyalty program anywhere, anytime through our mobile app.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Real-Time Analytics</h3>
                        <p>Track your program's performance with real-time analytics and insights. Make data-driven decisions.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>24/7 Support</h3>
                        <p>Get help when you need it with our dedicated support team. We're here to help you succeed.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="container text-center">
            <h2 class="section-heading mb-4">
                <span class="text-uzimate-purple">Ready to Get Started?</span>
            </h2>
            <p class="lead mb-4">
                Join thousands of businesses using Uzimate to grow their customer loyalty.
            </p>
            <a href="{{ route('register') }}" class="btn btn-uzimate-yellow btn-lg me-3">Start for Free</a>
            <a href="{{ route('contact') }}" class="btn btn-uzimate-purple btn-lg">Book a Demo</a>
        </div>
    </section>
</x-layouts.guest>

