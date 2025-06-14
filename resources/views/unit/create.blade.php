@extends('layout.layout')

@php
    $title = 'Unit Registration';
    $subTitle = 'Manage Unit';
@endphp
<style>
    /* Simplified styles */
    .unit-container {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        position: relative;
        transition: all 0.2s ease;
    }

    .unit-container:hover {
        border-color: #adb5bd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .unit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e9ecef;
    }

    .unit-actions {
        display: flex;
        gap: 8px;
    }

    .unit-action-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .unit-action-btn:hover {
        background-color: #f8f9fa;
    }

    .remove-unit {
        color: #dc3545;
    }

    .duplicate-unit {
        color: #0d6efd;
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

    .quick-add-container {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .status-toggle {
        display: flex;
        gap: 8px;
    }

    .status-toggle .form-check {
        margin: 0;
        padding: 0;
    }

    .status-toggle .form-check-input {
        margin-top: 0;
        margin-right: 4px;
    }

    .invalid-feedback {
        display: block;
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



            <!-- Form -->
            <form id="unit-registration-form" action="{{ route('unit.store') }}" method="POST">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}">
                <input type="hidden" name="unit_count" id="unit_count" value="1">

                <div class="d-flex justify-content-between align-items-center mb-16">
                    <h4 class="mb-0">Units <span class="badge bg-primary" id="units-counter">1</span></h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clear-all-btn">
                            <iconify-icon icon="ic:baseline-clear-all" class="me-1"></iconify-icon>
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Units Container -->
                <div id="units-container" class="mb-24">
                    <!-- Unit 1 (default) -->
                    <div class="unit-container" id="unit-1" data-unit-id="1">
                        <div class="unit-header">
                            <h5 class="mb-0">Unit #<span class="unit-number">1</span></h5>
                            <div class="unit-actions">
                                <button type="button" class="unit-action-btn duplicate-unit" data-unit-id="1" title="Duplicate Unit">
                                    <iconify-icon icon="ic:baseline-content-copy" width="18"></iconify-icon>
                                </button>
                                <button type="button" class="unit-action-btn remove-unit d-none" data-unit-id="1" title="Remove Unit">
                                    <iconify-icon icon="ic:baseline-delete" width="18"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Unit Type <span class="text-danger-600">*</span>
                                </label>
                                <select class="form-control radius-8 system-select" id="unit_type_1" name="units[0][unit_type]" required>
                                    <option value="">Select Unit Type</option>
                                    @foreach (['Flat', 'Section', 'Office', 'Shop', 'Other'] as $type)
                                        <option value="{{ $type }}" {{ old('units.0.unit_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="unit_type_error_1"></div>
                            </div>

                            <div class="col-md-6 mb-16">
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
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Monthly Rent <span class="text-danger-600">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary-50 border-primary-100">RM</span>
                                    <input type="number" class="form-control radius-8 system-input" id="unit_price_1"
                                        name="units[0][unit_price]" min="0" step="0.01" value="{{ old('units.0.unit_price') }}" required>
                                </div>
                                <div class="invalid-feedback" id="unit_price_error_1"></div>
                            </div>

                            <div class="col-md-6 mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Status <span class="text-danger-600">*</span>
                                </label>
                                <div class="d-flex flex-column gap-2">
                                    <div class="status-toggle">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="units[0][status]"
                                                id="status_available_1" value="available" checked>
                                            <label class="form-check-label" for="status_available_1">Available</label>
                                        </div>
                                    </div>
                                    <div class="status-toggle">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="units[0][status]"
                                                id="status_owner_1" value="owner">
                                            <label class="form-check-label" for="status_owner_1">Occupied by Owner</label>
                                        </div>
                                    </div>
                                    <div class="status-toggle">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="units[0][status]"
                                                id="status_tenant_1" value="tenant">
                                            <label class="form-check-label" for="status_tenant_1">Occupied by Tenant</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback" id="status_error_1"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Unit Button -->
                <div class="add-unit-btn" id="add-unit-btn">
                    <iconify-icon icon="ic:baseline-add-circle" class="text-primary me-8"></iconify-icon>
                    <span>Add Another Unit</span>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end mt-24">
                    <a href="{{ route('unit.index') }}" class="btn btn-outline-danger-600 text-danger-600 btn-medium px-32 py-12 radius-8 me-16">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success btn-medium px-32 py-12 radius-8">
                        <iconify-icon icon="ic:baseline-check" class="me-1"></iconify-icon>
                        Save Units
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables
            let unitCount = 1;
            const unitsContainer = document.getElementById('units-container');
            const addUnitBtn = document.getElementById('add-unit-btn');
            const unitCountInput = document.getElementById('unit_count');
            const unitsCounter = document.getElementById('units-counter');

            const clearAllBtn = document.getElementById('clear-all-btn');

            // Initialize
            updateUnitsCounter();

            // Add new unit
            addUnitBtn.addEventListener('click', addNewUnit);


            // Clear all units
            clearAllBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear all units?')) {
                    // Keep the first unit but clear its values
                    const firstUnit = document.getElementById('unit-1');
                    if (firstUnit) {
                        document.getElementById('unit_type_1').value = '';
                        document.getElementById('unit_name_1').value = '';
                        document.getElementById('unit_price_1').value = '';
                        document.getElementById('status_available_1').checked = true;
                    }

                    // Remove all other units
                    const units = document.querySelectorAll('.unit-container:not(#unit-1)');
                    units.forEach(unit => unit.remove());

                    unitCount = 1;
                    unitCountInput.value = unitCount;
                    updateUnitsCounter();
                }
            });

            // Remove unit delegation
            unitsContainer.addEventListener('click', function(e) {
                // Handle remove unit
                if (e.target.closest('.remove-unit')) {
                    const button = e.target.closest('.remove-unit');
                    const unitId = button.getAttribute('data-unit-id');
                    removeUnit(unitId);
                }

                // Handle duplicate unit
                if (e.target.closest('.duplicate-unit')) {
                    const button = e.target.closest('.duplicate-unit');
                    const unitId = button.getAttribute('data-unit-id');
                    duplicateUnit(unitId);
                }
            });

            // Form validation
            document.getElementById('unit-registration-form').addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });

            // Functions
            function addNewUnit() {
                unitCount++;
                const newUnit = createUnitHtml(unitCount);
                unitsContainer.appendChild(newUnit);
                unitCountInput.value = unitCount;
                updateUnitsCounter();
            }

            function removeUnit(unitId) {
                const unitDiv = document.getElementById(`unit-${unitId}`);
                if (unitDiv) {
                    unitDiv.remove();
                    renumberUnits();
                    updateUnitsCounter();
                }
            }

            function duplicateUnit(unitId) {
                const sourceUnit = document.getElementById(`unit-${unitId}`);
                if (sourceUnit) {
                    unitCount++;
                    const newUnit = createUnitHtml(unitCount);
                    unitsContainer.appendChild(newUnit);

                    // Copy values from source unit
                    const sourceUnitType = document.getElementById(`unit_type_${unitId}`).value;
                    const sourceUnitName = document.getElementById(`unit_name_${unitId}`).value;
                    const sourceUnitPrice = document.getElementById(`unit_price_${unitId}`).value;
                    const sourceStatus = getUnitStatus(unitId);

                    document.getElementById(`unit_type_${unitCount}`).value = sourceUnitType;
                    document.getElementById(`unit_name_${unitCount}`).value = sourceUnitName + ' (Copy)';
                    document.getElementById(`unit_price_${unitCount}`).value = sourceUnitPrice;

                    // Set status
                    document.getElementById(`status_available_${unitCount}`).checked = (sourceStatus === 'available');
                    document.getElementById(`status_owner_${unitCount}`).checked = (sourceStatus === 'owner');
                    document.getElementById(`status_tenant_${unitCount}`).checked = (sourceStatus === 'tenant');

                    unitCountInput.value = unitCount;
                    updateUnitsCounter();
                }
            }

            function getUnitStatus(unitId) {
                if (document.getElementById(`status_available_${unitId}`).checked) return 'available';
                if (document.getElementById(`status_owner_${unitId}`).checked) return 'owner';
                if (document.getElementById(`status_tenant_${unitId}`).checked) return 'tenant';
                return 'available'; // Default
            }

            function createUnitHtml(count) {
                const unitHtml = `
                <div class="unit-container" id="unit-${count}" data-unit-id="${count}">
                    <div class="unit-header">
                        <h5 class="mb-0">Unit #<span class="unit-number">${count}</span></h5>
                        <div class="unit-actions">
                            <button type="button" class="unit-action-btn duplicate-unit" data-unit-id="${count}" title="Duplicate Unit">
                                <iconify-icon icon="ic:baseline-content-copy" width="18"></iconify-icon>
                            </button>
                            <button type="button" class="unit-action-btn remove-unit" data-unit-id="${count}" title="Remove Unit">
                                <iconify-icon icon="ic:baseline-delete" width="18"></iconify-icon>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-16">
                            <label class="form-label text-primary-light text-sm mb-8">
                                Unit Type <span class="text-danger-600">*</span>
                            </label>
                            <select class="form-control radius-8 system-select" id="unit_type_${count}"
                                name="units[${count-1}][unit_type]" required>
                                <option value="">Select Unit Type</option>
                                ${['Flat', 'Section', 'Office', 'Shop', 'Other'].map(type =>
                                    `<option value="${type}">${type}</option>`).join('')}
                            </select>
                            <div class="invalid-feedback" id="unit_type_error_${count}"></div>
                        </div>

                        <div class="col-md-6 mb-16">
                            <label class="form-label text-primary-light text-sm mb-8">
                                Unit Name <span class="text-danger-600">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-50 border-primary-100">UN</span>
                                <input type="text" class="form-control radius-8 system-input" id="unit_name_${count}"
                                    name="units[${count-1}][unit_name]" required>
                            </div>
                            <div class="invalid-feedback" id="unit_name_error_${count}"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-16">
                            <label class="form-label text-primary-light text-sm mb-8">
                                Monthly Rent <span class="text-danger-600">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-50 border-primary-100">RM</span>
                                <input type="number" class="form-control radius-8 system-input" id="unit_price_${count}"
                                    name="units[${count-1}][unit_price]" min="0" step="0.01" required>
                            </div>
                            <div class="invalid-feedback" id="unit_price_error_${count}"></div>
                        </div>

                        <div class="col-md-6 mb-16">
                            <label class="form-label text-primary-light text-sm mb-8">
                                Status <span class="text-danger-600">*</span>
                            </label>
                            <div class="d-flex flex-column gap-2">
                                <div class="status-toggle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="units[${count-1}][status]"
                                            id="status_available_${count}" value="available" checked>
                                        <label class="form-check-label" for="status_available_${count}">Available</label>
                                    </div>
                                </div>
                                <div class="status-toggle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="units[${count-1}][status]"
                                            id="status_owner_${count}" value="owner">
                                        <label class="form-check-label" for="status_owner_${count}">Occupied by Owner</label>
                                    </div>
                                </div>
                                <div class="status-toggle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="units[${count-1}][status]"
                                            id="status_tenant_${count}" value="tenant">
                                        <label class="form-check-label" for="status_tenant_${count}">Occupied by Tenant</label>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback" id="status_error_${count}"></div>
                        </div>
                    </div>
                </div>
                `;

                const template = document.createElement('template');
                template.innerHTML = unitHtml.trim();
                return template.content.firstChild;
            }

            function renumberUnits() {
                const units = document.querySelectorAll('.unit-container');
                units.forEach((unit, index) => {
                    const unitId = index + 1;
                    unit.id = `unit-${unitId}`;
                    unit.setAttribute('data-unit-id', unitId);
                    unit.querySelector('.unit-number').textContent = unitId;
                    unit.querySelector('.remove-unit').setAttribute('data-unit-id', unitId);
                    unit.querySelector('.duplicate-unit').setAttribute('data-unit-id', unitId);

                    // Update all input names and IDs
                    const inputs = unit.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace(/units\[\d+\]/, `units[${index}]`);
                        }
                        if (input.id) {
                            input.id = input.id.replace(/_\d+$/, `_${unitId}`);
                        }
                    });

                    // Update labels
                    const labels = unit.querySelectorAll('label[for]');
                    labels.forEach(label => {
                        if (label.htmlFor) {
                            label.htmlFor = label.htmlFor.replace(/_\d+$/, `_${unitId}`);
                        }
                    });

                    const feedbacks = unit.querySelectorAll('.invalid-feedback');
                    feedbacks.forEach(fb => {
                        fb.id = fb.id.replace(/_\d+$/, `_${unitId}`);
                    });
                });

                unitCount = units.length;
                unitCountInput.value = unitCount;
                updateUnitsCounter();
            }

            function updateUnitsCounter() {
                const units = document.querySelectorAll('.unit-container');
                unitsCounter.textContent = units.length;

                // Hide remove button on the first unit if it's the only one
                const firstUnitRemoveBtn = document.querySelector('#unit-1 .remove-unit');
                if (firstUnitRemoveBtn) {
                    firstUnitRemoveBtn.classList.toggle('d-none', units.length === 1);
                }
            }

            function validateForm() {
                let isValid = true;

                // Clear previous validation errors
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.textContent = '';
                });

                // Validate each unit
                const units = document.querySelectorAll('.unit-container');
                units.forEach(unit => {
                    const unitId = unit.getAttribute('data-unit-id');
                    const unitType = document.getElementById(`unit_type_${unitId}`);
                    const unitName = document.getElementById(`unit_name_${unitId}`);
                    const unitPrice = document.getElementById(`unit_price_${unitId}`);

                    if (!unitType.value) {
                        document.getElementById(`unit_type_error_${unitId}`).textContent = 'Unit type is required';
                        isValid = false;
                    }

                    if (!unitName.value) {
                        document.getElementById(`unit_name_error_${unitId}`).textContent = 'Unit name is required';
                        isValid = false;
                    }

                    if (!unitPrice.value || parseFloat(unitPrice.value) < 0) {
                        document.getElementById(`unit_price_error_${unitId}`).textContent = 'Valid monthly rent is required';
                        isValid = false;
                    }
                });

                return isValid;
            }
        });
    </script>
@endsection
