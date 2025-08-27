<div class="space-y-4">
    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom Complet *</label>
        <input type="text" id="name" name="name" required 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
               placeholder="Entrer Nom">
    </div>

    <!-- Email Field -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
        <input type="email" id="email" name="email" required 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
               placeholder="Entrer Email">
    </div>

    <!-- Password Field -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
        <div class="relative">
            <input type="password" id="password" name="password" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                   placeholder="••••••••" minlength="8">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                {{-- <span class="text-gray-500 text-xs">8+ chars</span> --}}
            </div>
        </div>
        <p class="mt-1 text-xs text-gray-500">Le mot de passe doit contenir au moins 8 caractères</p>
    </div>

    <!-- Société Field -->
    <div>
        <label for="societe" class="block text-sm font-medium text-gray-700 mb-1">Société</label>
        <input list="societeList" id="societe" name="societe" 
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
</div>