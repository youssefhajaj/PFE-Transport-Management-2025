<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Secrétaire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Open Graph (for better previews) -->
    <meta property="og:title" content="Hanyjay">
    <meta property="og:description" content="Your app description here">
    <meta property="og:image" content="{{ asset('icons/KH_logo_512x512.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-semibold text-gray-800">Tableau de Bord {{ Auth::user()->name }}</div>
    <div class="space-x-4 flex items-center">
        <a href="{{ route('secretaire.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition cursor-not-allowed opacity-50">Tableau de Bord</a>
        <a href="{{ route('secretaire.historie') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Historique</a>
        
        <!-- Disponibilite button with notification -->
        <div class="relative">
            <button id="dispoButton" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Disponibilite
            </button>
            <span id="notifBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">
                !
            </span>
        </div>


        
        <form method="POST" action="{{ route('logout') }}" class="inline" onsubmit="localStorage.clear();">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Déconnexion</button>
        </form>
    </div>
</nav>

<!-- Main Content -->
<main class="max-w-4xl mx-auto p-6">
    {{-- <h2 class="text-2xl font-semibold mb-6 text-gray-700">Créer une Nouvelle Demande de Transport</h2> --}}

    @if ($errors->any())
        <div class="p-4 mb-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $cities = [
            'OMSAN Mohammedia',
            'Tirso',
            'M-Automotiv Renaault Vita',
            'M-Automotiv Renault Hay Errahma',
            'M-AUTOMOTIV Renault, Bandoeng Derb Omar',
            'M-AUTOMOTIV Renault, Lissasfa, Casablanca',
            'M-Automotiv Renault, Temara',
            'M-Automotiv Renault, Zenata',
            'M-Automotiv Sidi Othmane - Renault',
            'RENAULT/DACIA AGENT AMIS CLASS BOUSKOURA',
            'RENAULT/DACIA AGENT AMIS CLASS HEY MOLLAY RCHIDE',
            'RENAULT/DACIA AGENT LA CONTINENTALE',
            'RENAULT/DACIA AGENT REFERENCE CAR',
            'VVLOG-STOCK',
            'Casablanca',
            'PEUGEOT/CITROEN SAPINO NOUACEUR-MBA',
            'M-Automotiv Casablanca Succursale Vita'
        ];
    @endphp

    <!-- Form 1: Single Chassis (Original) -->
    <div class="mb-10 p-6 bg-blue-50 border border-gray-200 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Créer une Nouvelle Demande de Transport en dépannage</h3>
        <form id="transportFormSingle" method="POST" action="{{ route('transports.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Point de Départ -->
                <div class="mb-4">
                    <label for="pointdepart_select_single" class="block text-sm font-medium text-gray-700 mb-1">Point de Départ</label>
                    <select id="pointdepart_select_single" class="w-full p-3 border border-gray-300 rounded placeholder-gray-400 shadow-sm">
                        @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                        <option value="other">Autre...</option>
                    </select>
                    <input type="text" id="pointdepart_input_single" placeholder="Entrer une ville personnalisée"
                           class="mt-2 hidden w-full p-3 border border-gray-300 rounded placeholder-gray-400">
                </div>

                <!-- Point d'Arrivée -->
                <div class="mb-4">
                    <label for="poinarrive_select_single" class="block text-sm font-medium text-gray-700 mb-1">Point d'Arrivée</label>
                    <select id="poinarrive_select_single" class="w-full p-3 border border-gray-300 rounded placeholder-gray-400 shadow-sm">
                        @foreach($cities as $city)
                            <option value="{{ $city }}" @if($city == Auth::user()->depo) selected @endif>{{ $city }}</option>
                        @endforeach
                        <option value="other">Autre...</option>
                    </select>
                    <input type="text" id="poinarrive_input_single" placeholder="Entrer une ville personnalisée"
                           class="mt-2 hidden w-full p-3 border border-gray-300 rounded placeholder-gray-400">
                </div>
            </div>

            <!-- Type Véhicule Radio Buttons -->
            <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Type de Véhicule <span class="text-red-500">* merci de selectionner le type de vehicule (oblégatoire)</span>
            </label>                
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="typevehicule" value="vp" checked 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2">VP (Véhicule Particulier Citadin)</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="typevehicule" value="vu"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2">VU (Véhicule Utilitaire Long)</span>
                    </label>
                </div>
            </div>

            <!-- Numéro de Châssis -->
            <div class="mb-4">
                <label for="chassis_single" class="block text-sm font-medium text-gray-700 mb-1">Numéro de Châssis</label>
                <input type="text" id="chassis_single" name="chassis" required
                    maxlength="17"
                    class="w-full p-3 border border-gray-300 rounded placeholder-gray-400"
                    placeholder="Entrer le numéro de châssis">
                <p id="charCount_single" class="text-sm mt-1 text-gray-500 transition-all">17 caractères requis</p>
            </div>

            <!-- Modele form -->
            <div class="mb-4">
                <label for="model_single" class="block text-sm font-medium text-gray-700 mb-1">Modèle du Véhicule <span class="text-red-500">* merci de saisir le model (facultatif)</span></label>
                <input type="text" id="model_single" name="model"
                    class="w-full p-3 border border-gray-300 rounded placeholder-gray-400"
                    placeholder="Entrer le modèle du véhicule (ex: Clio, Megane, etc.)">
            </div>

            

            <!-- Hidden fields -->
            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
            <input type="hidden" name="chefvalid" value="1">
            <input type="hidden" name="needtobevalid" value="1">
            <input type="hidden" name="responsablevalid" value="0">

            <!-- Bouton de Soumission -->
            <div class="text-right mt-4">
                <button type="button" id="confirmBtn_single"
                        disabled
                        class="bg-gray-400 cursor-not-allowed text-white px-6 py-3 rounded transition-colors duration-300">
                    Soumettre la Demande
                </button>
            </div>
        </form>
    </div>

   
    <div class="my-10 border-t-2 border-gray-200 relative">
        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-white px-4 text-gray-500 font-medium">OU</div>
    </div>

    <!-- Form 2: Multiple Chassis (New) -->
    <div class="p-6 border bg-green-50 border-gray-200 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Créer une Nouvelle Demande de Transport en Porte 8</h3>
        <form id="transportFormMultiple" method="POST" action="{{ route('transports.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Point de Départ -->
                <div class="mb-4">
                    <label for="pointdepart_select_multiple" class="block text-sm font-medium text-gray-700 mb-1">Point de Départ</label>
                    <select id="pointdepart_select_multiple" class="w-full p-3 border border-gray-300 rounded placeholder-gray-400 shadow-sm">
                        @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                        <option value="other">Autre...</option>
                    </select>
                    <input type="text" id="pointdepart_input_multiple" placeholder="Entrer une ville personnalisée"
                           class="mt-2 hidden w-full p-3 border border-gray-300 rounded placeholder-gray-400">
                </div>

                <!-- Point d'Arrivée -->
                <div class="mb-4">
                    <label for="poinarrive_select_multiple" class="block text-sm font-medium text-gray-700 mb-1">Point d'Arrivée</label>
                    <select id="poinarrive_select_multiple" class="w-full p-3 border border-gray-300 rounded placeholder-gray-400 shadow-sm">
                        @foreach($cities as $city)
                            <option value="{{ $city }}" @if($city == Auth::user()->depo) selected @endif>{{ $city }}</option>
                        @endforeach
                        <option value="other">Autre...</option>
                    </select>
                    <input type="text" id="poinarrive_input_multiple" placeholder="Entrer une ville personnalisée"
                           class="mt-2 hidden w-full p-3 border border-gray-300 rounded placeholder-gray-400">
                </div>
            </div>

            <!-- Type Véhicule Radio Buttons -->
            <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Type de Véhicule <span class="text-red-500">*</span>
            </label>               
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="typevehicule" value="vp" checked 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2">VP (Véhicule Particulier Citadin)</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="typevehicule" value="vu"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2">VU (Véhicule Utilitaire Long)</span>
                    </label>
                </div>
            </div>
            <!-- Multiple Chassis Numbers -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Numéros de Châssis (jusqu'à 8)</label>
                <div class="space-y-2">
                    @for($i = 0; $i < 8; $i++)
                        <input type="text" name="chassis[]" 
                               maxlength="17"
                               class="w-full p-3 border border-gray-300 rounded placeholder-gray-400 chassis-input"
                               placeholder="Numéro de châssis {{ $i + 1 }}">
                    @endfor
                </div>
                <p class="text-sm mt-1 text-gray-500">Chaque numéro doit contenir exactement 17 caractères</p>
            </div>

            

            <!-- Hidden fields -->
            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
            <input type="hidden" name="chefvalid" value="1">
            <input type="hidden" name="needtobevalid" value="1">
            <input type="hidden" name="responsablevalid" value="0">
            <input type="hidden" name="multiple_chassis" value="1">

            <!-- Bouton de Soumission -->
            <div class="text-right mt-4">
                <button type="button" id="confirmBtn_multiple"
                        class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition-colors duration-300">
                    Soumettre la Demande
                </button>
            </div>
        </form>
    </div>
</main>

<!-- Modal de Confirmation -->
<div id="confirmModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded shadow-md max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Confirmer Votre Demande</h3>
        <p class="mb-2"><strong>Point de Départ:</strong> <span id="confirmDepart"></span></p>
        <p class="mb-2"><strong>Point d'Arrivée:</strong> <span id="confirmArrive"></span></p>
        <p class="mb-2"><strong>Type de Véhicule:</strong> <span id="confirmTypeVehicule"></span></p>
        <div class="mb-4">
            <strong>Numéro(s) de Châssis:</strong>
            <div id="confirmChassis" class="mt-1 space-y-1"></div>
        </div>
        <p class="mb-2"><strong>Modèle:</strong> <span id="confirmModel"></span></p>

        <div class="flex justify-end space-x-4">
            <button id="cancelModal" type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
            <button id="submitConfirmed" type="button" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center justify-center min-w-[100px]">
                <span id="submitText">Confirmer</span>
                <svg id="submitSpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- JS logic -->
<script>
    function handleCityChange(selectId, inputId, hiddenInputName) {
        const selectEl = document.getElementById(selectId);
        const inputEl = document.getElementById(inputId);

        selectEl.addEventListener('change', () => {
            if (selectEl.value === 'other') {
                inputEl.classList.remove('hidden');
                inputEl.setAttribute('name', hiddenInputName);
                inputEl.value = '';
                inputEl.required = true;

                const existingHidden = selectEl.parentNode.querySelector(`input[type="hidden"][name="${hiddenInputName}"]`);
                if (existingHidden) existingHidden.remove();
            } else {
                inputEl.classList.add('hidden');
                inputEl.removeAttribute('name');
                inputEl.required = false;

                let hiddenInput = selectEl.parentNode.querySelector(`input[type="hidden"][name="${hiddenInputName}"]`);
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = hiddenInputName;
                    selectEl.parentNode.appendChild(hiddenInput);
                }
                hiddenInput.value = selectEl.value;
            }
        });

        selectEl.dispatchEvent(new Event('change'));
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize city selects for both forms
        handleCityChange('pointdepart_select_single', 'pointdepart_input_single', 'pointdepart');
        handleCityChange('poinarrive_select_single', 'poinarrive_input_single', 'poinarrive');
        handleCityChange('pointdepart_select_multiple', 'pointdepart_input_multiple', 'pointdepart');
        handleCityChange('poinarrive_select_multiple', 'poinarrive_input_multiple', 'poinarrive');

        // Single form elements
        const confirmBtnSingle = document.getElementById('confirmBtn_single');
        const chassisInputSingle = document.getElementById('chassis_single');
        const modelInputSingle = document.getElementById('model_single'); // Added model input
        const charCountSingle = document.getElementById('charCount_single');
        const formSingle = document.getElementById('transportFormSingle');
        const typeRadiosSingle = document.querySelectorAll('#transportFormSingle [name="typevehicule"]');

        // Multiple form elements
        const confirmBtnMultiple = document.getElementById('confirmBtn_multiple');
        const formMultiple = document.getElementById('transportFormMultiple');
        const chassisInputsMultiple = document.querySelectorAll('.chassis-input');
        const typeRadiosMultiple = document.querySelectorAll('#transportFormMultiple [name="typevehicule"]');

        // Modal elements
        const confirmModal = document.getElementById('confirmModal');
        const cancelModal = document.getElementById('cancelModal');
        const submitConfirmed = document.getElementById('submitConfirmed');
        let activeForm = null;

        // Function to validate single chassis form
        function validateSingleForm() {
            const chassisValid = chassisInputSingle.value.length === 17;
            const typeSelected = [...typeRadiosSingle].some(radio => radio.checked);
            
            if (chassisValid && typeSelected) {
                confirmBtnSingle.disabled = false;
                confirmBtnSingle.classList.remove('bg-gray-400', 'cursor-not-allowed');
                confirmBtnSingle.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            } else {
                confirmBtnSingle.disabled = true;
                confirmBtnSingle.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                confirmBtnSingle.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
        }

        // Function to validate multiple chassis form
        function validateMultipleForm() {
            const hasValidChassis = Array.from(chassisInputsMultiple).some(
                input => input.value.length === 17
            );
            const typeSelected = [...typeRadiosMultiple].some(radio => radio.checked);
            
            if (hasValidChassis && typeSelected) {
                confirmBtnMultiple.disabled = false;
                confirmBtnMultiple.classList.remove('bg-gray-400', 'cursor-not-allowed');
                confirmBtnMultiple.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            } else {
                confirmBtnMultiple.disabled = true;
                confirmBtnMultiple.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                confirmBtnMultiple.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
        }

        // Single chassis validation
        chassisInputSingle.addEventListener('input', validateSingleForm);
        
        // Add event listeners to radio buttons for single form
        typeRadiosSingle.forEach(radio => {
            radio.addEventListener('change', validateSingleForm);
        });

        // Add event listeners to chassis inputs for multiple form
        chassisInputsMultiple.forEach(input => {
            input.addEventListener('input', validateMultipleForm);
        });
        
        // Add event listeners to radio buttons for multiple form
        typeRadiosMultiple.forEach(radio => {
            radio.addEventListener('change', validateMultipleForm);
        });

        // Initialize validation states
        validateSingleForm();
        validateMultipleForm();

        // Single form confirmation
        confirmBtnSingle.addEventListener('click', (e) => {
            e.preventDefault();
            activeForm = formSingle;
            
            // Get values
            const pointDepart = document.querySelector('#transportFormSingle [name="pointdepart"]').value;
            const pointArrive = document.querySelector('#transportFormSingle [name="poinarrive"]').value;
            const chassis = chassisInputSingle.value;
            const typeVehicule = document.querySelector('#transportFormSingle [name="typevehicule"]:checked').value;
            const model = modelInputSingle.value; // Get model value

            // Set modal values
            document.getElementById('confirmDepart').textContent = pointDepart;
            document.getElementById('confirmArrive').textContent = pointArrive;
            document.getElementById('confirmChassis').innerHTML = `<div>${chassis}</div>`;
            document.getElementById('confirmTypeVehicule').textContent = 
                typeVehicule === 'vp' ? 'VP (Véhicule Particulier Citadin)' : 'VU (Véhicule Utilitaire Long)';
            document.getElementById('confirmModel').textContent = model || 'Non spécifié'; // Display model

            // Show modal
            confirmModal.classList.remove('hidden');
        });

        // Multiple form confirmation
        confirmBtnMultiple.addEventListener('click', (e) => {
            e.preventDefault();
            activeForm = formMultiple;
            
            // Get values
            const pointDepart = document.querySelector('#transportFormMultiple [name="pointdepart"]').value;
            const pointArrive = document.querySelector('#transportFormMultiple [name="poinarrive"]').value;
            
            // Get all non-empty chassis values
            const chassisValues = Array.from(chassisInputsMultiple)
                .map(input => input.value.trim())
                .filter(value => value.length > 0);

            // Validate at least one chassis is entered
            if (chassisValues.length === 0) {
                alert('Veuillez entrer au moins un numéro de châssis');
                return;
            }

            // Validate each entered chassis has 17 characters
            const invalidChassis = chassisValues.some(chassis => chassis.length !== 17);
            if (invalidChassis) {
                alert('Chaque numéro de châssis doit contenir exactement 17 caractères');
                return;
            }

            // Get vehicle type
            const typeVehicule = document.querySelector('#transportFormMultiple [name="typevehicule"]:checked').value;

            // Set modal values
            document.getElementById('confirmDepart').textContent = pointDepart;
            document.getElementById('confirmArrive').textContent = pointArrive;
            
            // Display entered chassis numbers
            const chassisHtml = chassisValues
                .map(chassis => `<div>${chassis}</div>`)
                .join('');
            document.getElementById('confirmChassis').innerHTML = chassisHtml;
            document.getElementById('confirmTypeVehicule').textContent = 
                typeVehicule === 'vp' ? 'VP (Véhicule Particulier Citadin)' : 'VU (Véhicule Utilitaire Long)';

            // Show modal
            confirmModal.classList.remove('hidden');
        });

        // Modal cancel button
        cancelModal.addEventListener('click', () => {
            confirmModal.classList.add('hidden');
            activeForm = null;
        });

        // Modal confirm button
        submitConfirmed.addEventListener('click', () => {
            if (activeForm) {
                // Show loading state
                submitConfirmed.disabled = true;
                document.getElementById('submitText').classList.add('hidden');
                document.getElementById('submitSpinner').classList.remove('hidden');
                cancelModal.disabled = true;
                
                // Submit the active form
                activeForm.submit();
            }
        });
    });
</script>


<script>
    function checkNewTransports() {
        fetch('{{ route('secretaire.checkNewTransports') }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notifBadge');
                const lastSeen = localStorage.getItem('lastSeenTransports') || 0;

                if (data.count > parseInt(lastSeen)) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error checking transports:', error));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const dispoButton = document.getElementById('dispoButton');

        if (dispoButton) {
            dispoButton.addEventListener('click', function (e) {
                e.preventDefault(); // prevent immediate navigation

                // First: fetch current count
                fetch('{{ route('secretaire.checkNewTransports') }}')
                    .then(response => response.json())
                    .then(data => {
                        localStorage.setItem('lastSeenTransports', data.count);

                        // After setting it, redirect to demandesDisponibilite
                        window.location.href = '{{ route('secretaire.demandesDisponibilite') }}';
                    });
            });
        }

        checkNewTransports();
        setInterval(checkNewTransports, 10000); // every 10s
    });
</script>




</body>
</html>