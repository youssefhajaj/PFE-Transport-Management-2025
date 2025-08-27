<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer Utilisateur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <div class="text-xl font-semibold text-gray-800">
            Créer Nouvel Utilisateur
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('logistic.user.management') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 text-sm inline-flex items-center">
                <i class="fas fa-users-cog mr-2"></i> Utilisateurs
            </a>
            <a href="{{ route('logistic.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 text-sm">
                <i class="fas fa-home mr-1"></i> Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                    Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Créer un Nouvel Utilisateur</h2>
            </div>

            <div class="md:flex">
                <!-- Simple Roles -->
                <div class="md:w-1/2 p-6 border-r border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Responsable / Logistic / Metteur / Assistant Logistic</h3>
                    <form action="{{ route('logistic.user.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_type" value="role">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de Rôle *</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="role" value="responsable" required class="form-radio h-4 w-4 text-blue-600">
                                    <label class="ml-2">Responsable</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="role" value="logistic" class="form-radio h-4 w-4 text-blue-600">
                                    <label class="ml-2">Logistic</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="role" value="metteur" class="form-radio h-4 w-4 text-blue-600">
                                    <label class="ml-2">Metteur au Main</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="role" value="assistant" class="form-radio h-4 w-4 text-blue-600">
                                    <label class="ml-2">Assistant Logistic</label>
                                </div>
                            </div>
                        </div>

                        @include('logistic.partials.user-form-fields')

                        <div class="mt-6">
                            <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                                Créer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Chef / Secrétaire -->
                <div class="md:w-1/2 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Chef / Secrétaire</h3>
                    <form action="{{ route('logistic.user.store') }}" method="POST" id="chefSecretaireForm">
                        @csrf
                        <input type="hidden" name="user_type" value="chef_secretaire">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="chef_secretaire_type" value="chef" required class="form-radio h-4 w-4 text-green-600">
                                    <label class="ml-2">Chef</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="chef_secretaire_type" value="secretaire" class="form-radio h-4 w-4 text-green-600">
                                    <label class="ml-2">Secrétaire</label>
                                </div>
                            </div>
                        </div>

                        <div id="chefFields" class="hidden space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Nouveau Chef?</label>
                                    <select name="is_new_chef" id="is_new_chef" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="1">Oui - Générer un nouveau Tree</option>
                                        <option value="0">Non - Associer à un secrétaire existant</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- New Chef Fields -->
                            <div id="newChefFields" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative">
                                        <input type="text" value="{{ $nextTreePrefix }}" disabled
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 uppercase bg-gray-100 cursor-not-allowed">
                                        <input type="hidden" name="tree_prefix" value="{{ $nextTreePrefix }}">
                                    </div>
                                    <div class="relative">
                                        <input type="number" value="{{ $nextTreeNumber }}" disabled
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-100 cursor-not-allowed">
                                        <input type="hidden" name="tree_number" value="{{ $nextTreeNumber }}">
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">Tree généré: <span id="tree_preview" class="font-medium">{{ $nextTreePrefix }}-{{ $nextTreeNumber }}</span></p>
                            </div>
                            
                            <!-- Existing Secretary Fields -->
                            <div id="existingSecretaryFields" class="hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Secrétaire Associé *</label>
                                    <select name="secretaire_id" id="secretaire_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Sélectionnez un secrétaire</option>
                                        @foreach($secretaires as $secretaire)
                                            <option value="{{ $secretaire->id }}" data-societe="{{ $secretaire->societe }}">{{ $secretaire->name }} ({{ $secretaire->tree }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="secretaireFields" class="hidden space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Nouvelle Secrétaire?</label>
                                    <select name="is_new_secretaire" id="is_new_secretaire" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="1">Oui - Générer un nouveau Tree</option>
                                        <option value="0">Non - Associer à un chef existant</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- New Secretary Fields -->
                            <div id="newSecretaireFields" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative">
                                        <input type="text" value="{{ $nextTreePrefix }}" disabled
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 uppercase bg-gray-100 cursor-not-allowed">
                                        <input type="hidden" name="secretaire_tree_prefix" value="{{ $nextTreePrefix }}">
                                    </div>
                                    <div class="relative">
                                        <input type="number" value="{{ $nextTreeNumber }}" disabled
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-100 cursor-not-allowed">
                                        <input type="hidden" name="secretaire_tree_number" value="{{ $nextTreeNumber }}">
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">Tree généré: <span id="secretaire_tree_preview" class="font-medium">{{ $nextTreePrefix }}-{{ $nextTreeNumber }}</span></p>
                            </div>
                            
                            <!-- Existing Chef Fields -->
                            <div id="existingChefFields" class="hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Chef Associé *</label>
                                    <select name="chef_id" id="chef_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Sélectionnez un chef</option>
                                        @foreach($chefs as $chef)
                                            <option value="{{ $chef->id }}" data-societe="{{ $chef->societe }}">{{ $chef->name }} ({{ $chef->tree }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe *</label>
                            <input type="password" name="password" id="password" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- <div class="mb-4">
                            <label for="societe_chef_secretaire" class="block text-sm font-medium text-gray-700">Société</label>
                            <input type="text" name="societe" id="societe_chef_secretaire" value="{{ old('societe') }}" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="depo_chef_secretaire" class="block text-sm font-medium text-gray-700">Dépôt</label>
                            <input type="text" name="depo" id="depo_chef_secretaire" value="{{ old('depo') }}" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div> --}}

                        <!-- Société Field -->
                        <div>
                            <label for="societe" class="block text-sm font-medium text-gray-700 mb-1">Société</label>
                            <input list="societeList" id="societe_chef_secretaire" name="societe" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                placeholder="Sélectionnez ou saisissez">
                            <datalist id="societeList">
                                @foreach($existingSocietes as $soc)
                                    <option value="{{ $soc }}">
                                @endforeach
                            </datalist>
                        </div>

                        <!-- Dépôt Field -->
                        <div>
                            <label for="depo" class="block text-sm font-medium text-gray-700 mb-1">Dépôt</label>
                            <input list="depoList" id="depo" name="depo" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                placeholder="Sélectionnez ou saisissez">
                            <datalist id="depoList">
                                @foreach($existingDepos as $d)
                                    <option value="{{ $d }}">
                                @endforeach
                            </datalist>
                        </div>

                        {{-- @include('logistic.partials.user-form-fields') --}}
                        

                        <div class="mt-6">
                            <button type="submit" class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-md transition duration-200">
                                Créer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle between chef and secretaire forms
            document.querySelectorAll('input[name="chef_secretaire_type"]').forEach(r => {
                r.addEventListener('change', () => {
                    document.getElementById('chefFields').classList.toggle('hidden', r.value !== 'chef');
                    document.getElementById('secretaireFields').classList.toggle('hidden', r.value !== 'secretaire');
                    
                    // Reset societe field when switching types
                    document.getElementById('societe_chef_secretaire').value = '';
                });
            });

            // Toggle between new chef and existing secretary
            document.getElementById('is_new_chef')?.addEventListener('change', (e) => {
                const isNewChef = e.target.value === '1';
                document.getElementById('newChefFields').classList.toggle('hidden', !isNewChef);
                document.getElementById('existingSecretaryFields').classList.toggle('hidden', isNewChef);
                
                // Reset societe field when changing selection
                document.getElementById('societe').value = '';
            });

            // Toggle between new secretaire and existing chef
            document.getElementById('is_new_secretaire')?.addEventListener('change', (e) => {
                const isNewSecretaire = e.target.value === '1';
                document.getElementById('newSecretaireFields').classList.toggle('hidden', !isNewSecretaire);
                document.getElementById('existingChefFields').classList.toggle('hidden', isNewSecretaire);
                
                // Reset societe field when changing selection
                document.getElementById('societe').value = '';
            });

            // Update societe field when selecting an existing secretary
            document.getElementById('secretaire_id')?.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.societe) {
                    document.getElementById('societe_chef_secretaire').value = selectedOption.dataset.societe;
                }
            });

            // Update societe field when selecting an existing chef
            document.getElementById('chef_id')?.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.societe) {
                    document.getElementById('societe_chef_secretaire').value = selectedOption.dataset.societe;
                }
            });

            // Update tree preview for chef
            const updateTree = () => {
                const p = document.querySelector('input[name="tree_prefix"]').value.toUpperCase();
                const n = document.querySelector('input[name="tree_number"]').value;
                document.getElementById('tree_preview').textContent = `${p}-${n}`;
            };

            // Update tree preview for secretaire
            const updateSecretaireTree = () => {
                const p = document.querySelector('input[name="secretaire_tree_prefix"]').value.toUpperCase();
                const n = document.querySelector('input[name="secretaire_tree_number"]').value;
                document.getElementById('secretaire_tree_preview').textContent = `${p}-${n}`;
            };
        });
    </script>
</body>
</html>