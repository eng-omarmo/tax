<!DOCTYPE html>
<html lang="en" data-theme="light">

<x-head />

<body>

    <section class="auth bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="">
            </div>
        </div>

        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">

                <div>
                    <a href="{{ route('index') }}" class="mb-40 max-w-290-px">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="">
                    </a>
                    <h4 class="mb-12">Verify Your OTP</h4>
                    <p class="mb-32 text-secondary-light text-lg">Enter the OTP sent to your registered email or phone.</p>
                </div>

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form action="{{ route('verify.otp') }}" method="POST">
                    @csrf
                    <!-- OTP Field -->
                    <div class="icon-field mb-20">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mdi:form-textbox-password"></iconify-icon>
                        </span>
                        <input
                            type="text"
                            class="form-control h-56-px bg-neutral-50 radius-12"
                            name="otp"
                            placeholder="Enter OTP"
                            required>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">
                        Verify OTP
                    </button>
                </form>
            </div>
        </div>
    </section>

    <x-script />
</body>

</html>
