<!-- Bootstrap 5.3.3 -->
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<!-- Contact Component CSS -->
<link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/contacts/contact-1/assets/css/contact-1.css">

<!-- Laravel Assets -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{ asset('css/styles.css') }}" rel="stylesheet">
<!-- website icon -->
<link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/png">
<link rel="shortcut icon" href="{{ asset('images/siliconcovelogo.png') }}">

<title>
    Silicon Cove
</title>

@include('includes.navbar')
@include('includes.introduction')

<!-- ✅ Breadcrumb -->
<div class="container d-none">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mt-3">
            <li class="breadcrumb-item active" aria-current="page">Home</li>
        </ol>
    </nav>
</div>

<!-- POS Layout -->
<div class="pos-wrapper" style="margin-top:3rem;">
    <div class="container mt-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <!-- Top Navigation -->
    <nav class="pos-topbar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4 col-6">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search products..."
                            value="{{ request('query') }}">
                        <button class="btn btn-light" type="button" id="searchButton"><i
                                class="fa fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-4 d-none d-md-block text-center">
                    <h4 class="mb-0">Silicon Cove</h4>
                </div>
                <div class="col-md-4 col-6 text-end">
                    <a href="javascript:void(0);" class="text-decoration-none"
                        style="{{ auth()->guard('admin')->check() ? 'pointer-events: none; cursor: default; color: #6c757d; text-decoration: none;' : 'cursor: pointer' }}">
                        <button class="btn btn-primary cart-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="badge bg-danger">0</span>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="pos-content">
        <!-- Categories Sidebar -->
        <div class="categories-sidebar">
            <div class="category-list">
                <button class="category-item active" onclick="performSearch('')">
                    <i class="fas fa-box-open me-2"></i> All Items
                </button>
                <button class="category-item" onclick="performSearch('Processors')">
                    <i class="fas fa-microchip"></i> Processors
                </button>
                <button class="category-item" onclick="performSearch('Motherboards')">
                    <i class="fas fa-project-diagram"></i> Motherboards
                </button>
                <button class="category-item" onclick="performSearch('Graphics Cards')">
                    <i class="fas fa-desktop"></i> Graphics Cards
                </button>
                <button class="category-item" onclick="performSearch('Memory & Storage')">
                    <i class="fas fa-memory"></i> Memory & Storage
                </button>
                <button class="category-item" onclick="performSearch('Power & Cooling')">
                    <i class="fas fa-fan"></i> Power & Cooling
                </button>
                <button class="category-item" onclick="performSearch('Peripherals & Accessories')">
                    <i class="fas fa-keyboard"></i> Peripherals & Accessories
                </button>
                <button class="category-item" onclick="performSearch('Cases & Builds')">
                    <i class="fas fa-server"></i> Cases & Builds
                </button>
                <button class="category-item" onclick="performSearch('Mod Zone')">
                    <i class="fas fa-tools"></i> Mod Zone
                </button>
                {{-- @foreach ($latestProducts as $latest) --}}
                {{-- <button class="category-item">{{ $latest->name }}</button> --}}
                {{-- @endforeach --}}
            </div>
        </div>
        <!-- Products Grid -->
        <div class="products-grid">
            @forelse($products as $product)
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        <div class="stock-badge {{ $product->stock > 0 ? 'in-stock' : 'out-stock' }}">
                            {{ $product->stock > 0 ? $product->stock . ' left' : 'Out of Stock' }}
                        </div>
                    </div>
                    <div class="product-info">
                        <div style="height: 4rem;">
                            <h5 class="product-name">{{ $product->name }}</h5>
                            <div class="product-price">${{ number_format($product->price, 2) }}</div>
                        </div>
                        <button class="btn btn-primary btn-add-cart mt-3 {{ $product->stock <= 0 ? 'disabled' : '' }}"
                            data-bs-toggle="modal" data-bs-target="#addToCartModal" data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}" data-image="{{ asset('storage/' . $product->image) }}"
                            data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                            <i class="fa fa-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            @empty
                <div class="no-products">
                    <i class="fa fa-exclamation-circle"></i>
                    <p>No products found</p>
                </div>
            @endforelse
        </div>

        <!-- Cart Sidebar -->
        <div class="cart-sidebar">
            <div class="cart-header">
                <h5><i class="fa fa-shopping-cart me-2"></i>Current Order</h5>
                <button class="btn-close cart-toggle d-md-none"></button>
            </div>
            <div class="cart-items" id="cartItemsContainer">
                <!-- Cart items will be dynamically loaded here -->
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total:</span>
                    <span class="price" id="cartTotal">$0.00</span>
                </div>
                <form id="buyNowForm" method="POST">
                    @csrf
                    <input type="hidden" name="selected_product_items" id="selectedItemsInput">
                    <input type="hidden" name="selected_product_quantities" id="quantitiesInput">
                    <button type="button" class="btn btn-success btn-lg w-100" id="checkoutBtn"
                        onclick="handleCheckoutClick()">
                        <i class="fa fa-check-circle me-2"></i>Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>


    <nav data-total-pages="{{ $products->lastPage() }}">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            <li class="page-item prev {{ $products->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="#" data-page="{{ $products->currentPage() - 1 }}"
                    tabindex="-1">Previous</a>
            </li>

            {{-- Page Links --}}
            @for ($page = 1; $page <= $products->lastPage(); $page++)
                <li class="page-item {{ $products->currentPage() == $page ? 'active' : '' }}">
                    <a class="page-link" href="#" data-page="{{ $page }}">{{ $page }}</a>
                </li>
            @endfor

            {{-- Next Page Link --}}
            <li class="page-item next {{ $products->currentPage() == $products->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" href="#" data-page="{{ $products->currentPage() + 1 }}">Next</a>
            </li>
        </ul>
    </nav>


</div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="modalProductImage" src="" class="img-fluid mb-3" style="max-height: 200px;">
                </div>
                <h5 id="modalProductName"></h5>
                <p id="modalProductPrice" class="text-muted"></p>
                <p id="modalProductStock" class="text-muted"></p> <!-- ✅ Added stock display -->

                <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" id="modalProductId">

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="modalQuantity" class="form-control"
                            value="1" min="1" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- ✅ Footer -->
@include('includes.footer')

<!-- Add this new modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-3">
                    <button class="btn btn-primary btn-lg" onclick="handleCheckout('save')">
                        <i class="fa fa-shopping-cart me-2"></i>Save to Cart
                    </button>
                    <button class="btn btn-success btn-lg" onclick="handleCheckout('deliver')">
                        <i class="fa fa-truck me-2"></i>Proceed to Delivery
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add new admin checkout modal -->
<div class="modal fade" id="adminCheckoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-cash-register me-2"></i>Process Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Order Summary -->
                    <div class="col-md-7 border-end">
                        <h6 class="mb-3">Order Summary</h6>
                        <div class="order-items-container" style="max-height: 300px; overflow-y: auto;">
                            <!-- Items will be listed here dynamically -->
                        </div>
                        <div class="border-top mt-3 pt-3">
                            <h5 class="d-flex justify-content-between">
                                <span>Total Amount:</span>
                                <span class="text-primary" id="modalTotalAmount">$0.00</span>
                            </h5>
                        </div>
                    </div>

                    <!-- Payment Processing -->
                    <div class="col-md-5">
                        <h6 class="mb-3">Payment Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Amount Due</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="amountDue" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cash Received</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="cashReceived"
                                    onInput="calculateChange()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Change</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="changeAmount" readonly>
                            </div>
                        </div>
                        <div class="alert alert-info" role="alert">
                            <i class="fa fa-info-circle me-2"></i>
                            Please verify the amount before completing the transaction.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="printReceiptBtn" disabled>
                    <i class="fa fa-print me-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add hidden receipt template -->
<div id="receiptTemplate" style="display: none;">
    <div class="receipt-container"
        style="width: 300px; padding: 20px; font-family: 'Courier New', Courier, monospace;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h3>Silicon Cove</h3>
            <p>Official Receipt</p>
            <p>Date: <span id="receiptDate"></span></p>
            <p>Transaction #: <span id="receiptTransactionNo"></span></p>
        </div>
        <div id="receiptItems" style="margin-bottom: 20px;"></div>
        <div style="border-top: 1px dashed #000; padding-top: 10px;">
            <p><strong>Total Amount:</strong> $<span id="receiptTotal"></span></p>
            <p><strong>Cash Received:</strong> $<span id="receiptCashReceived"></span></p>
            <p><strong>Change:</strong> $<span id="receiptChange"></span></p>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <p>Thank you for shopping!</p>
        </div>
    </div>
</div>

<script>
    let checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    let selectedCategory = '';

    function showCheckoutModal() {
        if (cartItems.length === 0) {
            alert('Your cart is empty!');
            return;
        }
        checkoutModal.show();
    }

    function handleCheckout(action) {
        const form = document.getElementById('buyNowForm');
        const productIds = [];
        const quantities = [];

        cartItems.forEach(item => {
            productIds.push(item.id);
            quantities.push(item.quantity);
        });

        document.getElementById('selectedItemsInput').value = JSON.stringify(productIds);
        document.getElementById('quantitiesInput').value = JSON.stringify(quantities);

        if (action === 'save') {
            form.action = "{{ route('cart.add') }}";
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'bulk_add';
            input.value = 'true';
            form.appendChild(input);
        } else {
            form.action = "{{ route('cart.buy-now', ['product' => 'true']) }}";
            form.method = 'POST';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = csrfToken;
            form.appendChild(input);
        }

        checkoutModal.hide();
        form.submit();
    }

    // Toggle cart sidebar on mobile
    const cartToggles = document.querySelectorAll('.cart-toggle');
    const cartSidebar = document.querySelector('.cart-sidebar');

    cartToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            cartSidebar.classList.toggle('active');
        });
    });

    // Close cart when clicking outside
    document.addEventListener('click', (e) => {
        if (!cartSidebar.contains(e.target) &&
            !e.target.classList.contains('cart-toggle')) {
            cartSidebar.classList.remove('active');
        }
    });

    // Handle add to cart button clicks
    document.querySelectorAll(".btn-add-cart").forEach(button => {
        button.addEventListener("click", function(e) {
            e.preventDefault();

            const stock = parseInt(this.getAttribute("data-stock"));
            const productId = this.getAttribute("data-id");
            const productName = this.getAttribute("data-name");
            const productPrice = parseFloat(this.getAttribute("data-price"));
            const productImage = this.getAttribute("data-image");

            // Set modal content
            document.getElementById("modalProductImage").src = productImage;
            document.getElementById("modalProductName").textContent = productName;
            document.getElementById("modalProductPrice").textContent = "Price: $" + productPrice
                .toFixed(2);
            document.getElementById("modalProductStock").textContent = "Stock: " + stock;
            document.getElementById("modalProductId").value = productId;
            document.getElementById("modalQuantity").value = "1";

            // Handle stock logic
            const quantityInput = document.getElementById("modalQuantity");
            const addToCartButton = document.querySelector("#addToCartForm button[type='submit']");

            if (stock === 0) {
                quantityInput.disabled = true;
                addToCartButton.disabled = true;
                quantityInput.value = 0;
            } else {
                quantityInput.disabled = false;
                addToCartButton.disabled = false;
            }

            // Show modal
            addToCartModal.show();
        });
    });

    // Handle quantity input changes
    document.getElementById("modalQuantity").addEventListener("input", function() {
        const stock = parseInt(document.getElementById("modalProductStock").textContent.split(": ")[1]);
        const enteredQty = parseInt(this.value) || 0;
        const addToCartButton = document.querySelector("#addToCartForm button[type='submit']");

        if (enteredQty > stock || enteredQty <= 0) {
            addToCartButton.disabled = true;
            this.classList.add("is-invalid");
        } else {
            addToCartButton.disabled = false;
            this.classList.remove("is-invalid");
        }
    });

    // Cart management functions
    let cartItems = [];

    function addToCart(item) {
        // Check temporary stock before adding
        const currentStock = tempStocks.get(item.id) || 0;
        if (currentStock < item.quantity) {
            alert('Not enough stock available!');
            return false;
        }

        // Calculate exact new stock
        const newStock = currentStock - item.quantity;

        // Update temporary stock with exact calculation
        tempStocks.set(item.id, newStock);

        // Update display with correct stock number
        updateProductDisplay(item.id, newStock);

        // Add to cart with exact quantities
        const existingItem = cartItems.find(i => i.id === item.id);
        if (existingItem) {
            existingItem.quantity += item.quantity;
            existingItem.total = parseFloat((existingItem.quantity * existingItem.price).toFixed(2));
        } else {
            cartItems.push({
                ...item,
                quantity: parseInt(item.quantity),
                total: parseFloat((item.price * item.quantity).toFixed(2))
            });
        }

        updateCartUI();
        return true;
    }

    function removeFromCart(productId) {
        console.log('Removing from cart:', productId);
        const item = cartItems.find(item => item.id === productId);
        console.log(item);
        if (item) {
            manageStock(productId, item.quantity, false);
        }
        cartItems = cartItems.filter(item => item.id !== productId);
        updateCartUI();
    }

    function updateCartUI() {
        const container = document.getElementById('cartItemsContainer');
        // Ensure proper number formatting for total
        const total = cartItems.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);

        // Update cart badge
        document.querySelector('.cart-toggle .badge').textContent = cartItems.length;

        // Update cart items with proper number formatting
        container.innerHTML = cartItems.map(item => `
        <div class="cart-item border-bottom py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">${item.name}</h6>
                    <small class="text-muted">
                        ${item.quantity} × $${parseFloat(item.price).toFixed(2)}
                    </small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">$${parseFloat(item.total).toFixed(2)}</span>
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart('${item.id}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('') || '<div class="text-center text-muted py-3">Cart is empty</div>';

        // Update total with proper formatting
        document.getElementById('cartTotal').textContent = `$${total.toFixed(2)}`;

        // Toggle checkout button
        document.getElementById('checkoutBtn').disabled = cartItems.length === 0;
    }

    // Initialize empty cart
    updateCartUI();

    // Form submission handler
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const productId = document.getElementById('modalProductId').value;
        const quantity = parseInt(document.getElementById('modalQuantity').value);
        const name = document.getElementById('modalProductName').textContent;
        // Get price from the data attribute instead of text content
        const button = document.querySelector(`[data-id="${productId}"]`);
        const price = parseFloat(button.getAttribute('data-price'));

        // Add to cart only if we have valid numbers
        if (!isNaN(price) && !isNaN(quantity)) {
            addToCart({
                id: productId,
                name: name,
                quantity: quantity,
                price: price,
                total: price * quantity
            });
        }

        // Close modal using Bootstrap's hide method
        const modal = bootstrap.Modal.getInstance(document.getElementById('addToCartModal'));
        if (modal) {
            modal.hide();
        }

        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    // Handle modal hidden event to clean up
    document.getElementById('addToCartModal').addEventListener('hidden.bs.modal', function() {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    });

    // Initialize modal once and store the instance
    const modalEl = document.getElementById('addToCartModal');
    const addToCartModal = new bootstrap.Modal(modalEl, {
        backdrop: 'static' // Prevent closing by clicking outside
    });

    // Add click handlers to all add-to-cart buttons
    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Get product data
            const data = this.dataset;

            // Update modal content
            document.getElementById('modalProductImage').src = data.image;
            document.getElementById('modalProductName').textContent = data.name;
            document.getElementById('modalProductPrice').textContent =
                `Price: $${parseFloat(data.price).toFixed(2)}`;
            document.getElementById('modalProductStock').textContent = `Stock: ${data.stock}`;
            document.getElementById('modalProductId').value = data.id;
            document.getElementById('modalQuantity').value = "1";

            // Show modal immediately
            addToCartModal.show();
        });
    });

    // Handle modal cleanup
    modalEl.addEventListener('hidden.bs.modal', function() {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
    // Stock management functions
    const productStocks = new Map();
    const tempStocks = new Map();

    // Initialize stock tracking
    document.querySelectorAll('.btn-add-cart').forEach(button => {
        const id = button.getAttribute('data-id');
        const stock = parseInt(button.getAttribute('data-stock'));
        productStocks.set(id, stock);
        tempStocks.set(id, stock);
    });

    function updateProductDisplay(productId, tempStock) {
        const button = document.querySelector(`.btn-add-cart[data-id="${productId}"]`);
        if (!button) return;

        const stockBadge = button.closest('.product-card').querySelector('.stock-badge');
        stockBadge.textContent = tempStock > 0 ? `${tempStock} left` : 'Out of Stock';
        stockBadge.className = `stock-badge ${tempStock > 0 ? 'in-stock' : 'out-stock'}`;
        button.classList.toggle('disabled', tempStock <= 0);
        button.setAttribute('data-stock', tempStock);
    }

    function manageStock(productId, quantity, isAdd = true) {
        const currentStock = tempStocks.get(productId);
        if (typeof currentStock === 'undefined') return false;

        const newStock = isAdd ? currentStock - quantity : currentStock + quantity;

        if (isAdd && newStock < 0) return false;

        tempStocks.set(productId, newStock);
        updateProductDisplay(productId, newStock);
        return true;
    }

    function prepareCheckout(event) {
        event.preventDefault();

        // Extract product IDs and quantities from cart items
        const productIds = [];
        const quantities = [];

        cartItems.forEach(item => {
            productIds.push(item.id);
            quantities.push(item.quantity);
        });

        // Set the input values
        document.getElementById('selectedItemsInput').value = JSON.stringify(productIds);
        document.getElementById('quantitiesInput').value = JSON.stringify(quantities);

        console.log('Selected Items:', productIds);
        console.log('Quantities:', quantities);

        // Submit the form
        document.getElementById('buyNowForm').submit();
    }

    document.getElementById('searchButton').addEventListener('click', performSearch);
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    const globalStockMap = new Map();

    function performSearch(category = '', page = 1) {
        selectedCategory = category;
        const query = document.getElementById('searchInput').value;
        const productsGrid = document.querySelector('.products-grid');

        // Toggle active class
        document.querySelectorAll('.category-item').forEach(btn => btn.classList.remove('active'));
        const clickedBtn = Array.from(document.querySelectorAll('.category-item')).find(btn =>
            btn.getAttribute('onclick')?.includes(`'${category}'`)
        );
        if (clickedBtn) {
            clickedBtn.classList.add('active');
        }

        // Show loading template
        const loadingTemplate = document.getElementById('loadingTemplate');
        productsGrid.innerHTML = loadingTemplate.innerHTML;

        fetch(`{{ route('product.search') }}?query=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}&page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const newProducts = tempDiv.querySelector('.products-grid').innerHTML;

                // Small delay to prevent flickering on fast connections
                setTimeout(() => {
                    productsGrid.innerHTML = newProducts;

                    // After loading new products, update their stock display based on tracked values
                    document.querySelectorAll('.btn-add-cart').forEach(button => {
                        const id = button.getAttribute('data-id');
                        const currentTempStock = tempStocks.get(id);

                        if (typeof currentTempStock !== 'undefined') {
                            updateProductDisplay(id, currentTempStock);
                        } else {
                            const initialStock = parseInt(button.getAttribute('data-stock'));
                            productStocks.set(id, initialStock);
                            tempStocks.set(id, initialStock);
                        }
                    });

                    attachProductCardListeners();
                }, 300);
            })
            .catch(error => {
                console.error('Error:', error);
                productsGrid.innerHTML = `
                        <div class="text-center p-5 text-danger">
                            <i class="fa fa-exclamation-circle fa-3x mb-3"></i>
                            <p>Error loading products. Please try again.</p>
                        </div>
                    `;
            });
    }

    // Update attachProductCardListeners to not reinitialize stock values
    function attachProductCardListeners() {
        document.querySelectorAll('.btn-add-cart').forEach(button => {
            // Only attach click handlers, don't reinitialize stock
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const data = this.dataset;
                document.getElementById('modalProductImage').src = data.image;
                document.getElementById('modalProductName').textContent = data.name;
                document.getElementById('modalProductPrice').textContent =
                    `Price: $${parseFloat(data.price).toFixed(2)}`;
                document.getElementById('modalProductStock').textContent =
                    `Stock: ${tempStocks.get(data.id) || data.stock}`;
                document.getElementById('modalProductId').value = data.id;
                document.getElementById('modalQuantity').value = "1";

                const addToCartModal = new bootstrap.Modal(document.getElementById('addToCartModal'));
                addToCartModal.show();
            });
        });
    }

    const handlePaginationClick = function(e) {
        e.preventDefault();
        // Get total pages from a hidden span or nav
        const totalPagesEl = document.querySelector('[data-total-pages]');
        const totalPages = Number(totalPagesEl?.getAttribute('data-total-pages')) || 1;

        const clickedPage = Number(this.getAttribute('data-page'));
        if (isNaN(clickedPage) || this.closest('li').classList.contains('disabled')) return;

        // Update active state
        document.querySelectorAll('.pagination .page-item').forEach(item => {
            const link = item.querySelector('[data-page]');
            if (link) {
                const page = Number(link.getAttribute('data-page'));

                // Exclude Prev/Next by checking if the page is a valid number between 1 and totalPages
                if (!isNaN(page) && page >= 1 && page <= totalPages) {
                    item.classList.toggle('active', page === clickedPage);
                } else {
                    item.classList.remove('active');
                }
            }
        });

        // Update disabled state for Previous and Next buttons
        const prevBtn = document.querySelector('.pagination .page-item.prev');
        const nextBtn = document.querySelector('.pagination .page-item.next');

        if (prevBtn && nextBtn) {
            prevBtn.classList.toggle('disabled', clickedPage === 1);
            nextBtn.classList.toggle('disabled', clickedPage === totalPages);
        }

        // Perform AJAX
        performSearch(selectedCategory, clickedPage);
    };


    // Attach event listeners
    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', handlePaginationClick);
    });
</script>

<script>
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>

<!-- Add this before the closing body tag -->
<template id="loadingTemplate">
    <div class="text-center p-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Searching products...</p>
    </div>
</template>

<!-- Add this to your existing script section -->
<script>
    const adminCheckoutModal = new bootstrap.Modal(document.getElementById('adminCheckoutModal'));

    function handleCheckoutClick() {
        if (cartItems.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        @if (Auth::guard('admin')->check())
            showAdminCheckoutModal();
        @else
            showCheckoutModal();
        @endif
    }

    function showAdminCheckoutModal() {
        // Calculate total
        const total = cartItems.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);

        // Update order items in modal
        const orderItemsContainer = document.querySelector('.order-items-container');
        orderItemsContainer.innerHTML = cartItems.map(item => `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h6 class="mb-0">${item.name}</h6>
                <small class="text-muted">${item.quantity} × $${parseFloat(item.price).toFixed(2)}</small>
            </div>
            <span class="text-end">$${parseFloat(item.total).toFixed(2)}</span>
        </div>
    `).join('');

        // Set initial values
        document.getElementById('modalTotalAmount').textContent = `$${total.toFixed(2)}`;
        document.getElementById('amountDue').value = total.toFixed(2);
        document.getElementById('cashReceived').value = '';
        document.getElementById('changeAmount').value = '';

        adminCheckoutModal.show();
    }

    function calculateChange() {
        const amountDue = parseFloat(document.getElementById('amountDue').value);
        const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
        const change = cashReceived - amountDue;
        const changeField = document.getElementById('changeAmount');
        const printBtn = document.getElementById('printReceiptBtn');

        if (change >= 0) {
            changeField.value = change.toFixed(2);
            changeField.classList.remove('is-invalid');
            changeField.classList.add('is-valid');
            printBtn.disabled = false;
        } else {
            changeField.value = 'Insufficient amount';
            changeField.classList.remove('is-valid');
            changeField.classList.add('is-invalid');
            printBtn.disabled = true;
        }
    }

    // Add complete transaction handler
    document.getElementById('printReceiptBtn').addEventListener('click', function() {
        const receiptDate = new Date().toLocaleString();
        const transactionNo = Math.random().toString(36).substr(2, 9).toUpperCase();
        const total = parseFloat(document.getElementById('amountDue').value);
        const cashReceived = parseFloat(document.getElementById('cashReceived').value);
        const change = parseFloat(document.getElementById('changeAmount').value);

        // Update receipt template with transaction details
        document.getElementById('receiptDate').textContent = receiptDate;
        document.getElementById('receiptTransactionNo').textContent = transactionNo;
        document.getElementById('receiptTotal').textContent = total.toFixed(2);
        document.getElementById('receiptCashReceived').textContent = cashReceived.toFixed(2);
        document.getElementById('receiptChange').textContent = change.toFixed(2);

        // Generate items list
        const itemsHtml = cartItems.map(item => `
        <div style="margin-bottom: 10px;">
            <div>${item.name}</div>
            <div>${item.quantity} × $${parseFloat(item.price).toFixed(2)}</div>
            <div style="text-align: right;">$${parseFloat(item.total).toFixed(2)}</div>
        </div>
    `).join('');
        document.getElementById('receiptItems').innerHTML = itemsHtml;

        // Submit the form after printing
        const form = document.getElementById('buyNowForm');
        const productIds = [];
        const quantities = [];

        cartItems.forEach(item => {
            productIds.push(item.id);
            quantities.push(item.quantity);
        });

        document.getElementById('selectedItemsInput').value = JSON.stringify(productIds);
        document.getElementById('quantitiesInput').value = JSON.stringify(quantities);

        // Add payment details to form
        const cashReceivedInput = document.createElement('input');
        cashReceivedInput.type = 'hidden';
        cashReceivedInput.name = 'cash_received';
        cashReceivedInput.value = cashReceived;
        form.appendChild(cashReceivedInput);

        const changeInput = document.createElement('input');
        changeInput.type = 'hidden';
        changeInput.name = 'change_amount';
        changeInput.value = change;
        form.appendChild(changeInput);

        const transactionNoInput = document.createElement('input');
        transactionNoInput.type = 'hidden';
        transactionNoInput.name = 'transaction_no';
        transactionNoInput.value = transactionNo;
        form.appendChild(transactionNoInput);

        form.action = "{{ route('cart.buy-now', ['product' => 'true']) }}";
        form.method = 'POST';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;
        form.appendChild(tokenInput);
        form.submit();

        adminCheckoutModal.hide();

        // Print the receipt
        const receiptWindow = window.open('', '_blank');
        receiptWindow.document.write(`
        <html>
            <head>
                <title>Receipt - ${transactionNo}</title>
            </head>
            <body>
                ${document.getElementById('receiptTemplate').innerHTML}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    };
                <\/script>
            </body>
        </html>
    `);
        receiptWindow.document.close();
    });
</script>

<!-- Add backdrop div -->
<div class="sidebar-backdrop"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryToggle = document.getElementById('categoryToggle');
        const cartToggles = document.querySelectorAll('.cart-toggle');
        const categorySidebar = document.querySelector('.categories-sidebar');
        const cartSidebar = document.querySelector('.cart-sidebar');
        const backdrop = document.querySelector('.sidebar-backdrop');

        // Category sidebar toggle
        categoryToggle.addEventListener('click', () => {
            categorySidebar.classList.toggle('active');
            backdrop.classList.toggle('active');
            cartSidebar.classList.remove('active');
        });

        // Cart sidebar toggle
        cartToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                cartSidebar.classList.toggle('active');
                backdrop.classList.toggle('active');
                categorySidebar.classList.remove('active');
            });
        });

        // Close sidebars when clicking backdrop
        backdrop.addEventListener('click', () => {
            categorySidebar.classList.remove('active');
            cartSidebar.classList.remove('active');
            backdrop.classList.remove('active');
        });

        // Close sidebars when clicking outside
        document.addEventListener('click', (e) => {
            if (!categorySidebar.contains(e.target) &&
                !cartSidebar.contains(e.target) &&
                !e.target.matches('#categoryToggle') &&
                !e.target.matches('.cart-toggle')) {
                categorySidebar.classList.remove('active');
                cartSidebar.classList.remove('active');
                backdrop.classList.remove('active');
            }
        });

        // Close sidebars on window resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                categorySidebar.classList.remove('active');
                cartSidebar.classList.remove('active');
                backdrop.classList.remove('active');
            }
        });
    });
</script>
