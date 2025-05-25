<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .failure-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .failure-card:hover {
            transform: translateY(-5px);
        }
        .failure-icon {
            animation: bounce 1s ease;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .detail-card {
            background: rgba(255, 255, 255, 0.9);
            border-left: 4px solid #dc3545;
        }
        .btn-danger {
            background: #dc3545;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
        }
        .btn-danger:hover {
            background: #bb2d3b;
        }
        .btn-outline-secondary {
            border-color: #dee2e6;
            padding: 12px 30px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="failure-card card shadow-lg">
                    <div class="card-body px-5 py-4">
                        <div class="text-center py-4">
                            <div class="failure-icon mb-3">
                                <iconify-icon icon="mdi:close-circle" style="color: #dc3545; font-size: 80px;"></iconify-icon>
                            </div>
                            <h1 class="display-5 fw-bold text-danger mb-3">Payment Failed!</h1>
                            <p class="lead text-muted mb-4">We couldn't complete your transaction. Please try again.</p>
                        </div>

                        <div class="detail-card p-4 mb-4">
                            <h4 class="fw-bold mb-4 text-danger">Property Details</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="text-muted mb-1 fw-medium">Property Name</p>
                                        <h5 class="fw-semibold">{{ $property->property_name }}</h5>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-muted mb-1 fw-medium">Property Phone</p>
                                        <h5 class="fw-semibold">{{ $property->property_phone }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="text-muted mb-1 fw-medium">House Code</p>
                                        <h5 class="fw-semibold">{{ $property->house_code }}</h5>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-muted mb-1 fw-medium">Zone</p>
                                        <h5 class="fw-semibold">{{ $property->zone }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center py-4">
                            <p class="text-muted mb-4">No amount has been deducted from your account. Please verify your payment details.</p>
                            <div class="d-flex flex-column flex-lg-row justify-content-center gap-3">
                                <a href="{{ route('retry.payment', $property->id) }}"
                                   class="btn btn-danger d-flex align-items-center">
                                    <iconify-icon icon="mdi:credit-card-refresh" class="me-2"></iconify-icon>
                                    Retry Payment
                                </a>
                                <button onclick="window.history.back()"
                                        class="btn btn-outline-secondary d-flex align-items-center">
                                    <iconify-icon icon="mdi:arrow-left" class="me-2"></iconify-icon>
                                    Go Back
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
