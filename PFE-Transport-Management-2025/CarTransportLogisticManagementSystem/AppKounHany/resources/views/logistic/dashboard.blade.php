<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logistic Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    #zoneModal {
        transition: opacity 0.3s ease;
        z-index: 1000;
    }
    #zoneModal button[onclick^="selectZone"] {
        transition: background-color 0.2s;
    }
    #zoneModal button[onclick^="selectZone"]:hover {
        background-color: #f3f4f6;
    }
</style>
    <style>
    select, input {
        background-color: white;
    }
    .filter-section {
        transition: all 0.3s ease;
    }
    .notification-bell {
    position: relative;
    display: inline-block;
    margin-right: 20px;
}

.notification-bell .fa-bell {
    font-size: 1.5rem;
    color: #6c757d;
}

.notification-bell .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 0.7rem;
}

.notification-bell.shake .fa-bell {
    animation: shake 0.5s;
    animation-iteration-count: 2;
    color: #ffc107;
}

@keyframes shake {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    50% { transform: rotate(0eg); }
    75% { transform: rotate(-15deg); }
    100% { transform: rotate(0deg); }
}
</style>

<style>
.toast {
    background: #2d3748;
    color: #fff;
    padding: 10px 14px;
    margin-top: 20px;
    border-radius: 6px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.4s ease;
}
.toast.show {
    opacity: 1;
    transform: translateX(0);
}
</style>

<style>
    th{
        /* transform: rotate(-20deg); */
    }
</style>

</head>



<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <!-- Left: Title -->
    <div class="text-xl font-semibold text-gray-800">
        Dashboard -- {{ Auth::user()->name }}
    </div>

    <!-- Center: Notification Bell -->
    <div class="relative">
        <a href="#" id="notificationBell" class="text-gray-600 hover:text-gray-800 text-lg">
            <i class="fas fa-bell"></i>
            <span id="newTransportsCount" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 hidden">0</span>
        </a>
    </div>
    <div id="toastContainer" class="fixed top-5 right-5 space-y-3 z-50"></div>


    <audio id="notificationSound" src="{{ asset('sounds/notify.mp3') }}" preload="auto"></audio>
    {{-- <audio id="notificationSound" src="{{ Storage::url('sounds/notify.mp3') }}" preload="auto"></audio> --}}

    



    <!-- Right: Actions -->
    <div class="flex items-center space-x-4">
        
        @if ((auth()->check() && auth()->user()->email === 'contactkounhany@gmail.com') || auth()->user()->email === 'logistic@gmail.com')
            <a href="{{ route('logistic.user.management') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm inline-flex items-center">
                <i class="fas fa-users-cog mr-2"></i> Utilisateurs
            </a>

            <a href="{{ route('logistic.user.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm inline-flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Créer Utilisateur
            </a>
        @endif

        <a href="{{ route('logistic.statistics') }}" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm inline-flex items-center">
            <i class="fas fa-chart-bar mr-2"></i> Statistics
        </a>
            
        
            <a href="{{ route('logistic.transport.new') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Crée demande
            </a>

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                Logout
            </button>
        </form>
    </div>
</nav>


<!-- Main Content -->
<main class="max-w-full mx-auto p-1 bg-white rounded shadow-sm">
    {{-- <h2 class="text-2xl font-semibold mb-4 text-gray-700">Welcome, {{ Auth::user()->name }}!</h2> --}}

    <!-- Filters Section -->
<div class="bg-gray-50 p-4 mb-4 rounded shadow">
    <form method="GET" action="{{ route('logistic.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <!-- Date Range Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
            <div class="flex space-x-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded px-2 py-1 text-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded px-2 py-1 text-sm">
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded px-2 py-1 text-sm">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validé</option>
                <option value="refused" {{ request('status') == 'refused' ? 'selected' : '' }}>Refusé</option>
            </select>
        </div>

        <!-- Point de Départ Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Point de Départ</label>
            <select name="pointdepart" class="w-full border rounded px-2 py-1 text-sm">
                <option value="">All</option>
                @foreach($departPoints as $point)
                    <option value="{{ $point }}" {{ request('pointdepart') == $point ? 'selected' : '' }}>{{ $point }}</option>
                @endforeach
            </select>
        </div>

        <!-- Point d'Arrivée Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Point d'Arrivée</label>
            <select name="poinarrive" class="w-full border rounded px-2 py-1 text-sm">
                <option value="">All</option>
                @foreach($arrivalPoints as $point)
                    <option value="{{ $point }}" {{ request('poinarrive') == $point ? 'selected' : '' }}>{{ $point }}</option>
                @endforeach
            </select>
        </div>

        <!-- Chassis Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Châssis</label>
            <input type="text" name="chassis" value="{{ request('chassis') }}" placeholder="Filter by chassis" class="w-full border rounded px-2 py-1 text-sm">
        </div>

        <!-- Prestataire Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prestataire</label>
            <select name="prestataire" class="w-full border rounded px-2 py-1 text-sm">
                <option value="">All</option>
                @foreach($prestataires as $prestataire)
                    <option value="{{ $prestataire }}" {{ request('prestataire') == $prestataire ? 'selected' : '' }}>{{ $prestataire }}</option>
                @endforeach
            </select>
        </div>

        <!-- Disponibilité Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilité</label>
            <select name="disponibilite" class="w-full border rounded px-2 py-1 text-sm">
                <option value="">All</option>
                <option value="disponible" {{ request('disponibilite') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="non disponible" {{ request('disponibilite') == 'non disponible' ? 'selected' : '' }}>Non disponible</option>
            </select>
        </div>

        <!-- État d'Avancement Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">État d'Avancement</label>
            <select name="etatavancement" class="w-full border rounded px-2 py-1 text-sm">
                <option value="">All</option>
                <option value="en cours" {{ request('etatavancement') == 'en cours' ? 'selected' : '' }}>En cours</option>
                <option value="affecte" {{ request('etatavancement') == 'affecte' ? 'selected' : '' }}>Affecté</option>
                <option value="charge" {{ request('etatavancement') == 'charge' ? 'selected' : '' }}>Chargé</option>
                <option value="termine" {{ request('etatavancement') == 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="derangement" {{ request('etatavancement') == 'derangement' ? 'selected' : '' }}>Dérangement</option>
                <option value="annule" {{ request('etatavancement') == 'annule' ? 'selected' : '' }}>Annulé</option>
            </select>
        </div>

        <!-- Filter Buttons -->
        <div class="flex items-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Apply Filters
            </button>
            <a href="{{ route('logistic.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 text-sm">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- CSV -->
<div class="flex items-end space-x-2">
    <form method="POST" action="{{ route('logistic.export') }}" class="flex items-center space-x-2">
        @csrf
        <input type="date" name="export_date_from" id="export_date_from" 
               value="{{ request('date_from') }}" 
               class="border rounded px-2 py-1 text-sm"
               required>
        <input type="date" name="export_date_to" id="export_date_to" 
               value="{{ request('date_to') }}" 
               class="border rounded px-2 py-1 text-sm"
               required>
        <button type="submit" id="exportBtn" 
                class="px-4 py-2 bg-gray-400 text-white rounded text-sm cursor-not-allowed"
                disabled>
            <i class="fas fa-file-csv mr-1"></i> Export CSV
        </button>
    </form>
</div><br>


    @if ($transports->isEmpty())
        <p class="text-gray-500">No transport orders found.</p>
    @else
    
    
        <table class="w-full bg-white shadow-md rounded overflow-hidden table-auto text-xs">
            <thead class="bg-gray-200 text-gray-700">
                <tr class="text-center">
                    
                    <th class="py-3 px-1 text-center">Date</th>
                    <th class="py-3 px-1 text-center">Societe</th>
                    <th class="py-3 px-1 text-center">Site demandent</th>
                    <th class="py-3 px-1 text-center">Châssis</th>
                    <th class="py-3 px-1 text-center">Type</th>
                    <th class="py-3 px-1 text-center">Model</th>
                    <th class="py-3 px-1 text-center">Départ</th>
                    <th class="py-3 px-1 text-center">Arrivée</th>
                    <th class="py-3 px-1 text-center">Coordinateurs</th>
                    <th class="py-3 px-1 text-center">Chef</th>
                    <th class="py-3 px-1 text-center">Responsable</th>
                    <th class="py-3 px-1 text-center">Status</th>
                    <th class="py-3 px-1 text-center">Prestataire</th> 
                    <th class="py-3 px-1 text-center">BL cachet</th>
                    <th class="py-3 px-1 text-center">Disponibilite</th>
                    <th class="py-3 px-1 text-center">Commentaire</th>
                    <th class="py-3 px-1 text-center">Envoyer BL</th>
                    <th class="py-3 px-1 text-center">Etat_Mission</th>
                    <th class="py-3 px-1 text-center">État_Commentaire</th>
                    @if(!Auth::user()->isAssistantLogistic)
                        <th class="py-3 px-1 text-center">Retard</th>
                        <th class="py-3 px-1 text-center">Roullete</th>
                    @endif
                    <th class="py-3 px-1 text-center">Numéro Mission</th>
                    <th class="py-3 px-1 text-center">Description</th>
                    <th class="py-3 px-1 text-center">Fichier</th>
                    <th class="py-3 px-1 text-center">Type_Camion</th>
                    @if(!Auth::user()->isAssistantLogistic)
                        <th class="py-3 px-1 text-center">Prix_Commentaire</th>
                        <th class="py-3 px-1 text-center">Prix Achat</th>
                        <th class="py-3 px-1 text-center">Prix Vente</th>
                        <th class="py-3 px-1 text-center">Zone</th>
                        <th class="py-3 px-1 text-center">kilometrage</th>
                        <th class="py-3 px-1 text-center">Actions</th>
                    @endif
                    
                </tr>
            </thead>
            
            <tbody>
                @foreach ($transports as $transport)
                    <tr class="border-t hover:bg-gray-200 text-center">
                        
                        <td class="py-2 px-1 text-center border border-gray-300">{{ $transport->created_at->timezone('Africa/Casablanca')->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-1 text-center border border-gray-300">{{$transport->societe ?? $transport->societeUser->societe ?? '' }}</td>
                        <td class="py-2 px-1 text-center border border-gray-300">
                            {{ $transport->nameUser->depo ?? $transport->site_demandeur ?? '' }}
                        </td>

                        <td class="py-2 px-1 text-center border border-gray-300">
    @if(str_contains($transport->chassis, ','))
        <button onclick="showChassisModal('{{ $transport->chassis }}')" 
                class="text-green-600 hover:underline">
            <i class="fas fa-eye"></i>
        </button>
        <button 
            onclick="document.getElementById('modal-{{ $transport->id }}').classList.remove('hidden')"
            class="ml-2 px-4 py-2 text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
        >
            <i class="fas fa-pencil-alt"></i>
        </button>
    @else
        {{ $transport->chassis }}<br>
        @if(!Auth::user()->isAssistantLogistic)
            <button 
                onclick="document.getElementById('modal-{{ $transport->id }}').classList.remove('hidden')"
                class="ml-2 px-4 py-2 text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
            >
                <i class="fas fa-pencil-alt"></i>
            </button>
        @endif
    @endif
    
    <!-- Chassis Modification Modal -->
    <div id="modal-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Modifier Châssis</h2>
            <form method="POST" action="{{ route('transports.updateChassis', $transport->id) }}">
                @csrf
                @method('PUT')
                <input 
                    type="text" 
                    name="chassis" 
                    value="{{ $transport->chassis }}" 
                    class="w-full border px-3 py-2 rounded mb-4"
                />
                <div class="flex justify-end space-x-2">
                    <button 
                        type="button" 
                        onclick="document.getElementById('modal-{{ $transport->id }}').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                    >
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</td>
                        <td class="py-2 px-1 text-center border border-gray-300">{{ strtoupper($transport->typevehicule) }}</td> 
                        <td class="py-2 px-1 text-center border border-gray-300">{{ $transport->model }}</td>                        
                        <td class="py-2 px-1 text-center border border-gray-300">{{ $transport->pointdepart }}</td>
                        <td class="py-2 px-1 text-center border border-gray-300">{{ $transport->poinarrive }}</td>
                        <td class="py-2 px-1 text-center border border-gray-300">{{ $transport->nameUser->name ?? '' }}</td>

                        <!-- Chef column with name and status -->
                        <td class="py-2 px-1 text-center border border-gray-300">
                            @if($transport->chefvalid != 3)
                                {{ $transport->chefUser->name ?? '' }}<br>
                                @if ($transport->chefvalid === 1)
                                    <span class="text-green-600 text-sm font-semibold">Validé</span>
                                @elseif ($transport->chefvalid === 2)
                                    <span class="text-red-600 text-sm font-semibold">Refusé</span>
                                @else
                                    <span class="text-gray-500 text-sm">En attente</span>
                                @endif
                            @endif
                        </td>

                        <!-- Responsable column with name and status -->
                        <td class="py-2 px-1 text-center border border-gray-300">
                            {{ $transport->responsableUser->name ?? '' }}<br>
                            @if ($transport->needtobevalid == 0 || $transport->chefvalid === 2)
                                <!-- No status -->
                            @elseif ($transport->responsablevalid === 1)
                                <span class="text-green-600 text-sm font-semibold">Validé</span>
                            @elseif ($transport->responsablevalid === 2)
                                <span class="text-red-600 text-sm font-semibold">Refusé</span>
                            @else
                                <span class="text-gray-500 text-sm">En attente</span>
                            @endif
                        </td>

                        <!-- Final status -->
                        <td class="py-2 px-1 text-center border border-gray-300">
                            @if($transport->chefvalid == 3)
                                <span class="text-green-600 font-semibold">Validé</span>
                                {{-- Leave blank for chefvalid == 3 --}}
                            @elseif ($transport->chefvalid === 2 || $transport->responsablevalid === 2)
                                <span class="text-red-600 font-semibold">Refusé</span>
                            @elseif ($transport->chefvalid === 1 && ($transport->needtobevalid == 0 || $transport->responsablevalid === 1))
                                <span class="text-green-600 font-semibold">Validé</span>
                            @else
                                <span class="text-gray-500">En attente</span>
                            @endif
                        </td>
                        <td class="py-2 px-1 text-center border border-gray-300">
                            {{ $transport->prestataire ?? '' }}<br>

                            @if(!Auth::user()->isAssistantLogistic)
                                <button 
                                    onclick="document.getElementById('prestataire-modal-{{ $transport->id }}').classList.remove('hidden')"
                                    class="ml-2 px-3 py-1 rounded focus:outline-none focus:ring-2 focus:ring-opacity-50 {{ $transport->prestataire ? 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' }}"
                                >
                                    @if($transport->prestataire)
                                        <i class="fas fa-pencil-alt text-white cursor-pointer" title="Modifier"></i>
                                    @else
                                        <i class="fas fa-plus-circle text-white cursor-pointer" title="Ajouter"></i>
                                    @endif
                                </button>
                            @endif

                            <!-- Modal -->
                            <div id="prestataire-modal-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
                                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                                    <h2 class="text-xl font-semibold mb-4">Entrer le nom du prestataire</h2>
                                    <form method="POST" action="{{ route('transports.updatePrestataire', $transport->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input 
                                            type="text" 
                                            name="prestataire" 
                                            value="{{ $transport->prestataire }}" 
                                            placeholder="Nom du prestataire"
                                            class="w-full border px-3 py-2 rounded mb-4"
                                        />
                                        <div class="flex justify-end space-x-2">
                                            <button 
                                                type="button" 
                                                onclick="document.getElementById('prestataire-modal-{{ $transport->id }}').classList.add('hidden')"
                                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                            >
                                                Annuler
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        {{-- BL cachet --}}
                        {{-- Cas 1 : si chefvalid != 3 --}}
                        @if ($transport->chefvalid != 3)
                            <td class="py-3 px-1 border border-gray-300">
                                @if (!empty($transport->BL_cachet))
                                    <a href="{{ route('transports.cachet', $transport->id) }}"
                                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-1 px-3 rounded shadow mb-1 inline-block">
                                        Télécharger
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-red-800">
                                        Pas créé
                                    </span>
                                @endif
                            </td>

                        {{-- Cas 2 : si chefvalid == 3 --}}
                        @else
                            <td class="py-2 px-1 text-center border-r border-gray-200">
                                @if ($transport->BL_cachet)
                                    <!-- Télécharger BL cacheté -->
                                    <a href="{{ route('transports.cachet', $transport->id) }}"
                                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-1 px-3 rounded shadow mb-1 inline-block">
                                        Télécharger
                                    </a>

                                    <!-- Uploader un nouveau BL cachet -->
                                    <button onclick="openUploadModal({{ $transport->id }})"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded shadow">
                                        Uploader&nbsp;Nouveau
                                    </button>
                                @elseif (!empty($transport->prestataire))
                                    <!-- Premier upload -->
                                    <button onclick="openUploadModal({{ $transport->id }})"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded shadow">
                                        Uploader&nbsp;!
                                    </button>
                                @else
                                    <!-- Cas non validé ou pas de prestataire -->
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-red-800">
                                        -
                                    </span>
                                @endif
                            </td>

                        @endif




                        

                        
                        <td class="py-2 px-1 text-center border border-gray-300">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $transport->disponibilite === 'disponible' ? 
                                   ' text-green-800' : ' text-red-800' }}">
                                {{ ucfirst($transport->disponibilite) }}
                            </span>
                        </td>

                        <td class="py-2 px-1 text-center border border-gray-300">
                            @if (!empty($transport->commentaire))
                                <button 
                                    onclick="openCommentModal(`{{ addslashes($transport->commentaire) }}`)" 
                                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-1 px-3 rounded shadow">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @else
                                <button 
                                    disabled 
                                    class="bg-gray-400 text-white text-sm font-medium py-1 px-3 rounded cursor-not-allowed">
                                    Null
                                </button>
                            @endif
                        </td>
                        {{-- envoye le mail --}}
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
                        <td class="py-2 px-1 text-center border border-gray-300">
                            @if ($transport->etatavancement)
                                <div>
                                    <span class="py-2 px-2">{{ $transport->etatavancement }}</span><br>
                                    <button 
                                        class="px-3 py-1 bg-yellow-600 text-white rounded text-sm"
                                        onclick="document.getElementById('update-{{ $transport->id }}').classList.remove('hidden')"
                                    >
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                            @else
                                <button 
                                    class="px-4 py-2 bg-blue-600 text-white rounded"
                                    onclick="document.getElementById('update-{{ $transport->id }}').classList.remove('hidden')"
                                >
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            @endif
                        </td>

                        {{-- commentaire etat --}}
                        <td class="py-2 px-1 text-center border border-gray-300 text-center">
                            @if(empty($transport->etat_commentaire))
                                <button onclick="document.getElementById('add-comment-modal-{{ $transport->id }}').classList.remove('hidden')"
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            @else
                                <button onclick="document.getElementById('view-comment-modal-{{ $transport->id }}').classList.remove('hidden')"
                                        class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm mr-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="document.getElementById('edit-comment-modal-{{ $transport->id }}').classList.remove('hidden')"
                                        class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            @endif
                        </td>

                        @if(!Auth::user()->isAssistantLogistic)
                            {{-- retard --}}
                            <td class="py-2 px-1 text-center  border border-gray-300">
                                @if(empty($transport->retard))
                                    <button onclick="showRetardModal({{ $transport->id }})" 
                                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                @else
                                    {{ $transport->retard }}h
                                    <button onclick="showRetardModal({{ $transport->id }})" 
                                            class="ml-2 px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @endif
                            </td>
                            {{-- roullete --}}
                            <td class="py-2 px-1 text-center border border-gray-300">
                                @if($transport->roulette) {{-- Change to roulette --}}
                                    <span class="roulette-value">{{ $transport->roulette }}</span> {{-- Change to roulette --}}
                                    <button onclick="showEditRoulleteModal({{ $transport->id }}, {{ $transport->roulette }})" {{-- Change to roulette --}}
                                            class="px-3 py-1 bg-yellow-600 text-white hover:text-yellow-800 ml-2 rounded">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @else
                                    <button onclick="showAddRoulleteModal({{ $transport->id }})" 
                                            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                @endif
                            </td>
                        @endif
                        <!-- Numéro Mission column -->
                        <td class="py-2 px-1 text-center border border-gray-300">
                            {{ $transport->numero_mission ?? '' }}
                            <button 
                                onclick="document.getElementById('numero-mission-modal-{{ $transport->id }}').classList.remove('hidden')"
                                class="ml-2 px-3 py-1 rounded focus:outline-none focus:ring-2 focus:ring-opacity-50 {{ $transport->numero_mission ? 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' }}"
                            >
                                @if($transport->numero_mission)
                                    <i class="fas fa-pencil-alt text-white cursor-pointer" title="Modifier"></i>
                                @else
                                    <i class="fas fa-plus-circle text-white cursor-pointer" title="Ajouter"></i>
                                @endif
                            </button>

                            <!-- Numéro Mission Modal -->
                            <div id="numero-mission-modal-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
                                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                                    <h2 class="text-xl font-semibold mb-4">Numéro de Mission</h2>
                                    <form method="POST" action="{{ route('transports.updateNumeroMission', $transport->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input 
                                            type="text" 
                                            name="numero_mission" 
                                            value="{{ $transport->numero_mission }}" 
                                            placeholder="Entrez le numéro de mission"
                                            class="w-full border px-3 py-2 rounded mb-4"
                                        />
                                        <div class="flex justify-end space-x-2">
                                            <button 
                                                type="button" 
                                                onclick="document.getElementById('numero-mission-modal-{{ $transport->id }}').classList.add('hidden')"
                                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                            >
                                                Annuler
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>

                        <!-- Copy Button -->
                        <td class="py-2 px-1 text-center border border-gray-300">
                            {{-- @if(!empty($transport->prestataire) && 
                                (($transport->chefvalid === 1 && ($transport->needtobevalid == 0 || $transport->responsablevalid === 1)) || 
                                $transport->chefvalid == 3)) --}}
                                <button class="copy-info-btn bg-blue-500 hover:bg-blue-600 text-white p-2 rounded" 
                                        onclick="openCopyInfoModal(
                                            '{{ $transport->id }}', 
                                            '{{ $transport->prestataire }}', 
                                            '{{ $transport->chassis }}', 
                                            '{{ $transport->pointdepart }}', 
                                            '{{ $transport->poinarrive }}'
                                        )">
                                    <i class="fas fa-copy"></i>
                                </button>
                            {{-- @endif --}}
                        </td>

                        <!-- File column -->
                        <td class="py-2 px-1 text-center border border-gray-300">
                            <div class="flex justify-center items-center space-x-1">
                                <!-- Upload Button -->
                                <button onclick="document.getElementById('file-upload-modal-{{ $transport->id }}').classList.remove('hidden')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded" title="Uploader fichier">
                                    <i class="fas fa-upload"></i>
                                </button>
                                
                                <!-- Download Button -->
                                @if($transport->file_path)
                                <a href="{{ route('transports.downloadFile', $transport->id) }}" 
                                class="bg-green-500 hover:bg-green-600 text-white p-2 rounded" title="Télécharger fichier">
                                    <i class="fas fa-download"></i>
                                </a>
                                
                                <!-- View Button -->
                                <button onclick="openViewFileModal('{{ $transport->id }}', '{{ basename($transport->file_path) }}')" 
                                        class="bg-purple-500 hover:bg-purple-600 text-white p-2 rounded" title="Voir fichier">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @endif
                            </div>

                            <!-- Debug info (visible only if you add ?debug=1 to URL) -->
                            @if(request()->has('debug') && $transport->file_path)
                            <div class="text-xs text-gray-500 mt-1">
                                Path: {{ $transport->file_path }}<br>
                                URL: {{ Storage::url($transport->file_path) }}<br>
                                Exists: {{ Storage::disk('public')->exists($transport->file_path) ? 'Yes' : 'No' }}
                            </div>
                            @endif

                            <!-- File Upload Modal -->
                            <div id="file-upload-modal-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
                                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                                    <h2 class="text-xl font-semibold mb-4">Uploader un fichier</h2>
                                    <form method="POST" action="{{ route('transports.uploadFile', $transport->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input 
                                            type="file" 
                                            name="file" 
                                            class="w-full border px-3 py-2 rounded mb-4"
                                            required
                                        />
                                        <div class="flex justify-end space-x-2">
                                            <button 
                                                type="button" 
                                                onclick="document.getElementById('file-upload-modal-{{ $transport->id }}').classList.add('hidden')"
                                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                            >
                                                Annuler
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                {{-- TARIFF Part --}}
                        <td class="py-2 px-1 text-center border border-gray-300">
                            {{ str_contains($transport->chassis, ',') ? 'Porte 8' : 'Dépanage' }}
                        </td>

                    @if(!Auth::user()->isAssistantLogistic)
                            {{-- prix commentaire --}}
                            <td class="py-2 px-1 text-center border border-gray-300">
                                @if($transport->prix_commentaire)
                                    <div class="flex items-center justify-center space-x-1">
                                        <button class="text-blue-500 hover:text-blue-700" 
                                                onclick="openViewPrixCommentaireModal('{{ addslashes($transport->prix_commentaire) }}')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button class="text-blue-500 hover:text-blue-700" 
                                                onclick="openPrixCommentaireModal({{ $transport->id }}, {{ json_encode($transport->prix_commentaire) }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                    <button class="text-blue-500 hover:text-blue-700" 
                                            onclick="openPrixCommentaireModal({{ $transport->id }}, '')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                @endif
                            </td>
                    {{-- prix --}}
                            <td class="py-2 px-1 text-center border border-gray-300">
                                <span class="editable-prix" data-id="{{ $transport->id }}" data-field="prixachat">
                                    {{ $transport->prixachat ?? '' }}
                                </span>
                                <button class="text-blue-500 hover:text-blue-700" onclick="editPrix({{ $transport->id }}, 'prixachat', {{ $transport->prixachat ?? 0 }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="py-2 px-1 text-center border border-gray-300">
                                <span class="editable-prix" data-id="{{ $transport->id }}" data-field="prixvente">
                                    {{ $transport->prixvente ?? '' }}
                                </span>
                                <button class="text-blue-500 hover:text-blue-700" onclick="editPrix({{ $transport->id }}, 'prixvente', {{ $transport->prixvente ?? 0 }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="py-2 px-1 text-center border border-gray-300">
                                @if(!empty($transport->zone))
                                    {{ $transport->zone }}<br>
                                @endif
                                <button class="text-blue-500 hover:text-blue-700" onclick="openZoneModal({{ $transport->id }}, '{{ $transport->zone ?? '' }}')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </td>     
                            <td class="py-2 px-1 text-center border border-gray-300">
                                @if(!empty($transport->kilometrage))
                                    {{ number_format($transport->kilometrage, 2) }}<br>
                                @endif
                                <button class="text-blue-500 hover:text-blue-700" onclick="openKilometrageModal({{ $transport->id }}, {{ $transport->kilometrage ?? 'null' }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="py-2 px-1 text-center border border-gray-300">
                                <button onclick="showDeleteTransportModal('{{ $transport->id }}', '{{ $transport->chassis }}', '{{ $transport->pointdepart }}', '{{ $transport->poinarrive }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        @endif

                        

                    </tr>

                    {{-- end of table --}}

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


                    <!-- Modal for updating 'etatavancement' -->
                    <div id="update-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4">Mettre à jour l'état de l'avancement</h2>
                            <form method="POST" action="{{ route('transports.updateEtatavancement', $transport->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label for="etatavancement" class="block text-gray-700 font-semibold mb-2">Choisir l'état d'avancement</label>
                                    <select 
                                        name="etatavancement" 
                                        class="w-full border px-3 py-2 rounded"
                                        required
                                    >
                                           <option value="en cours" {{ $transport->etatavancement == 'en cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="affecte" {{ $transport->etatavancement == 'affecte' ? 'selected' : '' }}>Affecté</option>
                                            <option value="arrive point de depart" {{ $transport->etatavancement == 'arrive point de depart' ? 'selected' : '' }}>Arrivé point de départ</option>
                                            <option value="charge" {{ $transport->etatavancement == 'charge' ? 'selected' : '' }}>Chargé</option>
                                            <option value="arrive a destination" {{ $transport->etatavancement == 'arrive a destination' ? 'selected' : '' }}>Arrivé à destination</option>
                                            <option value="termine" {{ $transport->etatavancement == 'termine' ? 'selected' : '' }}>Terminé</option>
                                            <option value="derangement" {{ $transport->etatavancement == 'derangement' ? 'selected' : '' }}>Dérangement</option>
                                            <option value="annule" {{ $transport->etatavancement == 'annule' ? 'selected' : '' }}>Annulé</option>
                                    </select>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button 
                                        type="button" 
                                        onclick="document.getElementById('update-{{ $transport->id }}').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                    >
                                        Annuler
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                @endforeach
                {{-- Liens de pagination --}}
                <div class="mt-4">
                    {{ $transports->links() }}
                </div>
            </tbody>
        </table>

        <!-- Add these modals at the bottom of your table -->
@foreach ($transports as $transport)
    <!-- Add Comment Modal -->
    <div id="add-comment-modal-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Ajouter Commentaire</h2>
            <form method="POST" action="{{ route('transports.updateEtatComment', $transport->id) }}">
                @csrf
                @method('PUT')
                <textarea name="etat_commentaire" class="w-full border px-3 py-2 rounded mb-4" rows="4"></textarea>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('add-comment-modal-{{ $transport->id }}').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

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

    <!-- Edit Comment Modal -->
    <div id="edit-comment-modal-{{ $transport->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Modifier Commentaire</h2>
            <form method="POST" action="{{ route('transports.updateEtatComment', $transport->id) }}">
                @csrf
                @method('PUT')
                <textarea name="etat_commentaire" class="w-full border px-3 py-2 rounded mb-4" rows="4">{{ $transport->etat_commentaire }}</textarea>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('edit-comment-modal-{{ $transport->id }}').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endforeach
        

    @endif
</main>


<!-- Retard Modal -->
<div id="retardModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded shadow-md max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Sélectionner le retard</h3>
        <form id="retardForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-3 gap-2 mb-4">
                @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $hour)
                    <button type="button" onclick="selectRetard({{ $hour }})"
                            class="px-4 py-2 border rounded hover:bg-gray-100 retard-option">
                        {{ $hour }}h
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="retard" id="selectedRetard">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideModal('retardModal')" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Roullete Modal -->
<div id="roulleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
        <h3 class="text-lg font-semibold mb-4" id="roulleteModalTitle">Ajouter Roullete</h3>
        <input type="hidden" id="transportId">
        <div class="space-y-3">
            <label class="flex items-center space-x-2">
                <input type="radio" name="roulleteValue" value="2" class="h-4 w-4 text-blue-600">
                <span>2</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="radio" name="roulleteValue" value="4" class="h-4 w-4 text-blue-600">
                <span>4</span>
            </label>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
            <button onclick="hideRoulleteModal()" class="px-4 py-2 bg-gray-300 rounded">Annuler</button>
            <button onclick="saveRoullete()" class="px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Zone Selection Modal -->
<div id="zoneModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Sélectionner la Zone</h3>
            <div class="mt-4 space-y-2">
                @foreach(['R50', 'Urbain', 'Interurbain'] as $zoneOption)
                    <button 
                        onclick="selectZone('{{ $zoneOption }}')"
                        class="w-full px-4 py-2 border rounded-md hover:bg-gray-100 focus:bg-blue-100"
                    >
                        {{ $zoneOption }}
                    </button>
                @endforeach
            </div>
            <div class="mt-4 flex justify-center space-x-4">
                <button 
                    onclick="saveZone()" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700"
                >
                    Enregistrer
                </button>
                <button 
                    onclick="closeZoneModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                >
                    Annuler
                </button>
            </div>
            <input type="hidden" id="selectedZone">
            <input type="hidden" id="currentTransportId">
        </div>
    </div>
</div>

<!-- Kilometrage Edit Modal -->
<div id="kilometrageModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Modifier Kilométrage</h3>
            <div class="mt-4">
                <input 
                    type="number" 
                    id="kilometrageInput" 
                    step="0.01" 
                    min="0" 
                    class="w-full px-3 py-2 border rounded-md text-center"
                    placeholder="Entrez le kilométrage"
                >
            </div>
            <div class="mt-4 flex justify-center space-x-4">
                <button 
                    onclick="saveKilometrage()" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700"
                >
                    Enregistrer
                </button>
                <button 
                    onclick="closeKilometrageModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                >
                    Annuler
                </button>
            </div>
            <input type="hidden" id="currentTransportIdKilometrage">
        </div>
    </div>
</div>
<!-- Prix Edit Modal -->
<div id="prixModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="prixModalTitle">Modifier Prix</h3>
            <div class="mt-4">
                <input 
                    type="number" 
                    id="prixInput" 
                    step="0.01" 
                    min="0" 
                    class="w-full px-3 py-2 border rounded-md text-center"
                    placeholder="Entrez le prix"
                >
            </div>
            <div class="mt-4 flex justify-center space-x-4">
                <button 
                    onclick="savePrix()" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700"
                >
                    Enregistrer
                </button>
                <button 
                    onclick="closePrixModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                >
                    Annuler
                </button>
            </div>
            <input type="hidden" id="currentTransportIdPrix">
            <input type="hidden" id="currentPrixField">
        </div>
    </div>
</div>
<!-- Prix Commentaire Modal -->
<div id="prixCommentaireModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Commentaire sur le Prix</h3>
            <div class="mt-4">
                <textarea 
                    id="prixCommentaireInput" 
                    class="w-full px-3 py-2 border rounded-md"
                    rows="4"
                    placeholder="Entrez un commentaire sur le prix..."
                ></textarea>
            </div>
            <div class="mt-4 flex justify-center space-x-4">
                <button 
                    onclick="savePrixCommentaire()" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700"
                >
                    Enregistrer
                </button>
                <button 
                    onclick="closePrixCommentaireModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                >
                    Annuler
                </button>
            </div>
            <input type="hidden" id="currentTransportIdPrixCommentaire">
        </div>
    </div>
</div>

<!-- View Prix Commentaire Modal -->
<div id="viewPrixCommentaireModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Commentaire sur le Prix</h3>
            <div class="mt-4 p-4 bg-gray-50 rounded-md">
                <p id="viewPrixCommentaireContent" class="text-left text-gray-800"></p>
            </div>
            <div class="mt-4">
                <button 
                    onclick="closeViewPrixCommentaireModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                >
                    Fermer
                </button>
            </div>
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
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
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

<!-- Copy Info Modal -->
<div id="copyInfoModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-1/2 lg:w-1/3">
        <div class="p-6">
            <h3 class="text-xl font-semibold mb-4">Copier les informations</h3>
            <div class="bg-gray-100 p-4 rounded mb-4">
                <pre id="copyInfoContent" class="whitespace-pre-wrap"></pre>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeCopyInfoModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">Fermer</button>
                <button id="copyToClipboardBtn" onclick="copyToClipboard()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">
                    Copier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View File Modal -->
<div id="viewFileModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-2/3 max-h-screen">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold" id="viewFileName"></h3>
                <button onclick="closeViewFileModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="border rounded-lg p-4 bg-gray-50 max-h-96 overflow-auto" id="viewFileContent">
                <!-- Content will be inserted by JavaScript -->
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="closeViewFileModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
                    <i class="fas fa-times mr-2"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>



{{-- JS Logic --}}
<script>
    // Global variables
    const modals = {
        kilometrage: document.getElementById('kilometrageModal'),
        zone: document.getElementById('zoneModal'),
        chassis: document.getElementById('chassisModal'),
        comment: document.getElementById('commentModal'),
        roullete: document.getElementById('roulleteModal'),
        retard: document.getElementById('retardModal'),
        prix: document.getElementById('prixModal'),
        prixCommentaire: document.getElementById('prixCommentaireModal'),
        viewPrixCommentaire: document.getElementById('viewPrixCommentaireModal'),
        deleteTransport: document.getElementById('deleteTransportModal'),
        uploadCachet: document.getElementById('uploadCachetModal'),
        copyInfo: document.getElementById('copyInfoModal'),
        viewFile: document.getElementById('viewFileModal'),
    };



    // View File functions
    function openViewFileModal(transportId, fileName) {
        const modalContent = document.getElementById('viewFileContent');
        const fileExtension = fileName.split('.').pop().toLowerCase();
        
        // Clear previous content
        modalContent.innerHTML = '';
        
        // Generate the controller URL
        const fileUrl = `/transports/view-file/${transportId}`;
        
        // Display based on file type
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(fileExtension)) {
            // Image files - use the controller route
            modalContent.innerHTML = `<img src="${fileUrl}" alt="${fileName}" class="max-h-96 mx-auto">`;
        } else if (fileExtension === 'pdf') {
            // PDF files - use the controller route
            modalContent.innerHTML = `
                <iframe src="${fileUrl}" class="w-full h-96" frameborder="0">
                    Votre navigateur ne supporte pas les PDFs. <a href="${fileUrl}" download>Télécharger le PDF</a>
                </iframe>
            `;
        } else {
            // Other file types - show download link using controller
            modalContent.innerHTML = `
                <div class="text-center p-8">
                    <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                    <p class="text-lg font-semibold">${fileName}</p>
                    <p class="text-gray-600 mb-4">Ce type de fichier ne peut pas être prévisualisé</p>
                    <a href="${fileUrl}" download class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </a>
                </div>
            `;
        }
        
        // Set file name
        document.getElementById('viewFileName').textContent = fileName;
        
        // Show modal
        modals.viewFile.classList.remove('hidden');
    }

    function closeViewFileModal() {
        modals.viewFile.classList.add('hidden');
    }


    // Copy Info functions
    function openCopyInfoModal(transportId, prestataire, chassis, pointdepart, pointarrive) {
        // Format the content
        const content = `${prestataire}\n${chassis}\n${pointdepart} --> ${pointarrive}`;
        
        // Set modal content
        document.getElementById('copyInfoContent').textContent = content;
        
        // Store transport ID for potential future use
        currentTransportIds.copyInfo = transportId;
        
        // Show modal
        modals.copyInfo.classList.remove('hidden');
    }

    function closeCopyInfoModal() {
        modals.copyInfo.classList.add('hidden');
    }

    function copyToClipboard() {
        const text = document.getElementById('copyInfoContent').textContent;
        const copyBtn = document.getElementById('copyToClipboardBtn');
        
        navigator.clipboard.writeText(text).then(function() {
            // Show success feedback
            const originalText = copyBtn.textContent;
            copyBtn.textContent = 'Copié!';
            copyBtn.classList.remove('bg-blue-500');
            copyBtn.classList.add('bg-green-500');
            
            setTimeout(function() {
                copyBtn.textContent = originalText;
                copyBtn.classList.remove('bg-green-500');
                copyBtn.classList.add('bg-blue-500');
            }, 2000);
        }).catch(function(err) {
            console.error('Erreur lors de la copie: ', err);
            alert('Erreur lors de la copie');
        });
    }

    // Add to your existing event listeners
    document.addEventListener('click', function(e) {
        // Close modals when clicking outside
        if (e.target.classList.contains('fixed') && e.target.id.includes('Modal')) {
            Object.values(modals).forEach(modal => {
                if (modal && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                }
            });
        }
    });

    let currentTransportIds = {
        kilometrage: null,
        zone: null,
        retard: null,
        prix: null
    };

    function handleFirstDownload(url, id) {
        const btn = document.getElementById(`download-button-${id}`);
        const spinner = document.getElementById(`download-spinner-${id}`);
        const text = document.getElementById(`download-text-${id}`);
        const uploadBtn = document.getElementById(`upload-button-${id}`);

        // Spinner
        text.textContent = 'Téléchargement...';
        spinner.classList.remove('hidden');
        btn.disabled = true;

        // Lance téléchargement
        window.location.href = url;

        // Après 3s, on cache Télécharger et on montre Uploader
        setTimeout(() => {
            btn.classList.add('hidden');
            if (uploadBtn) uploadBtn.classList.remove('hidden');
        }, 3000);
    }



    // Upload Cachet functions
    function openUploadModal(transportId) {
        const form = document.getElementById('uploadCachetForm');
        form.action = `/transports/${transportId}/update-cachet`;
        document.getElementById('uploadCachetModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadCachetModal').classList.add('hidden');
    }



    // View Prix Commentaire functions
    function openViewPrixCommentaireModal(comment) {
        document.getElementById('viewPrixCommentaireContent').textContent = comment;
        document.getElementById('viewPrixCommentaireModal').classList.remove('hidden');
    }

    function closeViewPrixCommentaireModal() {
        document.getElementById('viewPrixCommentaireModal').classList.add('hidden');
    }

    // prix_commentaire
    function openPrixCommentaireModal(transportId, currentComment) {
        document.getElementById('prixCommentaireInput').value = currentComment ?? '';
        document.getElementById('currentTransportIdPrixCommentaire').value = transportId;
        document.getElementById('prixCommentaireModal').classList.remove('hidden');
    }

    function closePrixCommentaireModal() {
        document.getElementById('prixCommentaireModal').classList.add('hidden');
    }

    async function savePrixCommentaire() {
        const comment = document.getElementById('prixCommentaireInput').value;
        const transportId = document.getElementById('currentTransportIdPrixCommentaire').value;

        try {
            const response = await fetch('/transport/update-prix-comment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: transportId,
                    comment: comment
                })
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur de mise à jour: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        } finally {
            closePrixCommentaireModal();
        }
    }

    // Prix functions
    function editPrix(transportId, field, currentValue) {
        currentTransportIds.prix = transportId;
        document.getElementById('prixInput').value = currentValue ?? '';
        document.getElementById('currentTransportIdPrix').value = transportId;
        document.getElementById('currentPrixField').value = field;
        document.getElementById('prixModalTitle').textContent = `Modifier Prix ${field === 'prixachat' ? 'Achat' : 'Vente'}`;
        modals.prix.classList.remove('hidden');
    }

    function closePrixModal() {
        modals.prix.classList.add('hidden');
    }

    async function savePrix() {
        const prixValue = parseFloat(document.getElementById('prixInput').value);
        const transportId = document.getElementById('currentTransportIdPrix').value;
        const field = document.getElementById('currentPrixField').value;

        if (isNaN(prixValue)) {
            alert('Veuillez entrer un nombre valide');
            return;
        }

        try {
            const response = await fetch('/transport/update-prix', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: transportId,
                    field: field,
                    value: prixValue
                })
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur de mise à jour: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        } finally {
            closePrixModal();
        }
    }

    // Kilometrage functions
    function openKilometrageModal(transportId, currentKilometrage) {
        currentTransportIds.kilometrage = transportId;
        document.getElementById('kilometrageInput').value = currentKilometrage ?? '';
        document.getElementById('currentTransportIdKilometrage').value = transportId;
        modals.kilometrage.classList.remove('hidden');
    }

    function closeKilometrageModal() {
        modals.kilometrage.classList.add('hidden');
    }

    async function saveKilometrage() {
        const kilometrageValue = parseFloat(document.getElementById('kilometrageInput').value);
        const transportId = document.getElementById('currentTransportIdKilometrage').value;

        if (isNaN(kilometrageValue)) {
            alert('Veuillez entrer un nombre valide');
            return;
        }

        try {
            const response = await fetch('/transport/update-kilometrage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: transportId,
                    kilometrage: kilometrageValue
                })
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur de mise à jour: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        } finally {
            closeKilometrageModal();
        }
    }

    // Zone functions
    function openZoneModal(transportId, currentZone) {
        currentTransportIds.zone = transportId;
        document.getElementById('selectedZone').value = currentZone ?? '';
        modals.zone.classList.remove('hidden');
    }

    function closeZoneModal() {
        modals.zone.classList.add('hidden');
    }

    function selectZone(zone) {
        document.getElementById('selectedZone').value = zone;
    }

    async function saveZone() {
        const zone = document.getElementById('selectedZone').value;
        const transportId = currentTransportIds.zone;

        if (!zone || !transportId) return;

        try {
            const response = await fetch('/transport/update-zone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: transportId,
                    zone: zone
                })
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur de mise à jour: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        } finally {
            closeZoneModal();
        }
    }

    // Chassis functions
    function showChassisModal(chassisString) {
        const chassisList = document.getElementById('chassisList');
        chassisList.innerHTML = '';
        
        chassisString.split(',').forEach(chassis => {
            const div = document.createElement('div');
            div.className = 'p-2 border-b border-gray-100';
            div.textContent = chassis.trim();
            chassisList.appendChild(div);
        });
        
        modals.chassis.classList.remove('hidden');
    }

    // Comment functions
    function openCommentModal(comment) {
        document.getElementById('commentContent').textContent = comment;
        modals.comment.classList.remove('hidden');
        modals.comment.classList.add('flex');
    }

    function closeCommentModal() {
        modals.comment.classList.remove('flex');
        modals.comment.classList.add('hidden');
    }

    // Roullete functions
    function showAddRoulleteModal(transportId) {
        document.getElementById('transportId').value = transportId;
        document.getElementById('roulleteModalTitle').textContent = 'Ajouter Roullete';
        modals.roullete.classList.remove('hidden');
    }

    function showEditRoulleteModal(transportId, currentValue) {
        document.getElementById('transportId').value = transportId;
        document.getElementById('roulleteModalTitle').textContent = 'Modifier Roullete';
        document.querySelector(`input[name="roulleteValue"][value="${currentValue}"]`).checked = true;
        modals.roullete.classList.remove('hidden');
    }

    function hideRoulleteModal() {
        modals.roullete.classList.add('hidden');
    }

    async function saveRoullete() {
        const transportId = document.getElementById('transportId').value;
        const selectedValue = document.querySelector('input[name="roulleteValue"]:checked')?.value;
        
        if (!selectedValue) {
            alert('Veuillez sélectionner une valeur');
            return;
        }

        try {
            const response = await fetch(`/transport/${transportId}/update-roullete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ roulette: selectedValue })
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la mise à jour');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        }
    }

    // Retard functions
    function showRetardModal(transportId) {
        currentTransportIds.retard = transportId;
        document.getElementById('retardForm').action = `/transports/${transportId}/update-retard`;
        modals.retard.classList.remove('hidden');
    }

    function hideModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function selectRetard(hour) {
        document.getElementById('selectedRetard').value = hour;
        document.getElementById('retardForm').submit();
    }

    // Export functions
    function validateDates() {
        const dateFrom = document.getElementById('export_date_from');
        const dateTo = document.getElementById('export_date_to');
        const exportBtn = document.getElementById('exportBtn');
        
        const fromValid = dateFrom.value !== '';
        const toValid = dateTo.value !== '';
        
        if (fromValid && toValid && new Date(dateTo.value) >= new Date(dateFrom.value)) {
            exportBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            exportBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            exportBtn.disabled = false;
        } else {
            exportBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            exportBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            exportBtn.disabled = true;
        }
    }

    // Notification functions
    function setupNotifications() {
        const bell = document.getElementById('notificationBell');
        const badge = document.getElementById('newTransportsCount');
        const toastContainer = document.getElementById('toastContainer');
        
        let lastChecked = localStorage.getItem('lastTransportCheck') || new Date().toISOString();

        async function checkNewTransports() {
            try {
                const response = await fetch(`/logistics/new-transports?last_checked=${encodeURIComponent(lastChecked)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.new_count > 0) {
                    badge.textContent = data.new_count;
                    badge.style.display = 'inline';
                    bell.classList.add('shake');

                    setTimeout(() => bell.classList.remove('shake'), 1000);

                    // Show detailed toasts
                    data.transports.forEach(t => {
                        if (t.type === 'new') {
                            showToast(`🆕 Nouvelle commande: ${t.chassis || 'N/A'} (${t.pointdepart} → ${t.poinarrive})`);
                        } else {
                            showToast(`✏️ Commande modifiée: ${t.chassis || 'N/A'} (${t.pointdepart} → ${t.poinarrive})`);
                        }
                    });
                }

                lastChecked = data.current_time;
                localStorage.setItem('lastTransportCheck', lastChecked);
            } catch (error) {
                console.error('Error checking new transports:', error);
            }
        }
        
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = message;
            toastContainer.appendChild(toast);

            // Play sound
            const sound = document.getElementById('notificationSound');
            if (sound) {
                sound.currentTime = 0; // reset if already playing
                sound.play().catch(err => console.log("Autoplay blocked:", err));
            }

            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        }

        setInterval(checkNewTransports, 3000);
        checkNewTransports();

        bell.addEventListener('click', function(e) {
            e.preventDefault();
            badge.style.display = 'none';
            localStorage.setItem('lastTransportCheck', new Date().toISOString());
            window.location.reload();
        });
    }


    // Download spinner
    function showDownloadSpinner(button, id) {
        const spinner = document.getElementById(`download-spinner-${id}`);
        const text = document.getElementById(`download-text-${id}`);
        
        if (spinner && text) {
            text.textContent = 'Téléchargement...';
            spinner.classList.remove('hidden');
        }

        button.disabled = true;

        setTimeout(() => {
            if (spinner && text) {
                text.textContent = 'Télécharger';
                spinner.classList.add('hidden');
            }
            button.disabled = false;
        }, 5000);
    }

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Setup date validation for export
        const dateFrom = document.getElementById('export_date_from');
        const dateTo = document.getElementById('export_date_to');
        if (dateFrom && dateTo) {
            dateFrom.addEventListener('change', validateDates);
            dateTo.addEventListener('change', validateDates);
            validateDates(); // Initial validation
        }

        document.getElementById('cancelTransportDelete')?.addEventListener('click', closeDeleteTransportModal);

        // Setup notifications
        setupNotifications();
    });


    // Transport Delete functions
    function showDeleteTransportModal(transportId, chassis, depart, arrivee) {
        document.getElementById('modalChassis').textContent = chassis;
        document.getElementById('modalDepart').textContent = depart;
        document.getElementById('modalArrivee').textContent = arrivee;
        document.getElementById('deleteTransportForm').action = `/logistic/transport/${transportId}`;
        modals.deleteTransport.classList.remove('hidden');
    }

    function closeDeleteTransportModal() {
        modals.deleteTransport.classList.add('hidden');
    }

    async function deleteTransport() {
        const form = document.getElementById('deleteTransportForm');
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-HTTP-Method-Override': 'DELETE'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la suppression');
        } finally {
            closeDeleteTransportModal();
        }
    }
    function hideModal(id) {
        if (id === 'deleteTransportModal') {
            closeDeleteTransportModal();
        } else {
            document.getElementById(id).classList.add('hidden');
        }
    }

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
