<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Transport Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .chassis-counter {
            font-size: 0.8rem;
            text-align: right;
            margin-top: -15px;
            margin-bottom: 10px;
            color: #6b7280;
        }
        .chassis-counter.invalid {
            color: #ef4444;
        }
        .modal {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <div class="text-xl font-semibold text-gray-800">Creation de Demandes de Transport</div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('logistic.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </nav>

    <!-- Form -->
    <main class="max-w-4xl mx-auto p-6 bg-white rounded-2xl shadow-md mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-truck text-blue-600"></i> Nouvelle demande de Transport
        </h2>

        <form id="transportForm" method="POST" action="{{ route('logistic.transport.store') }}" class="space-y-6">
            @csrf

            <!-- Date Info -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-gray-500"></i> Date Info
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Date de Commande -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Date de Commande *</label>
                        <input type="date" name="date_commande" id="dateCommande"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            required
                            value="{{ date('Y-m-d') }}"
                            min="{{ date('Y-m-d', strtotime('-1 month')) }}"
                            max="{{ date('Y-m-d', strtotime('+1 month')) }}">
                    </div>
                </div>
            </div>
            <!-- Request Info -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-building text-gray-500"></i> Request Info
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Société -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Société *</label>
                        <select name="societe" id="societeSelect"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Société</option>
                            @foreach($societes as $societe => $sites)
                                <option value="{{ $societe }}">{{ $societe }}</option>
                            @endforeach
                            <option value="other">Other (Specify)</option>
                        </select>
                        <input type="text" name="custom_societe" id="customSociete"
                            class="w-full border rounded-lg px-3 py-2 text-sm mt-2 hidden focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter custom société">
                    </div>

                    <!-- Site -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Site Demandeur *</label>
                        <select name="site_demandeur" id="siteSelect"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Site</option>
                            <option value="other">Other (Specify)</option>
                        </select>
                        <input type="text" name="custom_site" id="customSite"
                            class="w-full border rounded-lg px-3 py-2 text-sm mt-2 hidden focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter custom site">
                    </div>
                </div>
            </div>

            <!-- Route Info -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-gray-500"></i> Route Info
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Departure -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Point depart *</label>
                        <select name="pointdepart" id="pointdepart"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            onchange="toggleCustomInput(this, 'customDeparture')">
                            <option value="">Select</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                            <option value="other">Other (Specify)</option>
                        </select>
                        <input type="text" name="custom_departure" id="customDeparture"
                            class="w-full border rounded-lg px-3 py-2 text-sm mt-2 hidden focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter custom departure">
                    </div>

                    <!-- Destination -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Point Arrive *</label>
                        <select name="poinarrive" id="poinarrive"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            onchange="toggleCustomInput(this, 'customArrival')">
                            <option value="">Select</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                            <option value="other">Other (Specify)</option>
                        </select>
                        <input type="text" name="custom_arrival" id="customArrival"
                            class="w-full border rounded-lg px-3 py-2 text-sm mt-2 hidden focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter custom destination">
                    </div>
                </div>
            </div>

            <!-- Vehicle Info -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-car text-gray-500"></i> Vehicle Info
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Chassis -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Chassis *</label>
                        <input type="text" name="chassis" id="chassisInput"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            required maxlength="17" oninput="updateChassisCounter(this)">
                        <div id="chassisCounter" class="chassis-counter">
                            0/17 characters
                        </div>
                    </div>

                    <!-- Vehicle Type -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Vehicle Type *</label>
                        <div class="flex gap-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="typevehicule" value="vp" checked
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">VP</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="typevehicule" value="vu"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">VU</span>
                            </label>
                        </div>
                    </div>

                    <!-- Model (Optional) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Model (Optional)</label>
                        <input type="text" name="model" id="model"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter vehicle model (optional)">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6 flex justify-end">
                <button type="button" onclick="validateAndShowModal()"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </div>
        </form>
    </main>


    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 modal">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Confirm Transport Request</h3>
            <div id="errorMessage" class="text-red-500 mb-4 hidden">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span id="errorText"></span>
            </div>
            <div id="confirmationContent">

                <div id="confirmationDetails" class="mb-4 text-sm text-gray-700 space-y-2">
                    <p><strong>Date de Commande:</strong> <span id="confirmDate"></span></p>
                    <p><strong>Société:</strong> <span id="confirmSociete"></span></p>
                    <p><strong>Site Demandeur:</strong> <span id="confirmSite"></span></p>
                    <p><strong>Departure:</strong> <span id="confirmDeparture"></span></p>
                    <p><strong>Destination:</strong> <span id="confirmArrival"></span></p>
                    <p><strong>Chassis:</strong> <span id="confirmChassis"></span></p>
                    <p><strong>Vehicle Type:</strong> <span id="confirmType"></span></p>
                    <p><strong>Model:</strong> <span id="confirmModel"></span></p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button onclick="hideModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button onclick="submitForm()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" id="confirmButton">
                        Confirm
                    </button>
                </div>
            </div>
            <div id="loadingIndicator" class="hidden text-center py-4">
                <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                <p class="mt-2 text-gray-600">Processing your request...</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle custom input fields
        function toggleCustomInput(selectElement, customInputId) {
            const customInput = document.getElementById(customInputId);
            if (selectElement.value === 'other') {
                customInput.classList.remove('hidden');
                customInput.required = true;
            } else {
                customInput.classList.add('hidden');
                customInput.required = false;
            }
        }

        // Chassis character counter
        function updateChassisCounter(input) {
            const counter = document.getElementById('chassisCounter');
            const length = input.value.length;
            counter.textContent = `${length}/17 characters`;
            
            if (length === 17) {
                counter.classList.add('invalid');
            } else {
                counter.classList.remove('invalid');
            }
        }

        // Show modal with validation
        function validateAndShowModal() {
            const form = document.getElementById('transportForm');
            const chassisInput = document.getElementById('chassisInput');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');

            // Reset error state
            errorMessage.classList.add('hidden');

            // Validate chassis length
            if (chassisInput.value.length !== 17) {
                errorText.textContent = 'Chassis must be exactly 17 characters';
                errorMessage.classList.remove('hidden');
                showModal();
                return;
            }

            // Validate form fields
            if (!form.reportValidity()) {
                errorText.textContent = 'Please fill all required fields correctly';
                errorMessage.classList.remove('hidden');
                showModal();
                return;
            }

            // ✅ Fill confirmation modal with user inputs
            document.getElementById('confirmSociete').textContent =
                document.getElementById('societeSelect').value === "other"
                    ? document.getElementById('customSociete').value
                    : document.getElementById('societeSelect').value;

            document.getElementById('confirmSite').textContent =
                document.getElementById('siteSelect').value === "other"
                    ? document.getElementById('customSite').value
                    : document.getElementById('siteSelect').value;

            document.getElementById('confirmDeparture').textContent =
                document.getElementById('pointdepart').value === "other"
                    ? document.getElementById('customDeparture').value
                    : document.getElementById('pointdepart').value;

            document.getElementById('confirmArrival').textContent =
                document.getElementById('poinarrive').value === "other"
                    ? document.getElementById('customArrival').value
                    : document.getElementById('poinarrive').value;

            document.getElementById('confirmChassis').textContent = chassisInput.value;
            document.getElementById('confirmType').textContent =
                document.querySelector('input[name="typevehicule"]:checked').value.toUpperCase();
            document.getElementById('confirmModel').textContent =
                document.getElementById('model').value || "—";

            document.getElementById('confirmDate').textContent =
                document.getElementById('dateCommande').value;

            // Show modal
            showModal();
        }


        function showModal() {
            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        function hideModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }

        function submitForm() {
            const confirmButton = document.getElementById('confirmButton');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const confirmationContent = document.getElementById('confirmationContent');
            
            // Show loading, hide buttons
            confirmButton.disabled = true;
            confirmationContent.classList.add('hidden');
            loadingIndicator.classList.remove('hidden');
            
            // Submit the form
            document.getElementById('transportForm').submit();
        }

        // Set default date to today if not set
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('dateCommande');
            if (!dateInput.value) {
                dateInput.value = new Date().toISOString().split('T')[0];
            }
        });

        // Optional: Add date validation
        document.getElementById('dateCommande').addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            const oneMonthAgo = new Date();
            oneMonthAgo.setMonth(today.getMonth() - 1);
            const oneMonthLater = new Date();
            oneMonthLater.setMonth(today.getMonth() + 1);
            
            if (selectedDate < oneMonthAgo || selectedDate > oneMonthLater) {
                alert('Please select a date within the last month or next month');
                this.value = new Date().toISOString().split('T')[0];
            }
        });
        // Prevent typing beyond 17 characters
        document.getElementById('chassisInput').addEventListener('keydown', function(e) {
            if (this.value.length >= 17 && e.key !== 'Backspace' && e.key !== 'Delete') {
                e.preventDefault();
            }
        });
    </script>

    <script>
        const societeSites = @json($societes);

        document.getElementById('societeSelect').addEventListener('change', function() {
            const societe = this.value;
            const siteSelect = document.getElementById('siteSelect');
            const customSociete = document.getElementById('customSociete');
            
            siteSelect.innerHTML = '<option value="">Select Site</option><option value="other">Other (Specify)</option>';
            customSociete.classList.add('hidden');
            customSociete.required = false;

            if (societe === "other") {
                customSociete.classList.remove('hidden');
                customSociete.required = true;
                return;
            }

            if (societe && societeSites[societe]) {
                societeSites[societe].forEach(site => {
                    let opt = document.createElement("option");
                    opt.value = site;
                    opt.textContent = site;
                    siteSelect.insertBefore(opt, siteSelect.querySelector('option[value="other"]'));
                });

                if (societeSites[societe].length === 1) {
                    siteSelect.value = societeSites[societe][0];
                }
            }
        });

        document.getElementById('siteSelect').addEventListener('change', function() {
            const customSite = document.getElementById('customSite');
            if (this.value === "other") {
                customSite.classList.remove('hidden');
                customSite.required = true;
            } else {
                customSite.classList.add('hidden');
                customSite.required = false;
            }
        });
    </script>

</body>
</html>