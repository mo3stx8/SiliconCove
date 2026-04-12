<div class="modal fade" id="productInfoModal" tabindex="-1" aria-labelledby="productInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productInfoModalLabel">Product Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="modalProductImage" src="" class="img-fluid rounded" style="max-height: 300px;"
                        alt="Product Img">
                </div>
                <h5 id="modalOrderNumber" class="text-muted">Order #: </h5>
                <h4 id="modalProductName" class="fw-bold"></h4>
                {{-- <p id="modalProductDescription" class="text-muted"></p>  Optional description field --}}
                <p id="modalProductPrice" class="text-danger fw-bold"></p>
                <p class="text-muted">Quantity: <span id="modalProductQuantity" class="fw-bold"></span></p>
                <p id="modalPaymentMethod" class="text-muted"></p>
                <!-- GCash Proof of Payment Section -->
                <div id="gcashProof" class="mt-3" style="display: none;">
                    <p class="text-muted">Proof of Payment:</p>
                    <img id="gcashProofImage" src="" class="img-fluid rounded" alt="Proof of Payment"
                        style="max-height: auto;width: 100%;">
                </div>

                <!-- Customer Info Section -->
                <div id="customerInfoSection" style="display: none;">
                    <div class="card mt-3">
                        <div class="card-body">
                            <p>
                                <i class="fas fa-shipping-fast me-2"></i>
                                <b>Ship to:</b>
                                <span
                                    id="modalCustomerAddress">
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showProductInfoModal(order, showCustomerInfo = true) {
        const modal = document.getElementById('productInfoModal');
        const productImage = document.getElementById('modalProductImage');
        const orderNumber = document.getElementById('modalOrderNumber');
        const productName = document.getElementById('modalProductName');
        // const productDescription = document.getElementById('modalProductDescription');
        const productPrice = document.getElementById('modalProductPrice');
        const productQuantity = document.getElementById('modalProductQuantity');
        const paymentMethod = document.getElementById('modalPaymentMethod');

        // Set modal content
        productImage.src = order.product?.image ?
            `{{ asset('storage/') }}/${order.product.image}` :
            '{{ asset('storage/default-product.png') }}';
        orderNumber.textContent = `Order #: ${order.order_no}`;
        productName.textContent = 'Product: ' + order.product?.name;
        // productDescription.textContent = '- ' + (order.product?.description ? order.product.description :
        //     'No description available');
        const productAmount = parseFloat(order.product?.price ?? 0);
        productPrice.textContent = `Price: $${productAmount.toFixed(2)}`;
        productQuantity.textContent = order.quantity;
        paymentMethod.textContent =
            `Payment Method: ${order.payment_method ? order.payment_method.toUpperCase() : 'N/A'}`;

        // Handle GCash proof of payment
        const gcashProofSection = document.getElementById('gcashProof');
        const gcashProofImage = document.getElementById('gcashProofImage');
        if (order.payment_method?.toLowerCase() == 'gcash' && order.proof_of_payment) {
            gcashProofSection.style.display = 'block';
            gcashProofImage.src = `{{ asset('storage/') }}/${order.proof_of_payment}`;
        } else {
            gcashProofSection.style.display = 'none';
        }

        // Handle customer information
        const customerInfoSection = document.getElementById('customerInfoSection');
        if (showCustomerInfo && order?.address) {
            customerInfoSection.style.display = 'block';
            document.getElementById('modalCustomerAddress').textContent = order?.address ?
                `${order?.address?.address}, ${order?.address?.area}, ${order?.address?.region}` : //, ${order?.address?.zip_code}
                'N/A';
        } else {
            customerInfoSection.style.display = 'none';
        }

        // Show modal
        const bootstrapModal = window.bootstrap.Modal.getOrCreateInstance(modal);
        bootstrapModal.show();
    }
</script>
