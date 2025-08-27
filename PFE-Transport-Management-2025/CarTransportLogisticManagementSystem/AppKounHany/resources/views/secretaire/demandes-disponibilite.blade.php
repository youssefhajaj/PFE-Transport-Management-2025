<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demande Disponibilité Châssis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-semibold text-gray-800">Demande Disponibilité Châssis</div>
    <div class="space-x-4">
        <a href="{{ route('secretaire.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Tableau de Bord</a>
        <a href="{{ route('secretaire.historie') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Historie</a>
        <a href="{{ route('secretaire.demandesDisponibilite') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition  cursor-not-allowed opacity-50">Disponibilité</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Logout</button>
        </form>
    </div>
</nav>

<!-- Main Content -->
<div class="w-full mt-8 px-4">
    <h2 class="text-2xl font-bold mb-4">Demandes de disponibilité</h2>

    @if ($transports->isEmpty())
        <p class="text-gray-600">Aucune demande trouvée.</p>
    @else
        <table class="w-full bg-white shadow-md rounded overflow-hidden">
            <thead class="bg-gray-200 text-gray-700 text-center text-sm">
                <tr>
                    <th class="py-2 px-1 text-center">Date</th>
                    <th class="py-2 px-1 text-center">Châssis</th>
                    <th class="py-2 px-1 text-center">Type</th>
                    <th class="py-2 px-1 text-center">Model</th>
                    <th class="py-2 px-1 text-center">Départ</th>
                    <th class="py-2 px-1 text-center">Arrivée</th>
                    <th class="py-2 px-1 text-center">Coordinateurs</th>
                    <th class="py-2 px-1 text-center">Chef</th>
                    <th class="py-2 px-1 text-center">Responsable</th>
                    <th class="py-2 px-1 text-center">Statut</th>
                    <th class="py-2 px-1 text-center">Prestataire</th>
                    <th class="py-2 px-4 text-center">Disponibilité</th>
                    <th class="py-2 px-4 text-center">Commentaire</th>
                </tr>
            </thead>
            <tbody class="text-center text-sm">
                @foreach ($transports as $transport)
                @php
                    $chefStatus = match($transport->chefvalid) {
                        1 => '✅ Validé',
                        2 => '❌ Refusé',
                        default => '⏳ En attente',
                    };
                
                    $respStatus = '';
                    if ($transport->chefvalid !== 2 && $transport->needtobevalid == 1) {
                        $respStatus = match($transport->responsablevalid) {
                            1 => '✅ Validé',
                            2 => '❌ Refusé',
                            default => '⏳ En attente',
                        };
                    }
                
                    $overallStatus = '⏳ En cours';
                    if ($transport->chefvalid === 2 || $transport->responsablevalid === 2) {
                        $overallStatus = '❌ Refusé';
                    } elseif ($transport->chefvalid === 1 && ($transport->responsablevalid === 1 || $transport->needtobevalid == 0)) {
                        $overallStatus = '✅ Validé';
                    }
                @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-2 px-1 text-center">{{ $transport->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->chassis ?? '' }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->typevehicule ?? '' }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->model ?? '' }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->pointdepart ?? '' }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->poinarrive ?? '' }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->nameUser->name ?? '' }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->chefUser->name ?? '' }}<br>{{ $chefStatus }}</td>
                        <td class="py-2 px-1 text-center">{{ $respStatus }}</td>
                        <td class="py-2 px-1 text-center font-semibold">{{ $overallStatus }}</td>
                        <td class="py-2 px-1 text-center">{{ $transport->prestataire ?? '' }}</td>
                        <td class="py-2 px-1 text-center">
                            <span class="font-semibold {{ $transport->disponibilite === 'disponible' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transport->disponibilite ?? '' }}
                            </span>
                            <button onclick="openEditModal({{ $transport->id }}, 'disponibilite')" 
                                    class="ml-2 text-blue-600 hover:text-blue-800">
                                ✏️
                            </button>
                        </td>
                        <td class="py-2 px-1 text-center">
                            {{ $transport->commentaire ?? '-' }}
                            <button onclick="openEditModal({{ $transport->id }}, 'commentaire')" 
                                    class="ml-2 text-blue-600 hover:text-blue-800">
                                ✏️
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $transports->links() }}
        </div>
    @endif
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-bold mb-4"></h3>
        <div id="modalContent"></div>
        <input type="hidden" id="transportId">
        <input type="hidden" id="fieldType">
        
        <div class="flex justify-end space-x-4 mt-4">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Annuler
            </button>
            <button onclick="saveChanges()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Enregistrer
            </button>
        </div>
    </div>
</div>

<script>
    function openEditModal(transportId, fieldType) {
        document.getElementById('transportId').value = transportId;
        document.getElementById('fieldType').value = fieldType;
        
        const modal = document.getElementById('editModal');
        const title = document.getElementById('modalTitle');
        const content = document.getElementById('modalContent');
        
        if (fieldType === 'disponibilite') {
            title.textContent = 'Modifier Disponibilité';
            content.innerHTML = `
                <select id="editValue" class="w-full border px-3 py-2 rounded">
                    <option value="disponible">Disponible</option>
                    <option value="non disponible">Non disponible</option>
                    <option value="Chassis au Showroom">Chassis au Showroom</option>
                </select>
            `;
        } else {
            title.textContent = 'Modifier Commentaire';
            content.innerHTML = `
                <textarea id="editValue" class="w-full border px-3 py-2 rounded" rows="4"></textarea>
            `;
        }
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function saveChanges() {
        const transportId = document.getElementById('transportId').value;
        const fieldType = document.getElementById('fieldType').value;
        const value = document.getElementById('editValue').value;
        
        axios.put(`/transports/${transportId}/update-field`, {
            field: fieldType,
            value: value
        })
        .then(response => {
            location.reload(); // Refresh to see changes
        })
        .catch(error => {
            console.error(error);
            alert('Erreur lors de la mise à jour');
        });
    }
</script>

</body>
</html>