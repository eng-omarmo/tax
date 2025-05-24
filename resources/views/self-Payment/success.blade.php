<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .success-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .success-card:hover {
            transform: translateY(-5px);
        }
        .success-icon {
            animation: bounce 1s ease;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .detail-card {
            background: rgba(255, 255, 255, 0.9);
            border-left: 4px solid #28a745;
        }
        .btn-primary {
            background: #28a745;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: #218838;
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
                <div class="success-card card shadow-lg">
                    <div class="card-body px-5 py-4">
                        <div class="text-center py-4">
                            <div class="success-icon mb-3">
                                <iconify-icon icon="mdi:check-circle" style="color: #28a745; font-size: 80px;"></iconify-icon>
                            </div>
                            <h1 class="display-5 fw-bold text-success mb-3">Payment Successful!</h1>
                            <p class="lead text-muted mb-4">Your transaction has been completed successfully.</p>
                        </div>

                        <div class="detail-card p-4 mb-4">
                            <h4 class="fw-bold mb-4 text-success">Property Details</h4>
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
                            <p class="text-muted mb-4">A detailed receipt has been sent to your registered email address.</p>
                            <div class="d-flex flex-column flex-lg-row justify-content-center gap-3">
                                <a href="{{ route('self.payment', $property->id) }}"
                                   class="btn btn-primary d-flex align-items-center">
                                    <iconify-icon icon="mdi:home" class="me-2"></iconify-icon>
                                    view property details
                                </a>
                                <button onclick="window.print()"
                                        class="btn btn-outline-secondary d-flex align-items-center">
                                    <iconify-icon icon="mdi:printer" class="me-2"></iconify-icon>
                                    Print Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted small">Automatic redirect to dashboard in <span class="fw-bold">10 seconds</span></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
