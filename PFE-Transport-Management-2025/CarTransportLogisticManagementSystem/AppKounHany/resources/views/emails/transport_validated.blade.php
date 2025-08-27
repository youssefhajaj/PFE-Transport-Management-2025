<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demande de Transport Validée</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background: white;
        }
        .email-header {
            background-color: #3182ce;
            color: white;
            padding: 24px;
            text-align: center;
        }
        h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .email-body {
            padding: 28px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .details-table td {
            border: 1px solid #000;
            padding: 12px;
            vertical-align: top;
        }
        .label-col {
            font-weight: bold;
            background-color: #f1f5f9;
            width: 35%;
        }
        .validation-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #38a169;
            color: white;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        .action-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3182ce;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 24px;
            transition: background-color 0.3s ease;
        }
        .action-button:hover {
            background-color: #225ea8;
        }
    </style>
</head>
<body>
    <div class="email-card">
        <div class="email-header">
            <h2>Demande de Transport Validée</h2>
        </div>
        
        <div class="email-body">
            <table class="details-table">
                <tr>
                    <td class="label-col">Châssis</td>
                    <td><strong>{{ $transport->chassis }}</strong></td>
                </tr>
                <tr>
                    <td class="label-col">Point de départ</td>
                    <td>{{ $transport->pointdepart }}</td>
                </tr>
                <tr>
                    <td class="label-col">Point d'arrivée</td>
                    <td>{{ $transport->poinarrive }}</td>
                </tr>
                <tr>
                    <td class="label-col">Créée par</td>
                    <td>{{ $transport->nameUser->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Statut</td>
                    <td><span class="validation-badge">Validée</span></td>
                </tr>
                <tr>
                    <td class="label-col">Validée par</td>
                    <td>
                        {{ $transport->chefUser->name }}<br>
                        <small style="color: #4b5563;">{{ $transport->chefUser->email }}</small>
                    </td>
                </tr>
            </table>

            <div style="text-align: center;">
                <a href="https://www.hanyjay.com" class="action-button">
                    Valider via l'application
                </a>
            </div> 
        </div>
    </div>
</body>
</html>
