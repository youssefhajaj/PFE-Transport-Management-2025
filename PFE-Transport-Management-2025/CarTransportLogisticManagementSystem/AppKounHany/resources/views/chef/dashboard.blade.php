<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chef Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-semibold text-gray-800">Dashboard - {{ Auth::user()->name }}</div>
    <div class="space-x-4">
        <a href="{{ route('chef.historie') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Historie</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Logout</button>
        </form>
    </div>
</nav>

<!-- Main Content -->
<main class="max-w-full mx-auto mt-1 p-1 bg-white rounded shadow-sm">
    @if ($transports->isEmpty())
        {{-- <p class="text-gray-500">No requests to validate at the moment.</p> --}}
    @else
        <table class="w-full bg-white shadow-md rounded overflow-hidden table-auto">
            <thead class="bg-gray-200 text-gray-700 text-sm">
                <tr>
                    <th class="py-2 px-1 text-center">Date</th>
                    <th class="py-2 px-1 text-center">Société</th>
                    <th class="py-2 px-1 text-center">Départ</th>
                    <th class="py-2 px-1 text-center">Arrivée</th>
                    <th class="py-2 px-1 text-center">Châssis</th>
                    <th class="py-2 px-1 text-center">type</th>
                    <th class="py-2 px-1 text-center">Model</th>
                    <th class="py-2 px-1 text-center">Coordinateurs</th>
                    <th class="py-2 px-1 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center text-sm">
                @foreach ($transports as $transport)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->created_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->societeUser->societe ?? '' }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->pointdepart }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->poinarrive }}</td>
                        <td class="py-2 px-1  border border-gray-300">
                            @if(str_contains($transport->chassis, ','))
                                <button onclick="showChassisModal('{{ $transport->chassis }}')" 
                                        class="text-blue-600 hover:underline">
                                    Voir
                                </button>
                            @else
                                {{ $transport->chassis }}
                            @endif
                        </td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->typevehicule ?? '' }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->model ?? '' }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->nameUser->name ?? '' }}</td>
                        
                        <td class="py-2 px-1   flex gap-2">
                            <button onclick="showConfirmModal('validate', {{ $transport->id }})" 
                                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 flex items-center justify-center">
                                <span id="validateText-{{ $transport->id }}">Validate</span>
                                <svg id="validateSpinner-{{ $transport->id }}" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                            <button onclick="showConfirmModal('refuse', {{ $transport->id }})" 
                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 flex items-center justify-center">
                                <span id="refuseText-{{ $transport->id }}">Refuse</span>
                                <svg id="refuseSpinner-{{ $transport->id }}" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Chassis List Modal -->
    <div id="chassisModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded shadow-md max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Liste des Numéros de Châssis</h3>
            <div id="chassisList" class="max-h-60 overflow-y-auto">
                <!-- Chassis numbers will be inserted here -->
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="document.getElementById('chassisModal').classList.add('hidden')" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded shadow-md max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4 text-gray-800" id="confirmModalTitle">Confirmer l'action</h3>
            <p class="mb-4" id="confirmModalMessage">Êtes-vous sûr de vouloir effectuer cette action ?</p>
            <form id="confirmModalForm" method="POST" onsubmit="showLoading(this)">
                @csrf
                @method('PUT')
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="document.getElementById('confirmModal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-white rounded flex items-center justify-center" id="confirmModalButton">
                        <span id="confirmModalButtonText">Confirmer</span>
                        <svg id="confirmModalSpinner" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $transports->appends(request()->query())->links() }}
    </div>
</main>

<script>
    function showChassisModal(chassisString) {
        const modal = document.getElementById('chassisModal');
        const chassisList = document.getElementById('chassisList');
        
        // Clear previous content
        chassisList.innerHTML = '';
        
        // Split the comma-separated string and create list items
        const chassisNumbers = chassisString.split(',');
        chassisNumbers.forEach(chassis => {
            const div = document.createElement('div');
            div.className = 'p-2 border-b border-gray-100';
            div.textContent = chassis.trim();
            chassisList.appendChild(div);
        });
        
        // Show the modal
        modal.classList.remove('hidden');
    }

    function showConfirmModal(action, transportId) {
        const modal = document.getElementById('confirmModal');
        const form = document.getElementById('confirmModalForm');
        const title = document.getElementById('confirmModalTitle');
        const message = document.getElementById('confirmModalMessage');
        const button = document.getElementById('confirmModalButton');
        const buttonText = document.getElementById('confirmModalButtonText');

        // Set modal content based on action
        if (action === 'validate') {
            title.textContent = 'Confirmer la validation';
            message.textContent = 'Êtes-vous sûr de vouloir valider cette demande de transport ?';
            buttonText.textContent = 'Valider';
            button.className = 'px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center justify-center';
            form.action = `/chef/validate/${transportId}`;
        } else {
            title.textContent = 'Confirmer le refus';
            message.textContent = 'Êtes-vous sûr de vouloir refuser cette demande de transport ?';
            buttonText.textContent = 'Refuser';
            button.className = 'px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 flex items-center justify-center';
            form.action = `/chef/refuse/${transportId}`;
        }

        // Show the modal
        modal.classList.remove('hidden');
    }

    function showLoading(form) {
        // Show loading spinner in confirmation modal
        const spinner = document.getElementById('confirmModalSpinner');
        const buttonText = document.getElementById('confirmModalButtonText');
        const submitButton = form.querySelector('button[type="submit"]');
        
        submitButton.disabled = true;
        buttonText.classList.add('hidden');
        spinner.classList.remove('hidden');
        
        // Also show loading on the original button
        const transportId = form.action.split('/').pop();
        const action = form.action.includes('validate') ? 'validate' : 'refuse';
        
        if (action === 'validate') {
            document.getElementById(`validateText-${transportId}`).classList.add('hidden');
            document.getElementById(`validateSpinner-${transportId}`).classList.remove('hidden');
        } else {
            document.getElementById(`refuseText-${transportId}`).classList.add('hidden');
            document.getElementById(`refuseSpinner-${transportId}`).classList.remove('hidden');
        }
    }
</script>

</body>
</html>