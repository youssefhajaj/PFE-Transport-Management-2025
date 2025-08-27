<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Responsable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<nav class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-semibold text-gray-800">Dashboard - Responsable</div>

    <div class="space-x-4 flex items-center">
        <!-- 🕓 Historie Button -->
        <a href="{{ route('responsable.historie') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Historie
        </a>

        <!-- 🔒 Logout Button -->
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Logout</button>
        </form>
    </div>
</nav>

<main class="max-w-full mx-auto mt-1 p-1 bg-white rounded shadow-sm">
    @if ($transports->isEmpty())
        {{-- <p class="text-gray-500">No pending transport requests found.</p> --}}
    @else
        <table class="w-full bg-white shadow-md rounded overflow-hidden table-auto">
            <thead class="bg-gray-200 text-gray-700 text-sm">
                <tr>
                    <th class="py-1 px-2 text-center">Départ</th>
                    <th class="py-1 px-2 text-center">Arrivée</th>
                    <th class="py-1 px-2 text-center">Châssis</th>
                    <th class="py-1 px-2 text-center">Type</th>
                    <th class="py-1 px-2 text-center">Model</th>
                    <th class="py-1 px-2 text-center">Coordinateurs</th>
                    <th class="py-1 px-2 text-center">Chef</th>
                    <th class="py-1 px-2 text-center">Date</th>
                    <th class="py-1 px-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach ($transports as $transport)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-1 px-2 text-center border border-gray-300">{{ $transport->pointdepart }}</td>
                        <td class="py-1 px-2 text-center border border-gray-300">{{ $transport->poinarrive }}</td>
                        <td class="py-1 px-2 text-center border border-gray-300">
                            @if(str_contains($transport->chassis, ','))
                                <button onclick="showChassisModal('{{ $transport->chassis }}')" 
                                        class="text-blue-600 hover:underline">
                                    Voir
                                </button>
                            @else
                                {{ $transport->chassis }}
                            @endif
                        </td>
                        <td class="py-1 px-2 text-center border border-gray-300">{{ $transport->typevehicule ?? '' }}</td>
                        <td class="py-1 px-2 text-center border border-gray-300">{{ $transport->model ?? '' }}</td>
                        <td class="py-1 px-2 text-center border border-gray-300">{{ $transport->nameUser->name ?? '' }}</td>
                        
                        @php
                            $chefStatus = match($transport->chefvalid) {
                                1 => '✅ Validé',
                                2 => '❌ Refusé',
                                default => '⏳ En attente',
                            };
                        @endphp
                        <td class="py-1 px-2 text-center border border-gray-300">{{ $transport->chefUser->name ?? ''}}<br>{{ $chefStatus }}</td>
                        <td class="py-1 px-2 text-center border border-gray-300">{{ $transport->created_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
                        <td class="py-1 px-2 text-center flex gap-2">
                            <form method="POST" action="{{ route('responsable.validate', $transport->id) }}" onsubmit="event.preventDefault(); openModal(this, 'validate');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                    Validate
                                </button>
                            </form>

                            <form method="POST" action="{{ route('responsable.refuse', $transport->id) }}" onsubmit="event.preventDefault(); openModal(this, 'refuse');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                    Refuse
                                </button>
                            </form>

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

    <!-- Pagination -->
    <div class="mt-6">
        {{ $transports->appends(request()->query())->links() }}
    </div>
</main>


<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded shadow max-w-sm w-full">
        <h2 class="text-lg font-semibold text-gray-800 mb-4" id="modalTitle">Confirmer l'action</h2>
        <p class="text-sm text-gray-600 mb-4" id="modalMessage">Êtes-vous sûr de vouloir continuer ?</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">Annuler</button>
            <button id="confirmButton" onclick="submitAction()" class="px-4 py-2 bg-blue-600 text-white rounded flex items-center">
                <svg id="spinner" class="w-4 h-4 mr-2 hidden animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Confirmer
            </button>
        </div>
    </div>
</div>


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
</script>


<script>
    let actionForm = null;

    function openModal(form, actionType) {
        actionForm = form;
        document.getElementById('modalTitle').textContent = actionType === 'validate' ? 'Confirmer la validation' : 'Confirmer le refus';
        document.getElementById('modalMessage').textContent = actionType === 'validate'
            ? 'Êtes-vous sûr de vouloir valider cette demande ?'
            : 'Êtes-vous sûr de vouloir refuser cette demande ?';
        document.getElementById('confirmationModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        actionForm = null;
    }

    function submitAction() {
        const spinner = document.getElementById('spinner');
        const confirmButton = document.getElementById('confirmButton');
        spinner.classList.remove('hidden');
        confirmButton.setAttribute('disabled', 'true');
        actionForm.submit();
    }
</script>

</body>
</html>