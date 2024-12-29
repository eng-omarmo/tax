@extends('layout.layout')
@php
    $title = 'Edit Landlord';
    $subTitle = 'Edit Landlord';
    $script = '<script>
        // ================== Image Upload Js Start ===========================
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $("#imagePreview").css("background-image", "url(" + e.target.result + ")");
                    $("#imagePreview").hide();
                    $("#imagePreview").fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });
        // ================== Image Upload Js End ===========================
    </script>';
@endphp

@section('content')

    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-lg-10">
                    <div class="card border">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('lanlord.store') }}" method="POST">
                                @csrf

                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Landlord Name <span
                                            class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name" name="name"
                                        placeholder="Enter Landlord Name">
                                </div>

                                <div class="mb-20">
                                    <label for="phone"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Landlord Phone <span
                                            class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="phone" name="phone"
                                        placeholder="Enter Phone">
                                </div>
                                <div class="mb-20">
                                    <label for="email"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Landlord Email <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="email" name="email"
                                        placeholder="Enter Landlord Email">
                                </div>


                                <div class="mb-20">
                                    <label for="address"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Landlord Address <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="address" name="address"
                                        placeholder="Enter Landlord Address">
                                </div>

                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('lanlord.index') }}" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">Cancel</a>
                                    <button type="submit"
                                        class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
