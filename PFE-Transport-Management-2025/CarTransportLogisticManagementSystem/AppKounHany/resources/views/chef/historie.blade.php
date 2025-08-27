<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historie - Chef</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-semibold text-gray-800">Historie - {{ Auth::user()->name }}</div>

    <div class="space-x-4">
        <a href="{{ route('chef.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Logout</button>
        </form>
    </div>
</nav>

<!-- Main Content -->
<main class="max-w-full mx-auto mt-1 p-1 bg-white rounded shadow-sm">
    @if ($transports->isEmpty())
        <p class="text-gray-500">Aucune demande trouvée.</p>
    @else
        <table class="w-full bg-white shadow-md rounded overflow-hidden table-auto">
            <thead class="bg-gray-200 text-gray-700 text-sm">
                <tr>
                    <th class="py-2 px-2 text-center">Départ</th>
                    <th class="py-2 px-2 text-center">Arrivée</th>
                    <th class="py-2 px-2 text-center">Châssis</th>
                    <th class="py-2 px-2 text-center">Type</th>
                    <th class="py-2 px-2 text-center">Model</th>
                    <th class="py-2 px-2 text-center">Coordinateurs</th>
                    <th class="py-2 px-2 text-center">Société</th>
                    <th class="py-2 px-2 text-center">Statut</th>
                    <th class="py-2 px-2 text-center">Date</th>
                    <th class="py-2 px-2 text-center">Etat</th>
                    <th class="py-2 px-2 text-center">Disponibilite</th>
                    <th class="py-2 px-2 text-center">Commentaire</th>
                </tr>
            </thead>
            <tbody class="text-center text-sm">
                @foreach ($transports as $transport)
                    <tr class="border-t hover:bg-gray-50">
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
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->societeUser->societe ?? '' }}</td>
                        <td class="py-2 px-1  border border-gray-300">
                            @if ($transport->chefvalid === 1)
                                <span class="text-green-600 font-semibold">✅ Validé</span>
                            @elseif ($transport->chefvalid === 2)
                                <span class="text-red-600 font-semibold">❌ Refusé</span>
                            @else
                                <span class="text-gray-500">⏳ En attente</span>
                            @endif
                        </td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->created_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{$transport->etatavancement}}</td>
                        <td class="py-2 px-1  border border-gray-300">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $transport->disponibilite === 'disponible' ? 
                                   ' text-green-800' : ' text-red-800' }}">
                                {{ ucfirst($transport->disponibilite) }}
                            </span>
                        </td>
                        <td class="py-2 px-1">
                            @if (!empty($transport->commentaire))
                                <button 
                                    onclick="openCommentModal(`{{ addslashes($transport->commentaire) }}`)" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-1 px-3 rounded shadow">
                                    Voir
                                </button>
                            @else
                                <button 
                                    disabled 
                                    class="bg-gray-400 text-white text-sm font-medium py-1 px-3 rounded cursor-not-allowed">
                                    Aucun
                                </button>
                            @endif
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

    <!-- Commentaire Modal -->
    <div id="commentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white w-11/12 max-w-md p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Commentaire</h3>
            <p id="commentContent" class="text-gray-700 whitespace-pre-line"></p>
            <div class="mt-6 text-right">
                <button onclick="closeCommentModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
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

    function openCommentModal(comment) {
        document.getElementById('commentContent').textContent = comment;
        document.getElementById('commentModal').classList.remove('hidden');
        document.getElementById('commentModal').classList.add('flex');
    }

    function closeCommentModal() {
        document.getElementById('commentModal').classList.remove('flex');
        document.getElementById('commentModal').classList.add('hidden');
    }
</script>

</body>
</html>