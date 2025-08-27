<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .societe-group {
            border-left: 4px solid #3b82f6;
        }
        .secretaire-row {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Confirmer la suppression</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cet utilisateur?</p>
                    <p id="userToDeleteInfo" class="text-sm font-medium mt-2"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 mr-2">
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

    {{-- modifier modal --}}
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Modifier l'utilisateur</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-sm font-medium">Nom</label>
                    <input type="text" name="name" id="editName" class="w-full border rounded p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" id="editEmail" class="w-full border rounded p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Société</label>
                    <input type="text" name="societe" id="editSociete" class="w-full border rounded p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Dépôt</label>
                    <input type="text" name="depo" id="editDepo" class="w-full border rounded p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Mot de passe (laisser vide si inchangé)</label>
                    <input type="password" name="password" id="editPassword" class="w-full border rounded p-2">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelEdit" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="bg-white shadow px-6 py-4 flex items-center space-x-4">
        <a href="{{ route('logistic.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 text-sm">
            <i class="fas fa-home mr-1"></i> Dashboard
        </a>
        <a href="{{ route('logistic.user.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm inline-flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Créer Utilisateur
        </a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                Logout
            </button>
        </form>
        <h1 class="text-xl font-semibold text-gray-800">Utilisateurs</h1>
    </div>

    <div class="container mx-auto px-4 py-8">
        @foreach($users as $societe => $societeUsers)
        <div class="bg-white shadow rounded-lg mb-6 societe-group">
            <h2 class="text-lg font-bold text-gray-800 p-6 pb-2">{{ $societe ?? 'Sans Société' }}</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-4 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                            <th class="px-4 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dépôt</th>
                            <th class="px-4 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Supprimer</th>
                            <th class="px-4 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Modifier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Responsables -->
                        @foreach($societeUsers->where('isResponsable', true) as $user)
                        <tr>
                            <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800">Responsable</span>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->depo ?? '' }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showDeleteModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->societe }}', '{{ $user->depo }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        <!-- 2. Logistics -->
                        @foreach($societeUsers->where('isLogistic', true) as $user)
                        <tr>
                            <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Logistic</span>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->depo ?? '' }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showDeleteModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->societe }}', '{{ $user->depo }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        <!-- 2. Assistant Logistics -->
                        @foreach($societeUsers->where('isAssistantLogistic', true) as $user)
                        <tr>
                            <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-pink-100 text-yellow-800">Assistant Logistic</span>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->depo ?? '' }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showDeleteModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->societe }}', '{{ $user->depo }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        <!-- 3. Chefs and Secretaires -->
                        @php
                            $chefs = $societeUsers->where('isChef', true);
                            $allSecretaires = $societeUsers->where('isSecretaire', true);
                            
                            // Find secretaires that don't have a matching chef (orphaned secretaires)
                            $orphanedSecretaires = $allSecretaires->reject(function ($secretaire) use ($chefs) {
                                return $chefs->contains('tree', $secretaire->tree);
                            });
                            
                            // Find chefs that don't have secretaires
                            $chefsWithoutSecretaires = $chefs->reject(function ($chef) use ($allSecretaires) {
                                return $allSecretaires->contains('tree', $chef->tree);
                            });
                        @endphp

                        <!-- First show orphaned secretaires (those without chefs) -->
                        @foreach($orphanedSecretaires as $secretaire)
                        <tr class="secretaire-row">
                            <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 pl-9">
                                    <i class="fas fa-arrow-right text-gray-400 mr-2"></i>
                                    {{ $secretaire->name }}
                                </div>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">Secrétaire</span>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $secretaire->email }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $secretaire->depo ?? '' }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showDeleteModal('{{ $secretaire->id }}', '{{ $secretaire->name }}', '{{ $secretaire->email }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showEditModal('{{ $secretaire->id }}', '{{ $secretaire->name }}', '{{ $secretaire->email }}', '{{ $secretaire->societe }}', '{{ $secretaire->depo }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        <!-- Then show chefs with their secretaires (or without) -->
                        @foreach($chefs as $chef)
                            <!-- Chef row -->
                            <tr>
                                <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $chef->name }}</div>
                                </td>
                                <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Chef</span>
                                </td>
                                <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $chef->email }}</td>
                                <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $chef->depo ?? '' }}</td>
                                <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                    <button onclick="showDeleteModal('{{ $chef->id }}', '{{ $chef->name }}', '{{ $chef->email }}')" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                                <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                    <button onclick="showEditModal('{{ $chef->id }}', '{{ $chef->name }}', '{{ $chef->email }}', '{{ $chef->societe }}', '{{ $chef->depo }}')" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                        <!-- Secretaires for this chef -->
                        @foreach($societeUsers->where('isSecretaire', true)->where('tree', $chef->tree) as $secretaire)
                        <tr class="secretaire-row">
                            <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 pl-9">
                                    <i class="fas fa-arrow-right text-gray-400 mr-2"></i>
                                    {{ $secretaire->name }}
                                </div>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">Secrétaire</span>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $secretaire->email }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $secretaire->depo ?? '' }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showDeleteModal('{{ $secretaire->id }}', '{{ $secretaire->name }}', '{{ $secretaire->email }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showEditModal('{{ $secretaire->id }}', '{{ $secretaire->name }}', '{{ $secretaire->email }}', '{{ $secretaire->societe }}', '{{ $secretaire->depo }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @endforeach

                        <!-- Finally show chefs without secretaires -->
                        {{-- @foreach($chefsWithoutSecretaires as $chef)
                        <tr>
                            <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $chef->name }}</div>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Chef</span>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $chef->email }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $chef->depo ?? '' }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showDeleteModal('{{ $chef->id }}', '{{ $chef->name }}', '{{ $chef->email }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showEditModal('{{ $chef->id }}', '{{ $chef->name }}', '{{ $chef->email }}', '{{ $chef->societe }}', '{{ $chef->depo }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach --}}

                        <!-- 4. Metteurs au Main -->
                        @foreach($societeUsers->where('isMetteurAuMain', true) as $user)
                        <tr>
                            <td class="px-1 py-2 border border-gray-300 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Metteur au Main</span>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap text-sm text-gray-500">{{ $user->depo ?? '' }}</td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showDeleteModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td class="px-1 py-2 text-center border border-gray-300 whitespace-nowrap">
                                <button onclick="showEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->societe }}', '{{ $user->depo }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    <script>
        function showDeleteModal(userId, userName, userEmail) {
            const modal = document.getElementById('deleteModal');
            const userInfo = document.getElementById('userToDeleteInfo');
            const deleteForm = document.getElementById('deleteForm');
            
            // Set user info in modal
            userInfo.textContent = `${userName} (${userEmail})`;
            

            // Set form action
            deleteForm.action = `/logistic/user/${userId}`;
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Cancel button handler
            document.getElementById('cancelDelete').onclick = function() {
                modal.classList.add('hidden');
            };
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        }


        // modifier
        function showEditModal(userId, userName, userEmail, userSociete, userDepo) {
    const modal = document.getElementById('editModal');
    document.getElementById('editName').value = userName;
    document.getElementById('editEmail').value = userEmail;
    document.getElementById('editSociete').value = userSociete || '';
    document.getElementById('editDepo').value = userDepo || '';
    document.getElementById('editPassword').value = ''; // jamais pré-rempli
    
    const editForm = document.getElementById('editForm');
    editForm.action = `/logistic/user/${userId}`;
    
    modal.classList.remove('hidden');
    
    document.getElementById('cancelEdit').onclick = function() {
        modal.classList.add('hidden');
    };
}

    </script>
</body>
</html>