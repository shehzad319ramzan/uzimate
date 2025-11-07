<x-layouts.guest page-title="{{ $sectorName }} - Uzimate">
    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 80px; padding: 80px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="hero-content fade-in-up">
                        <h1 class="display-heading">
                            <span class="text-uzimate-yellow">{{ $sectorName }}</span>
                        </h1>
                        <p class="lead">
                            Discover how Uzimate can transform your {{ strtolower($sectorName) }} business with our powerful loyalty platform.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sector Content Section -->
    <section class="section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-heading">
                        <span class="text-uzimate-yellow">Perfect for</span> <span class="text-uzimate-purple">{{ $sectorName }}</span>
                    </h2>
                    <p class="lead">
                        Uzimate is specifically designed to help {{ strtolower($sectorName) }} businesses build stronger customer relationships and increase customer loyalty. Our platform provides the tools you need to reward your customers and grow your business.
                    </p>
                    <p>
                        Whether you're a small independent business or a growing enterprise, Uzimate provides flexible, scalable solutions that work for businesses of all sizes in the {{ strtolower($sectorName) }} sector.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-uzimate-yellow btn-lg me-3">Start for Free</a>
                        <a href="{{ route('contact') }}" class="btn btn-uzimate-purple btn-lg">Book a Demo</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mobile-mockup">
                        <img src="{{ asset('frontend/assets/image/1.webp') }}" alt="{{ $sectorName }} - Uzimate" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section section-alt">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Why Choose</span> <span class="text-uzimate-purple">Uzimate</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Customized Rewards</h3>
                        <p>Create reward programs tailored specifically for your {{ strtolower($sectorName) }} business and customer base.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Increase Revenue</h3>
                        <p>Boost customer retention and lifetime value with our proven loyalty strategies.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Customer Insights</h3>
                        <p>Gain valuable insights into your customer behavior and preferences.</p>
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
                Join thousands of {{ strtolower($sectorName) }} businesses using Uzimate to grow their customer base.
            </p>
            <a href="{{ route('register') }}" class="btn btn-uzimate-yellow btn-lg me-3">Start for Free</a>
            <a href="{{ route('contact') }}" class="btn btn-uzimate-purple btn-lg">Contact Us</a>
        </div>
    </section>
</x-layouts.guest>

