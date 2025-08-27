<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Demandes de Transport</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Statistiques des Demandes</h1>
            <div class="flex items-center space-x-4">
                <a href="{{ route('logistic.dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                @if ((auth()->check() && auth()->user()->email === 'contactkounhany@gmail.com') || auth()->user()->email === 'logistic@gmail.com')
                    <a href="{{ route('logistic.user.management') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm inline-flex items-center">
                        <i class="fas fa-users-cog mr-2"></i> Utilisateurs
                    </a>
                    <a href="{{ route('logistic.user.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm inline-flex items-center">
                        <i class="fas fa-user-plus mr-2"></i> Créer Utilisateur
                    </a>
                @endif
                <a href="{{ route('logistic.transport.new') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Créer Demande
                </a>
                <a href="{{ route('logistic.statistics') }}" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm inline-flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i> Statistics
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Filtrer par Période</h2>
                <div class="flex space-x-2">
                    <button id="dailyBtn" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 font-bold">Quotidien</button>
                    <button id="monthlyBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 opacity-75">Mensuel</button>
                    <button id="yearlyBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 opacity-75">Annuel</button>
                </div>
            </div>

            <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                <canvas id="statisticsChart"></canvas>
            </div>
        </div>

        <!-- Secretaries Statistics Section -->
            <div class="bg-white rounded-lg shadow-md p-6 w-full lg:w-1/2">
                <h2 class="text-xl font-semibold mb-4">Statistiques des Secrétaires</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Secrétaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aujourd'hui</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ce Mois</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cette Année</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($secretaries as $secretary)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $secretary->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $secretary->today_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $secretary->month_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $secretary->year_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $secretary->total_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- Cartes Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 m-8">
            <!-- Total des Demandes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-truck fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total des Demandes</p>
                        <h3 class="text-2xl font-bold">{{ $yearlyData->sum('count') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Demandes Cette Année -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Demandes Cette Année</p>
                        <h3 class="text-2xl font-bold">{{ $monthlyData->sum('count') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Demandes Ce Mois -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-calendar-day fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Demandes Ce Mois</p>
                        <h3 class="text-2xl font-bold">{{ $dailyData->sum('count') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Préparer les données annuelles
            const yearlyData = {
                labels: @json($yearlyData->pluck('year')),
                datasets: [{
                    label: 'Demandes par Année',
                    data: @json($yearlyData->pluck('count')),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };

            // Préparer les données mensuelles
            const monthlyCounts = @json($monthlyData->mapWithKeys(function ($item) {
                return [$item->month => $item->count];
            })->toArray());
            const monthlyFilled = Array(12).fill(0);
            Object.keys(monthlyCounts).forEach(month => {
                monthlyFilled[month-1] = monthlyCounts[month];
            });

            const monthlyData = {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Demandes par Mois (Cette Année)',
                    data: monthlyFilled,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };

            // Préparer les données quotidiennes
            const dailyCounts = @json($dailyData->mapWithKeys(function ($item) {
                return [$item->day => $item->count];
            })->toArray());
            const daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();
            const dailyFilled = Array(daysInMonth).fill(0);
            Object.keys(dailyCounts).forEach(day => {
                dailyFilled[day-1] = dailyCounts[day];
            });

            const dailyData = {
                labels: Array.from({length: daysInMonth}, (_, i) => i + 1),
                datasets: [{
                    label: 'Demandes par Jour (Ce Mois)',
                    data: dailyFilled,
                    backgroundColor: 'rgba(153, 102, 255, 0.5)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1,
                    tension: 0.4,
                    fill: false
                }]
            };

            // Créer le graphique (line chart par défaut)
            const ctx = document.getElementById('statisticsChart').getContext('2d');
            let chart = new Chart(ctx, {
                type: 'line',
                data: dailyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de Demandes'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Jour'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            function updateActiveButton(activeBtn) {
                document.querySelectorAll('#dailyBtn, #monthlyBtn, #yearlyBtn').forEach(btn => {
                    btn.classList.remove('font-bold');
                    btn.classList.add('opacity-75');
                });
                activeBtn.classList.add('font-bold');
                activeBtn.classList.remove('opacity-75');
            }

            document.getElementById('yearlyBtn').addEventListener('click', function() {
                chart.data = yearlyData;
                chart.config.type = 'bar';
                chart.options.scales.x.title.text = 'Année';
                chart.update();
                updateActiveButton(this);
            });

            document.getElementById('monthlyBtn').addEventListener('click', function() {
                chart.data = monthlyData;
                chart.config.type = 'bar';
                chart.options.scales.x.title.text = 'Mois';
                chart.update();
                updateActiveButton(this);
            });

            document.getElementById('dailyBtn').addEventListener('click', function() {
                chart.data = dailyData;
                chart.config.type = 'line';
                chart.options.scales.x.title.text = 'Jour';
                chart.update();
                updateActiveButton(this);
            });

            // Initialiser avec le bouton Quotidien actif
            updateActiveButton(document.getElementById('dailyBtn'));
        });
    </script>
</body>
</html>

