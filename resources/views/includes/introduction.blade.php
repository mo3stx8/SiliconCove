<section class="jumbotron text-center">
    <div style="margin-top: 60px"></div>

    <div class="container">

        <!-- Single Clean Carousel -->
        <div id="welcomeCarousel" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="1500">

            <!-- Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#welcomeCarousel" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#welcomeCarousel" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#welcomeCarousel" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>

            <!-- Slides -->
            <div class="carousel-inner rounded shadow" style="max-height: 500px; overflow: hidden;">
                <div class="carousel-item active">
                    <img src="{{ asset('images/001.jpg') }}" class="d-block w-100"
                        style="height: 450px; object-fit: cover;" alt="SiliconCove Products">
                    <div class="carousel-caption bg-dark bg-opacity-50 rounded p-2">
                        <h5 class="text-white fw-bold">SiliconCove Products</h5>
                    </div>
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('images/004.jpg') }}" class="d-block w-100"
                        style="height: 450px; object-fit: cover;" alt="Shop Fast. Shop Safe. Shop Smart.">
                    <div class="carousel-caption bg-dark bg-opacity-50 rounded p-2">
                        <h5 class="text-white fw-bold">Shop Fast. Shop Safe. Shop Smart.</h5>
                    </div>
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('images/005.jpg') }}" class="d-block w-100"
                        style="height: 450px; object-fit: cover;" alt="From Us to You, Right at Your Door.">
                    <div class="carousel-caption bg-dark bg-opacity-50 rounded p-2">
                        <h5 class="text-white fw-bold">From Us to You, Right at Your Door.</h5>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
            </button>

        </div>

        <!-- Welcome Message -->
        <h1 class="jumbotron-heading text-primary fw-bold">Welcome to SiliconCove</h1>
        <p class="lead text-muted">
            Welcome to <strong>SiliconCove</strong> Your trusted source for authentic SiliconCove products.
            Shop confidently with reliable service, curated selections, and smooth, hassle-free delivery every time.
            <strong>Thank you very much for your patronage!</strong> 🛒✨
        </p>

    </div>
</section>
