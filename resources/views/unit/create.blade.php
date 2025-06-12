@extends('layout.layout')

@php
    $title = 'Unit Registration';
    $subTitle = 'Manage Unit';
@endphp
<style>
    /* Multi-step form styles */
    .step-indicator {
        color: #6c757d;
        position: relative;
        z-index: 1;
    }

    .step-indicator.active {
        color: #0d6efd;
        font-weight: 600;
    }

    .step-indicator.completed {
        color: #198754;
    }

    .step-indicator.completed::after {
        content: '✓';
        display: inline-block;
        margin-left: 4px;
        font-size: 12px;
    }

    .radio-card {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .radio-card:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }

    input[type="radio"]:checked+.form-check-label+.radio-card,
    input[type="radio"]:checked~.radio-card {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }

    .invalid-feedback {
        display: block;
    }

    /* Styles for multiple units */
    .unit-container {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        position: relative;
    }

    .unit-container:hover {
        border-color: #adb5bd;
    }

    .remove-unit {
        position: absolute;
        top: 8px;
        right: 8px;
        cursor: pointer;
        color: #dc3545;
    }

    .add-unit-btn {
        width: 100%;
        border: 1px dashed #adb5bd;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .add-unit-btn:hover {
        background-color: #e9ecef;
        border-color: #6c757d;
    }
</style>

@section('content')
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <!-- Property Info Card - Always visible -->
            <div class="card bg-primary-50 border-primary-100 radius-8 mb-24">
                <div class="card-body p-16">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-sm text-primary-light mb-8">Property Details</div>
                            <div class="d-flex align-items-center mb-12">
                                <iconify-icon icon="ic:twotone-business" class="text-primary me-8"></iconify-icon>
                                <span class="fw-semibold">{{ $property->property_name }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-12">
                                <iconify-icon icon="ic:baseline-code" class="text-primary me-8"></iconify-icon>
                                <span class="text-sm">{{ $property->house_code }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-sm text-primary-light mb-8">Property Details</div>
                            <div class="d-flex align-items-center mb-12">
                                <iconify-icon icon="ic:baseline-house" class="text-primary me-8"></iconify-icon>
                                <span class="text-sm">{{ $property->house_type }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-12">
                                <iconify-icon icon="ic:baseline-phone" class="text-primary me-8"></iconify-icon>
                                <span class="text-sm">{{ $property->property_phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert bg-danger-50 border-danger-200 radius-8 mb-24">
                    <div class="d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-error" class="text-danger me-8"></iconify-icon>
                        <div class="text-sm text-danger">
                            <div class="fw-semibold">Validation Errors:</div>
                            <ul class="mb-0 ps-16">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Progress Bar -->

            <div class="mb-24">
                <div class="progress radius-8" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 20%;" id="step-progress-bar"
                        aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-8">
                    <span class="text-sm step-indicator active" data-step="1">Property</span>
                    <span class="text-sm step-indicator" data-step="2">Units</span>
                    <span class="text-sm step-indicator" data-step="3">Review</span>
                    <span class="text-sm step-indicator" data-step="4">Submit</span>
                </div>
            </div>
            <!-- Multi-step Form -->
            <form id="unit-registration-form" action="{{ route('unit.store') }}" method="POST">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}">
                <input type="hidden" name="current_step" id="current_step" value="1">
                <input type="hidden" name="unit_count" id="unit_count" value="1">

                <!-- Step 1: Property Information (Read-only, already displayed above) -->
                <div class="step-content" id="step-1">
                    <div class="text-center py-24">
                        <h4 class="mb-16">Property Information</h4>
                        <p class="text-muted">The property details are shown above. Click Next to continue with unit
                            registration.</p>
                    </div>
                </div>

                <!-- Step 2: Units Configuration -->
                <div class="step-content d-none" id="step-2">
                    <h4 class="mb-24">Units Configuration</h4>
                    <p class="text-muted mb-16">Add one or more units to this property.</p>
                    <div id="units-container">
                        <!-- Unit 1 (default) -->
                        <div class="unit-container" id="unit-1">
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <h5 class="mb-0">Unit #<span class="unit-number">1</span></h5>
                                <iconify-icon icon="ic:baseline-delete" class="remove-unit d-none"
                                    data-unit="1"></iconify-icon>
                            </div>

                            <!-- Unit Type -->
                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Unit Type <span class="text-danger-600">*</span>
                                </label>
                                <select class="form-control radius-8 system-select" id="unit_type_1"
                                    name="units[0][unit_type]" required>
                                    <option value="">Select Unit Type</option>
                                    @foreach (['Flat', 'Section', 'Office', 'Shop', 'Other'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('units.0.unit_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="unit_type_error_1"></div>
                            </div>

                            <!-- Unit Name -->
                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Unit Name <span class="text-danger-600">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary-50 border-primary-100">UN</span>
                                    <input type="text" class="form-control radius-8 system-input" id="unit_name_1"
                                        name="units[0][unit_name]" value="{{ old('units.0.unit_name') }}" required>
                                </div>
                                <div class="invalid-feedback" id="unit_name_error_1"></div>
                            </div>

                            <!-- Monthly Rent -->
                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Monthly Rent <span class="text-danger-600">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary-50 border-primary-100">RM</span>
                                    <input type="number" class="form-control radius-8 system-input" id="unit_price_1"
                                        name="units[0][unit_price]" min="0" step="0.01"
                                        value="{{ old('units.0.unit_price') }}" required>
                                </div>
                                <div class="invalid-feedback" id="unit_price_error_1"></div>
                            </div>

                            <!-- Common Status Settings for All Units -->
                            <div class="card bg-light-50 border-light-100 radius-8 mb-24">
                                <div class="card-body p-16">

                                    <div class="row">
                                        <!-- Availability -->
                                        <div class="col-md-6 mb-16">
                                            <label class="form-label text-primary-light text-sm mb-8">
                                                Availability <span class="text-danger-600">*</span>
                                            </label>
                                            <div class="d-flex flex-column gap-8">
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_available"
                                                        id="available_yes" value="0"
                                                        {{ old('is_available', 0) == 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="available_yes">
                                                        Available
                                                    </label>
                                                </div>
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_available"
                                                        id="available_no" value="1"
                                                        {{ old('is_available') == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="available_no">
                                                        Occupied
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" id="is_available_error"></div>
                                        </div>

                                        <!-- Occupied By -->
                                        <div class="col-md-6 mb-16">
                                            <label class="form-label text-primary-light text-sm mb-8">
                                                Occupied By <span class="text-danger-600">*</span>
                                            </label>
                                            <div class="d-flex flex-column gap-8">
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_owner"
                                                        id="occupant_owner" value="yes"
                                                        {{ old('is_owner') == 'yes' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="occupant_owner">
                                                        Property Owner
                                                    </label>
                                                </div>
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_owner"
                                                        id="occupant_tenant" value="no"
                                                        {{ old('is_owner') == 'no' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="occupant_tenant">
                                                        Tenant
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" id="is_owner_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="units-container">

                    </div>


                    <!-- Add Unit Button -->
                    <div class="add-unit-btn" id="add-unit-btn">
                        <iconify-icon icon="ic:baseline-add-circle" class="text-primary me-8"></iconify-icon>
                        <span>Add Another Unit</span>
                    </div>
                </div>

        </div>


        <!-- Step 4: Review & Submit -->
        <div class="step-content d-none" id="step-4">
            <h4 class="mb-24">Review Unit Details</h4>
            <p class="text-muted mb-16">Review the units you're about to create.</p>

            <div id="review-units-container">
                <!-- Will be populated dynamically -->
            </div>

            <div class="alert bg-info-50 border-info-100 radius-8 mt-24">
                <div class="d-flex align-items-center">
                    <iconify-icon icon="ic:baseline-info" class="text-info me-8"></iconify-icon>
                    <div class="text-sm">Please review the information above before submitting. Once submitted, you
                        can still edit the unit details later.</div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex align-items-center justify-content-between mt-24">
            <button type="button" id="prev-step" class="btn btn-outline-primary btn-medium px-32 py-12 radius-8 d-none">
                Previous
            </button>

            <div class="ms-auto">
                <a href="{{ route('unit.index') }}"
                    class="btn btn-outline-danger-600 text-danger-600 btn-medium px-32 py-12 radius-8 me-16">
                    Cancel
                </a>
                <button type="button" id="next-step" class="btn btn-primary btn-medium px-32 py-12 radius-8">
                    Next
                </button>
                <button type="submit" id="submit-form" class="btn btn-success btn-medium px-32 py-12 radius-8 d-none">
                    Submit
                </button>
            </div>
        </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('unit-registration-form');
            const stepContents = document.querySelectorAll('.step-content');
            const stepIndicators = document.querySelectorAll('.step-indicator');
            const progressBar = document.getElementById('step-progress-bar');
            const currentStepInput = document.getElementById('current_step');
            const unitCountInput = document.getElementById('unit_count');
            const prevStepBtn = document.getElementById('prev-step');
            const nextStepBtn = document.getElementById('next-step');
            const submitFormBtn = document.getElementById('submit-form');
            const addUnitBtn = document.getElementById('add-unit-btn');
            const unitsContainer = document.getElementById('units-container');
            const reviewUnitsContainer = document.getElementById('review-units-container');

            let currentStep = 1;
            const totalSteps = stepContents.length;
            let unitCount = 1;

            // Initialize the form
            updateFormState();

            // Event listeners for navigation buttons
            prevStepBtn.addEventListener('click', goToPreviousStep);
            nextStepBtn.addEventListener('click', goToNextStep);

            // Event listener for adding a new unit
            addUnitBtn.addEventListener('click', addNewUnit);

            // Event delegation for removing units
            unitsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-unit') || e.target.closest('.remove-unit')) {
                    const unitElement = e.target.closest('.unit-container');
                    if (unitElement) {
                        removeUnit(unitElement);
                    }
                }
            });

            // Function to update the form state based on the current step
            function updateFormState() {
                // Update progress
                const progressPercentage = (currentStep / totalSteps) * 100;
                progressBar.style.width = `${progressPercentage}%`;

                // Update step indicators
                stepIndicators.forEach((indicator, index) => {
                    indicator.classList.toggle('active', index + 1 === currentStep);
                    indicator.classList.toggle('completed', index + 1 < currentStep);
                });

                // Show current step, hide others
                stepContents.forEach((step, index) => {
                    if (index + 1 === currentStep) {
                        step.classList.remove('d-none');
                    } else {
                        step.classList.add('d-none');
                    }
                });

                // Update navigation buttons
                prevStepBtn.classList.toggle('d-none', currentStep === 1);
                nextStepBtn.classList.toggle('d-none', currentStep === totalSteps);
                submitFormBtn.classList.toggle('d-none', currentStep !== totalSteps);

                // Update review section if on last step
                if (currentStep === 4) {
                    updateReviewSection();
                }

                // Update current step in hidden input
                currentStepInput.value = currentStep;
            }

            // Function to go to the previous step
            function goToPreviousStep() {
                if (currentStep > 1) {
                    currentStep--;
                    updateFormState();
                }
            }

            // Function to go to the next step
            function goToNextStep() {
                if (validateCurrentStep() && currentStep < totalSteps) {
                    currentStep++;
                    updateFormState();
                }
            }

            // Function to validate the current step

            // Function to validate the current step
            function validateCurrentStep() {
                let isValid = true;

                // Clear previous validation errors
                const errorElements = document.querySelectorAll('.invalid-feedback');
                errorElements.forEach(el => el.textContent = '');

                // Validate based on current step
                switch (currentStep) {
                    case 2: // Units and Status (combined)
                        // Validate each unit's fields
                        const unitContainers = document.querySelectorAll('.unit-container');
                        unitContainers.forEach((container, index) => {
                            const unitId = container.id.split('-')[1];

                            const unitType = document.getElementById(`unit_type_${unitId}`);
                            const unitName = document.getElementById(`unit_name_${unitId}`);
                            const unitPrice = document.getElementById(`unit_price_${unitId}`);

                            if (!unitType.value) {
                                document.getElementById(`unit_type_error_${unitId}`).textContent =
                                    'Please select a unit type';
                                isValid = false;
                            }

                            if (!unitName.value.trim()) {
                                document.getElementById(`unit_name_error_${unitId}`).textContent =
                                    'Please enter a unit name';
                                isValid = false;
                            }

                            if (!unitPrice.value || parseFloat(unitPrice.value) <= 0) {
                                document.getElementById(`unit_price_error_${unitId}`).textContent =
                                    'Please enter a valid monthly rent amount';
                                isValid = false;
                            }
                        });

                        // Validate status fields
                        const isAvailable = document.querySelector('input[name="is_available"]:checked');
                        const isOwner = document.querySelector('input[name="is_owner"]:checked');

                        if (!isAvailable) {
                            document.getElementById('is_available_error').textContent =
                                'Please select availability status';
                            isValid = false;
                        }

                        if (!isOwner) {
                            document.getElementById('is_owner_error').textContent = 'Please select occupant type';
                            isValid = false;
                        }
                        break;
                }

                return isValid;
            }


            // Function to add a new unit
            function addNewUnit() {
                unitCount++;
                const newIndex = unitCount - 1; // For array indexing in form submission

                const unitTemplate = `
                    <div class="unit-container" id="unit-${unitCount}">
                        <div class="d-flex justify-content-between align-items-center mb-16">
                            <h5 class="mb-0">Unit #<span class="unit-number">${unitCount}</span></h5>
                            <iconify-icon icon="ic:baseline-delete" class="remove-unit" data-unit="${unitCount}"></iconify-icon>
                        </div>

                        <!-- Unit Type -->
                        <div class="mb-16">
                            <label class="form-label text-primary-light text-sm mb-8">
                                Unit Type <span class="text-danger-600">*</span>
                            </label>
                            <select class="form-control radius-8 system-select" id="unit_type_${unitCount}" name="units[${newIndex}][unit_type]" required>
                                <option value="">Select Unit Type</option>
                                @foreach (['Flat', 'Section', 'Office', 'Shop', 'Other'] as $type)
                                    <option value="{{ $type }}">
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="unit_type_error_${unitCount}"></div>
                        </div>

                        <!-- Unit Name -->
                        <div class="mb-16">
                            <label class="form-label text-primary-light text-sm mb-8">
                                Unit Name <span class="text-danger-600">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-50 border-primary-100">UN</span>
                                <input type="text" class="form-control radius-8 system-input" id="unit_name_${unitCount}" name="units[${newIndex}][unit_name]" required>
                            </div>
                            <div class="invalid-feedback" id="unit_name_error_${unitCount}"></div>
                        </div>

                        <!-- Monthly Rent -->
                        <div class="mb-16">
                            <label class="form-label text-primary-light text-sm mb-8">
                                Monthly Rent <span class="text-danger-600">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-50 border-primary-100">RM</span>
                                <input type="number" class="form-control radius-8 system-input" id="unit_price_${unitCount}"
                                    name="units[${newIndex}][unit_price]" min="0" step="0.01" required>
                            </div>
                            <div class="invalid-feedback" id="unit_price_error_${unitCount}"></div>
                        </div>
                    </div>
                        <!-- Common Status Settings for All Units -->
                            <div class="card bg-light-50 border-light-100 radius-8 mb-24">
                                <div class="card-body p-16">

                                    <div class="row">
                                        <!-- Availability -->
                                        <div class="col-md-6 mb-16">
                                            <label class="form-label text-primary-light text-sm mb-8">
                                                Availability <span class="text-danger-600">*</span>
                                            </label>
                                            <div class="d-flex flex-column gap-8">
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_available"
                                                        id="available_yes" value="0"
                                                        {{ old('is_available', 0) == 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="available_yes">
                                                        Available
                                                    </label>
                                                </div>
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_available"
                                                        id="available_no" value="1"
                                                        {{ old('is_available') == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="available_no">
                                                        Occupied
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" id="is_available_error"></div>
                                        </div>

                                        <!-- Occupied By -->
                                        <div class="col-md-6 mb-16">
                                            <label class="form-label text-primary-light text-sm mb-8">
                                                Occupied By <span class="text-danger-600">*</span>
                                            </label>
                                            <div class="d-flex flex-column gap-8">
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_owner"
                                                        id="occupant_owner" value="yes"
                                                        {{ old('is_owner') == 'yes' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="occupant_owner">
                                                        Property Owner
                                                    </label>
                                                </div>
                                                <div class="form-check radio-card align-items-center p-12 radius-8">
                                                    <input class="form-check-input" type="radio" name="is_owner"
                                                        id="occupant_tenant" value="no"
                                                        {{ old('is_owner') == 'no' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-sm ms-8" for="occupant_tenant">
                                                        Tenant
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" id="is_owner_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                `;

                unitsContainer.insertAdjacentHTML('beforeend', unitTemplate);

                // Show remove button for the first unit if there are now multiple units
                if (unitCount === 2) {
                    const firstUnitRemoveBtn = document.querySelector('#unit-1 .remove-unit');
                    if (firstUnitRemoveBtn) {
                        firstUnitRemoveBtn.classList.remove('d-none');
                    }
                }

                // Update unit count in hidden input
                unitCountInput.value = unitCount;
            }

            // Function to remove a unit
            function removeUnit(unitElement) {
                // Don't allow removing if there's only one unit left
                if (document.querySelectorAll('.unit-container').length <= 1) {
                    return;
                }

                unitElement.remove();

                // Reindex the remaining units for display purposes
                const unitContainers = document.querySelectorAll('.unit-container');
                unitContainers.forEach((container, index) => {
                    const unitNumberElement = container.querySelector('.unit-number');
                    if (unitNumberElement) {
                        unitNumberElement.textContent = index + 1;
                    }
                });

                // Hide remove button for the first unit if there's only one unit left
                if (unitContainers.length === 1) {
                    const firstUnitRemoveBtn = document.querySelector('#unit-1 .remove-unit');
                    if (firstUnitRemoveBtn) {
                        firstUnitRemoveBtn.classList.add('d-none');
                    }
                }

                // Update unit count
                unitCount = unitContainers.length;
                unitCountInput.value = unitCount;
            }

            // Function to update the review section
            function updateReviewSection() {
                // Clear previous content
                reviewUnitsContainer.innerHTML = '';

                // Get all unit containers
                const unitContainers = document.querySelectorAll('.unit-container');

                // Get common status values
                const isAvailableRadio = document.querySelector('input[name="is_available"]:checked');
                const isOwnerRadio = document.querySelector('input[name="is_owner"]:checked');

                const availabilityStatus = isAvailableRadio ?
                    (isAvailableRadio.value == '0' ? 'Available' : 'Occupied') : 'Not specified';

                const occupantType = isOwnerRadio ?
                    (isOwnerRadio.value === 'yes' ? 'Property Owner' : 'Tenant') : 'Not specified';

                // Create review cards for each unit
                unitContainers.forEach((container, index) => {
                    const unitId = container.id.split('-')[1];

                    // Get unit values
                    const unitTypeSelect = document.getElementById(`unit_type_${unitId}`);
                    const unitTypeText = unitTypeSelect ? unitTypeSelect.options[unitTypeSelect
                        .selectedIndex].text : 'Not specified';

                    const unitName = document.getElementById(`unit_name_${unitId}`).value ||
                        'Not specified';
                    const unitPrice = document.getElementById(`unit_price_${unitId}`).value ||
                        'Not specified';

                    // Create review card
                    const reviewCard = `
                        <div class="card bg-light-50 border-light-100 radius-8 mb-16">
                            <div class="card-body p-16">
                                <h5 class="mb-16">Unit #${index + 1}</h5>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-semibold">Unit Type:</div>
                                    <div class="col-md-8">${unitTypeText}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-semibold">Unit Name:</div>
                                    <div class="col-md-8">${unitName}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-semibold">Monthly Rent:</div>
                                    <div class="col-md-8">${unitPrice ? `USD ${parseFloat(unitPrice).toFixed(2)}` : 'Not specified'}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-semibold">Availability:</div>
                                    <div class="col-md-8">${availabilityStatus}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 fw-semibold">Occupied By:</div>
                                    <div class="col-md-8">${occupantType}</div>
                                </div>
                            </div>
                        </div>
                    `;

                    reviewUnitsContainer.insertAdjacentHTML('beforeend', reviewCard);
                });
            }
        });
    </script>
@endsection
