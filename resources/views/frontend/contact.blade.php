<x-layouts.guest page-title="Contact Us - Uzimate">
    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 80px; padding: 80px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="hero-content fade-in-up">
                        <h1 class="display-heading">
                            <span class="text-uzimate-yellow">Get in</span> <span class="text-uzimate-purple">Touch</span>
                        </h1>
                        <p class="lead">
                            Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm border-0" style="border-radius: 15px; padding: 40px;">
                        <h2 class="section-heading text-center mb-4">
                            <span class="text-uzimate-yellow">Contact</span> <span class="text-uzimate-purple">Us</span>
                        </h2>
                        
                        <form id="contactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter subject" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Enter your message" required></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-uzimate-purple btn-lg px-5">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information Section -->
    <section class="section section-alt">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Email Us</h3>
                        <p>info@uzimate.com</p>
                        <p>support@uzimate.com</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Call Us</h3>
                        <p>+1 (555) 123-4567</p>
                        <p>Mon - Fri, 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-card-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Visit Us</h3>
                        <p>123 Business Street</p>
                        <p>City, State 12345</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Contact form submission
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you can add form submission logic
            // For now, just show an alert
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });
    </script>
    @endpush
</x-layouts.guest>

