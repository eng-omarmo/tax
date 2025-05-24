<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card radius-12 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="success-icon mb-3">
                            <iconify-icon icon="mdi:check-circle" style="color: #28a745; font-size: 80px;"></iconify-icon>
                        </div>
                        <h2 class="fw-bold text-success">Payment Successful!</h2>
                        <p class="text-muted">Your payment has been processed successfully.</p>
                    </div>

                    <div class="payment-details p-3 bg-light radius-8 mb-4">
                        <h4 class="mb-3">Property Details</h4>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <p class="mb-1 fw-semibold">Property Name:</p>
                                <p>{{ $property->property_name }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p class="mb-1 fw-semibold">House Code:</p>
                                <p>{{ $property->house_code }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p class="mb-1 fw-semibold">Property Phone:</p>
                                <p>{{ $property->property_phone }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p class="mb-1 fw-semibold">Zone:</p>
                                <p>{{ $property->zone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="mb-4">A receipt has been sent to your email address.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('self.payment', $property->id) }}" class="btn btn-primary px-4 py-2">
                                <iconify-icon icon="mdi:home" class="me-2"></iconify-icon>
                                Return to Dashboard
                            </a>
                            <a href="#" onclick="window.print()" class="btn btn-outline-secondary px-4 py-2">
                                <iconify-icon icon="mdi:printer" class="me-2"></iconify-icon>
                                Print Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">You will be redirected back to the dashboard in 10 seconds.</p>
            </div>
        </div>
    </div>
</div>
