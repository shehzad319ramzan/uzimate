<x-layouts.guest page-title="Watch Video - Uzimate">
    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 80px; padding: 80px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="hero-content fade-in-up">
                        <h1 class="display-heading">
                            <span class="text-uzimate-yellow">Watch</span> <span class="text-uzimate-purple">Video</span>
                        </h1>
                        <p class="lead">
                            Discover how Uzimate can transform your business with our comprehensive video guide.
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
                    <div class="video-container fade-in-up" style="margin-bottom: 2rem;">
                        <video controls autoplay muted loop playsinline style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);">
                            <source src="{{ asset('frontend/assets/image/vidio/food.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    
                    <div class="video-languages text-center">
                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-video text-danger" style="font-size: 1.5rem;"></i>
                                <span class="text-uzimate-purple fw-bold">Watch in different languages:</span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap justify-content-center">
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
    </section>

    <!-- Video Description Section -->
    <section class="section section-alt">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-heading text-center mb-4">
                        <span class="text-uzimate-yellow">About This</span> <span class="text-uzimate-purple">Video</span>
                    </h2>
                    <p class="lead text-center mb-4">
                        This video demonstrates how Uzimate works and how it can help your business grow. Learn about our features, benefits, and how easy it is to get started.
                    </p>
                    <div class="row g-4 mt-4">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-card-icon">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                                <h3>Easy Setup</h3>
                                <p>See how quickly you can set up your loyalty program in just a few minutes.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3>Customer Experience</h3>
                                <p>Learn how your customers can easily join and use the loyalty program.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-card-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3>Analytics & Insights</h3>
                                <p>Discover how to track your program's performance and gain valuable insights.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-card-icon">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <h3>Rewards Management</h3>
                                <p>See how to create and manage attractive rewards for your customers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Content Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-heading text-center mb-5">
                <span class="text-uzimate-yellow">Learn More</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3>How It Works</h3>
                        <p>Learn more about how Uzimate works and how to get started.</p>
                        <a href="{{ route('how-it-works') }}" class="btn btn-uzimate-purple btn-sm mt-2">Learn More</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Features & Benefits</h3>
                        <p>Discover all the features and benefits that Uzimate offers.</p>
                        <a href="{{ route('features') }}" class="btn btn-uzimate-purple btn-sm mt-2">View Features</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h3>Resources</h3>
                        <p>Find documentation, FAQs, and support resources.</p>
                        <a href="{{ route('resources') }}" class="btn btn-uzimate-purple btn-sm mt-2">View Resources</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section section-alt">
        <div class="container text-center">
            <h2 class="section-heading mb-4">
                <span class="text-uzimate-purple">Ready to Get Started?</span>
            </h2>
            <p class="lead mb-4">
                Start your free 7-day trial and see how Uzimate can transform your business.
            </p>
            <a href="{{ route('register') }}" class="btn btn-uzimate-yellow btn-lg me-3">Start for Free</a>
            <a href="{{ route('contact') }}" class="btn btn-uzimate-purple btn-lg">Book a Demo</a>
        </div>
    </section>
</x-layouts.guest>

