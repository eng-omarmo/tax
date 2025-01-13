@extends('layout.layout')
@php
    $title = 'Add User';
    $subTitle = 'Add User';
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
                            {{-- <h6 class="text-md text-primary-light mb-16">Profile Image</h6> --}}
                            <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                {{-- <div class="mb-24 mt-16">
                                    <div class="avatar-upload">
                                        <div
                                            class="avatar-edit position-absolute bottom-0 end-0 me-24 mt-16 z-1 cursor-pointer">
                                            <input type='file' id="imageUpload" name="profile_image"
                                                accept=".png, .jpg, .jpeg" hidden>
                                            <label for="imageUpload"
                                                class="w-32-px h-32-px d-flex justify-content-center align-items-center bg-primary-50 text-primary-600 border border-primary-600 bg-hover-primary-100 text-lg rounded-circle">
                                                <iconify-icon icon="solar:camera-outline" class="icon"></iconify-icon>
                                            </label>
                                        </div>
                                        <div class="avatar-preview">
                                            <div id="imagePreview"> </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Full Name <span
                                            class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name" name="name"
                                        placeholder="Enter Full Name" value="{{ old('name') }}">
                                </div>
                                <div class="mb-20">
                                    <label for="email"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Email <span
                                            class="text-danger-600">*</span></label>
                                    <input type="email" class="form-control radius-8" id="email" name="email"
                                        placeholder="Enter email address" value="{{ old('email') }}">
                                </div>
                                <div class="mb-20">
                                    <label for="phone"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Phone</label>
                                    <input type="text" class="form-control radius-8" id="phone" name="phone"
                                        placeholder="Enter phone number" value="{{ old('phone') }}">
                                </div>
                                <div class="mb-20">
                                    <label for="role"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Role <span
                                            class="text-danger-600">*</span></label>
                                    <select class="form-control radius-8 form-select" id="role" name="role">
                                        <option value="admin">Admin</option>
                                        <option value="Tax Officer">Tax Officer</option>

                                    </select>
                                </div>
                                <div class="mb-20">
                                    <label for="status"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Status <span
                                            class="text-danger-600">*</span></label>
                                    <select class="form-control radius-8 form-select" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>



                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">Cancel</button>
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
