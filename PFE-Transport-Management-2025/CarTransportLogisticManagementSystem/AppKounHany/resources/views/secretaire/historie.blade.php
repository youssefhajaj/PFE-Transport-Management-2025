<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historie</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">

    <style>
#chassisList::-webkit-scrollbar {
    width: 5px;
}
#chassisList::-webkit-scrollbar-track {
    background: #f1f1f1;
}
#chassisList::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}
#chassisList::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<!-- Navbar -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-semibold text-gray-800">Historie des Transports</div>

    <div class="space-x-4">
        <a href="{{ route('secretaire.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition ">Tableau de Bord</a>
        <a href="{{ route('secretaire.historie') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition cursor-not-allowed opacity-50">Historique</a>
        <a href="{{ route('secretaire.demandesDisponibilite') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Disponibilite</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Déconnexion</button>
        </form>
    </div>
</nav>

<!-- Main Content -->
<div class="w-full mt-1 p-1">

    {{-- <h2 class="text-2xl font-bold mb-4">Mes demandes de transport</h2> --}}

    @if ($transports->isEmpty())
        <p class="text-gray-600">Aucune demande de transport trouvée.</p>
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
                    <th class="py-2 px-1 text-center">Validé par Chef</th>
                    <th class="py-2 px-1 text-center">Validé par Responsable</th>
                    <th class="py-2 px-1 text-center">Statut</th>
                    <th class="py-2 px-1 text-center">Prestataire</th>
                    <th class="py-2 px-1 text-center">BL</th>
                    <th class="py-2 px-1 text-center">BL_cachet</th>
                    <th class="py-2 px-1 text-center">Envoyer BL</th>
                    <th class="py-2 px-1 text-center">Etat</th>
                    <th class="py-2 px-1 text-center">Disponibilite</th>
                    <th class="py-2 px-1 text-center">Commentaire</th>
                    {{-- <th class="py-2 px-1 text-center">Actions</th> --}}
                    
                </tr>
            </thead>
            <tbody class="text-center text-sm">
                @foreach ($transports as $transport)
                    @php
                        $chefStatus = match($transport->chefvalid) {
                            0 => '⏳ En cours',
                            1 => '✅ Validé',
                            2 => '❌ Refusé',
                        };

                        $respStatus = '';
                        if ($transport->needtobevalid == 1) {
                            $respStatus = match($transport->responsablevalid) {
                                0 => '⏳ En cours',
                                1 => '✅ Validé',
                                2 => '❌ Refusé',
                            };
                        }

                        if ($transport->chefvalid === 2 || $transport->responsablevalid === 2) {
                            $overallStatus = '❌ Refusé';
                        } elseif ($transport->chefvalid === 1 && ($transport->responsablevalid === 1 || $respStatus == '')) {
                            $overallStatus = '✅ Validé';
                        } elseif ($transport->chefvalid === 0 || ($transport->needtobevalid === 1 && $transport->responsablevalid === 0)) {
                            $overallStatus = '⏳ En cours';
                        } else {
                            $overallStatus = '';
                        }
                    @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-2 px-1 text-center border-r border-gray-200">{{ $transport->created_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
                        <td class="px-1 py-2   text-center border-r border-gray-200 whitespace-nowrap">
                            @if(str_contains($transport->chassis, ','))
                                <button onclick="showChassisModal('{{ $transport->chassis }}')" 
                                        class="text-blue-600 hover:underline">
                                    Voir
                                </button>
                            @else
                                {{ $transport->chassis }}
                            @endif
                        </td>
                        <td class="py-2 px-1 text-center border-r border-gray-200">{{ $transport->typevehicule }}</td>
                        <td class="py-2 px-1 text-center border-r border-gray-200">{{ $transport->model }}</td>
                        <td class="py-2 px-1 text-center border-r border-gray-200">{{ $transport->pointdepart }}</td>
                        <td class="py-2 px-1 text-center border-r border-gray-200">{{ $transport->poinarrive }}</td>
                        <td class="py-2 px-1  text-center border-r border-gray-200">{{$transport->chefUser->name ?? '' }}{{ $chefStatus }}</td>
                        <td class="py-2 px-1  text-center border-r border-gray-200">{{$transport->responsableUser->name ?? '' }}{{ $respStatus }}</td>
                        <td class="py-2 px-1  text-center border-r border-gray-200 font-semibold">{{ $overallStatus }}</td>
                        <td class="py-2 px-1  text-center border-r border-gray-200">{{ $transport->prestataire ?? '-' }}</td>
<td class="py-2 px-1  text-center border-r border-gray-200">
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


<td class="py-2 px-1   text-center border-r border-gray-200">
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

{{-- *************************envoyer mail************************* --}}
                        <td class="py-3 px-1 text-center border border-gray-300">
                            @if($transport->bl_sent_at)
                                {{-- Show only the sent date --}}
                                <div class="text-xs text-gray-500 mt-1">
                                    Envoyé le {{ $transport->bl_sent_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}
                                </div>
                            @else
                                {{-- Show form with button if not sent yet --}}
                                <form action="{{ route('transports.sendBL', $transport->id) }}" method="POST" onsubmit="handleSubmit(this)">
                                    @csrf
                                    @if (!empty($transport->BL_cachet) && !empty($transport->prestataire))
                                        <button type="submit" 
                                                class="flex items-center justify-center gap-2 px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 submit-btn">
                                            <span class="btn-text">Envoyer Email</span>
                                            <svg class="hidden animate-spin h-4 w-4 text-white spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" class="px-3 py-1 bg-gray-300 text-gray-500 cursor-not-allowed" disabled>
                                            Envoyer Email
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </td>

                        <td class="py-2 px-1  text-center border-r border-gray-200">{{$transport->etatavancement}}</td>
                        <td class="py-2 px-1  text-center border-r border-gray-200">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $transport->disponibilite === 'disponible' ? 
                                   ' text-green-800' : ' text-red-800' }}">
                                {{ ucfirst($transport->disponibilite) }}
                            </span>
                        </td>
                        <td class="py-2 px-1  text-center border-r border-gray-200">
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


                        {{-- supprimer button --}}
                        {{-- <td class="py-2 px-1 text-center">
                            <button onclick="showDeleteTransportModal('{{ $transport->id }}', '{{ $transport->chassis }}', '{{ $transport->pointdepart }}', '{{ $transport->poinarrive }}')" 
                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td> --}}

                        
                        
                        
                    </tr>
                @endforeach
            </tbody>
        </table>


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

<!-- Delete Transport Modal -->
<div id="deleteTransportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Confirmer la suppression</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cette commande de transport?</p>
                <div id="transportToDeleteInfo" class="text-sm text-left mt-2 space-y-1">
                    <p><span class="font-medium">Châssis:</span> <span id="modalChassis"></span></p>
                    <p><span class="font-medium">Départ:</span> <span id="modalDepart"></span></p>
                    <p><span class="font-medium">Arrivée:</span> <span id="modalArrivee"></span></p>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteTransportForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="cancelTransportDelete" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 mr-2">
                        Annuler
                    </button>
                    <button type="button" onclick="deleteTransport()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>




        <!-- Pagination -->
        <div class="mt-6">
            {{ $transports->appends(request()->query())->links() }}
        </div>
    @endif

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

<script>
function delayedDownload(url, id) {
    const spinner = document.getElementById('download-spinner-' + id);
    const text = document.getElementById('download-text-' + id);
    const button = document.getElementById('download-button-' + id);

    // Show spinner and disable button
    if (spinner && text && button) {
        spinner.classList.remove('hidden');
        text.textContent = 'Chargement...';
        button.disabled = true;
        button.classList.add('cursor-not-allowed', 'opacity-70');
    }

    // Wait a bit to show spinner, then trigger the download
    setTimeout(() => {
        const a = document.createElement('a');
        a.href = url;
        a.setAttribute('download', '');
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        // Optional: reset button state after few seconds
        setTimeout(() => {
            if (spinner && text && button) {
                spinner.classList.add('hidden');
                text.textContent = 'Télécharger';
                button.disabled = false;
                button.classList.remove('cursor-not-allowed', 'opacity-70');
            }
        }, 3000); // reset after 3s
    }, 300); // 300ms delay to show spinner
}

// Delete Transport Modal Functions
function showDeleteTransportModal(transportId, chassis, depart, arrivee) {
    // Set the transport details in the modal
    document.getElementById('modalChassis').textContent = chassis;
    document.getElementById('modalDepart').textContent = depart;
    document.getElementById('modalArrivee').textContent = arrivee;
    
    // Set the form action with the transport ID
    document.getElementById('deleteTransportForm').action = `/secretaire/transport/${transportId}`;
    
    // Show the modal
    document.getElementById('deleteTransportModal').classList.remove('hidden');
}

function closeDeleteTransportModal() {
    document.getElementById('deleteTransportModal').classList.add('hidden');
}

async function deleteTransport() {
    const form = document.getElementById('deleteTransportForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-HTTP-Method-Override': 'DELETE'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            alert('Transport supprimé avec succès');
            // Reload the page to see the changes
            location.reload();
        } else {
            alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de la suppression: ' + error.message);
    }
}

// Add event listeners when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Cancel button handler
    document.getElementById('cancelTransportDelete')?.addEventListener('click', closeDeleteTransportModal);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('deleteTransportModal')) {
            closeDeleteTransportModal();
        }
    });
});


    function handleSubmit(form) {
        const btn = form.querySelector('.submit-btn');
        const text = form.querySelector('.btn-text');
        const spinner = form.querySelector('.spinner');
        
        // disable button
        btn.disabled = true;
        btn.classList.add("opacity-75", "cursor-not-allowed");
        
        // swap text with spinner
        text.textContent = "Envoi...";
        spinner.classList.remove("hidden");
    }


</script>




</body>
</html>
