<!-- Header/Navigation -->
<nav class="navbar navbar-expand-lg navbar-uzimate fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('welcome') }}">
            <x-logo />
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About</a>
                </li>
                <li class="nav-item dropdown dropdown-hover">
                    <a class="nav-link dropdown-toggle" href="#" id="retailDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Retail Sectors
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="retailDropdown">
                        <li class="dropdown-header">
                            <h6 class="mb-0 fw-bold text-uzimate-purple">Retail Sectors</h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="list-unstyled mb-0">
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Beauty & Personal Care</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Food & Hospitality</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Healthcare Providers</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Recreation & Leisure</a></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled mb-0">
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Fitness & Wellness</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Wholesale & Suppliers</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Bakeries & Confectioneries</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Cafes & Dessert Parlors</a></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled mb-0">
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Retail Outlets</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Automotive Services</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Fashion & Jewelry</a></li>
                                    <li><a class="dropdown-item text-uzimate-purple" href="#">Takeaway Food Outlet</a></li>
                                </ul>
                            </div>
                        </div>
                    </ul>
                </li>
                <li class="nav-item dropdown dropdown-hover">
                    <a class="nav-link dropdown-toggle" href="#" id="featuresDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Features & Benefits
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="featuresDropdown">
                        <li><a class="dropdown-item text-uzimate-purple" href="#features">All Features</a></li>
                        <li><a class="dropdown-item text-uzimate-purple" href="#how-it-works">How It Works</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown dropdown-hover">
                    <a class="nav-link dropdown-toggle" href="#" id="howItWorksDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        How Does It Work?
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="howItWorksDropdown">
                        <li><a class="dropdown-item text-uzimate-purple" href="#how-it-works">Overview</a></li>
                        <li><a class="dropdown-item text-uzimate-purple" href="#watch">Watch Video</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown dropdown-hover">
                    <a class="nav-link dropdown-toggle" href="#" id="resourcesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Resources
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="resourcesDropdown">
                        <li><a class="dropdown-item text-uzimate-purple" href="#">Documentation</a></li>
                        <li><a class="dropdown-item text-uzimate-purple" href="#">FAQ</a></li>
                        <li><a class="dropdown-item text-uzimate-purple" href="#">Support</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-3 ms-auto">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-inline-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://flagcdn.com/w20/gb.png" alt="UK Flag" class="me-1" style="width: 20px; height: auto;">
                        English
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/fr.png" alt="French Flag" class="me-2" style="width: 20px; height: auto;">French</a></li>
                        <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/it.png" alt="Italian Flag" class="me-2" style="width: 20px; height: auto;">Italian</a></li>
                        <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/sa.png" alt="Arabic Flag" class="me-2" style="width: 20px; height: auto;">Arabic</a></li>
                        <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/pk.png" alt="Urdu Flag" class="me-2" style="width: 20px; height: auto;">Urdu</a></li>
                    </ul>
                </div>
                <a href="{{ route('login') }}" class="nav-link d-inline-flex align-items-center">
                    <i class="fas fa-lock me-2"></i>Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-uzimate-yellow btn-sm d-inline-flex align-items-center">Start for free</a>
            </div>
        </div>
    </div>
</nav>
