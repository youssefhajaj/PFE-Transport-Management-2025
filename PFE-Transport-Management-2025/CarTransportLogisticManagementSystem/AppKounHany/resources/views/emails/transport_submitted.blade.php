<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle Demande de Transport</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
        }
        .email-header {
            background-color: #3490dc;
            color: white;
            padding: 25px;
            text-align: center;
        }
        h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .email-body {
            padding: 25px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .details-table td {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
        }
        .label-cell {
            font-weight: bold;
            width: 40%;
            background-color: #f9f9f9;
        }
        .value-cell {
            color: #000;
        }
        .action-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3490dc;
            color: white !important;
            text-decoration: none;
            font-weight: 600;
            border-radius: 4px;
            margin-top: 25px;
            transition: background-color 0.3s ease;
        }
        .action-button:hover {
            background-color: #2366a8;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Nouvelle Demande de Transport</h2>
        </div>
        
        <div class="email-body">
            <table class="details-table">
                <tr>
                    <td class="label-cell">Point de départ</td>
                    <td class="value-cell">{{ $transport->pointdepart }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Point d'arrivée</td>
                    <td class="value-cell">{{ $transport->poinarrive }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Châssis</td>
                    <td class="value-cell">{{ $transport->chassis }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Créée par</td>
                    <td class="value-cell">{{ $transport->nameUser->name }}</td>
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
