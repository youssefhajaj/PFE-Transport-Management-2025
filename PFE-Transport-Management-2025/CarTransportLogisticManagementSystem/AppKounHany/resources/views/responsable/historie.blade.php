<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historie - Responsable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<nav class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-semibold text-gray-800">Historie - Responsable</div>

    <div class="space-x-4">
        <a href="{{ route('responsable.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Logout</button>
        </form>
    </div>
</nav>

<main class="max-w-full mx-auto mt-1 p-1 bg-white rounded shadow-sm">
    @if ($transports->isEmpty())
        <p class="text-gray-500">Aucune demande trouvée.</p>
    @else
        <table class="w-full bg-white shadow-md rounded overflow-hidden table-auto">
            <thead class="bg-gray-200 text-gray-700 text-sm">
                <tr>
                    <th class="py-2 px-1 text-center">Départ</th>
                    <th class="py-2 px-1 text-center">Arrivée</th>
                    <th class="py-2 px-1 text-center">Châssis</th>
                    <th class="py-2 px-1 text-center">Type</th>
                    <th class="py-2 px-1 text-center">Model</th>
                    <th class="py-2 px-1 text-center">Coordinateurs</th>
                    <th class="py-2 px-1 text-center">Chef</th>
                    <th class="py-2 px-1 text-center">Responsable</th>
                    <th class="py-2 px-1 text-center">Statut</th>
                    <th class="py-2 px-1 text-center">Prestataire</th> 
                    <th class="py-2 px-1 text-center">BL</th>
                    <th class="py-2 px-1 text-center">BL_cachet</th>
                    <th class="py-2 px-1 text-center">Date</th>
                    <th class="py-2 px-1 text-center">Etat</th>
                    <th class="py-2 px-1 text-center">Etat_Commentaire</th>
                    <th class="py-2 px-1 text-center">Disponibilite</th>
                    <th class="py-2 px-1 text-center">Commentaire</th>
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
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->chefUser->name ?? '' }}<br>{{ $chefStatus }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{ $respStatus }}</td>
                        <td class="py-2 px-1  border border-gray-300 font-semibold">{{ $overallStatus }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->prestataire ?? '' }}</td>
                        {{-- ******************************************************************************************************** --}}

<td class="py-2 px-1  border border-gray-300">
    @if ($overallStatus === '✅ Validé' && !empty($transport->prestataire))
        <button onclick="delayedDownload('{{ route('transports.downloadBL', $transport->id) }}', '{{ $transport->id }}')"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded shadow inline-flex items-center"
            id="download-button-{{ $transport->id }}">
            
            <svg id="download-spinner-{{ $transport->id }}" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 hidden animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            
            <span id="download-text-{{ $transport->id }}">Télécharger</span>
        </button>
    @else
        <button disabled 
            class="bg-gray-400 text-white text-sm font-medium py-1 px-3 rounded cursor-not-allowed">
            Télécharger
        </button>
    @endif
</td>


<td class="py-2 px-1  border border-gray-300">
    @if ($transport->BL_cachet)
        <!-- Download direct -->
        <a href="{{ route('transports.cachet', $transport->id) }}"
        class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-1 px-3 rounded shadow mb-1 inline-block">
            Télécharger
        </a>


        <!-- Conserver le bouton d’upload si besoin -->
        <button onclick="openUploadModal({{ $transport->id }})"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded shadow">
            Uploader&nbsp;Nouveau
        </button>
    @elseif ($overallStatus === '✅ Validé' && !empty($transport->prestataire))
        <button onclick="openUploadModal({{ $transport->id }})"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded shadow">
            Uploader !
        </button>
    @endif
</td>
                        {{-- ******************************************************************************************************** --}}
                        <td class="py-2 px-1  border border-gray-300">{{ $transport->created_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-1  border border-gray-300">{{$transport->etatavancement}}</td>
                        <td class="py-2 px-1  border border-gray-300">
                            @if (empty($transport->etat_commentaire))
                                <button class="bg-gray-400 text-white text-sm font-medium py-1 px-3 rounded cursor-not-allowed" disabled>
                                    Aucun
                                </button>
                            @else
                                <button onclick="document.getElementById('view-comment-modal-{{ $transport->id }}').classList.remove('hidden')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded shadow">
                                    Voir
                                </button>
                            @endif
                        </td>

                        <td class="py-2 px-1  border border-gray-300">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $transport->disponibilite === 'disponible' ? 
                                   ' text-green-800' : ' text-red-800' }}">
                                {{ ucfirst($transport->disponibilite) }}
                            </span>
                        </td>
                        <td class="py-2 px-1  border border-gray-300">
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


    <!-- Upload Cachet Modal -->
<div id="uploadCachetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white w-11/12 max-w-md p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Uploader le BL Cachet</h3>
        <form id="uploadCachetForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="file" name="BL_cachet" class="w-full p-2 border rounded mb-4" required>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeUploadModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Annuler
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Uploader
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Cachet Modal -->
<div id="viewCachetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white w-11/12 max-w-4xl p-6 rounded-lg shadow-lg" style="max-height: 90vh;">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">BL Cachet</h3>
        <div class="flex justify-center mb-4" style="height: 70vh;">
            <iframe id="cachetFrame" src="" class="w-full h-full border" frameborder="0"></iframe>
        </div>
        <div class="flex justify-end space-x-4">
            <button onclick="closeCachetModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Fermer
            </button>
            <button onclick="openUploadModalFromView()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Uploader Nouveau
            </button>
        </div>
    </div>
</div>


{{-- modal etat commentaire --}}
@foreach ($transports as $transport)
    @if (!empty($transport->etat_commentaire))
        <!-- View Comment Modal -->
        <div id="view-comment-modal-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-semibold mb-4">Commentaire</h2>
                <div class="border p-4 rounded bg-gray-50 mb-4">
                    {{ $transport->etat_commentaire }}
                </div>
                <div class="flex justify-end">
                    <button onclick="document.getElementById('view-comment-modal-{{ $transport->id }}').classList.add('hidden')"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif
@endforeach



        <!-- Modal -->
<div id="blModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white w-11/12 max-w-6xl max-h-[90vh] rounded-lg shadow-lg flex flex-col" style="height: 90vh;">
        <div class="flex-grow overflow-auto p-4">
            <iframe id="pdfFrame" src="" class="w-full h-full border" frameborder="0"></iframe>
        </div>
        
        <div class="p-4 border-t">
            <form id="blForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Créer BL
                    </button>
                </div>
            </form>
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



        function openModal(transportId) {
        const url = `/transports/${transportId}/generate-bl`;
        document.getElementById('pdfFrame').src = url;
        
        // Set the form action
        const form = document.getElementById('blForm');
        form.action = `/transports/${transportId}/fill-bl`;
        
        document.getElementById('blModal').classList.remove('hidden');
        document.getElementById('blModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('pdfFrame').src = '';
        document.getElementById('blModal').classList.remove('flex');
        document.getElementById('blModal').classList.add('hidden');
    }
    function showDownloadSpinner(button, id) {
    const spinner = document.getElementById(`download-spinner-${id}`);
    const text = document.getElementById(`download-text-${id}`);
    
    if (spinner && text) {
        text.textContent = 'Téléchargement...';
        spinner.classList.remove('hidden');
    }

    button.disabled = true;

    // Optional fallback after 5 seconds
    setTimeout(() => {
        if (spinner && text) {
            text.textContent = 'Télécharger';
            spinner.classList.add('hidden');
        }
        button.disabled = false;
    }, 5000);
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


// Cachet Modal Functions
function openUploadModal(transportId) {
    const form = document.getElementById('uploadCachetForm');
    form.action = `/transports/${transportId}/update-cachet`;
    document.getElementById('uploadCachetModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadCachetModal').classList.add('hidden');
}
</script>
</body>
</html>