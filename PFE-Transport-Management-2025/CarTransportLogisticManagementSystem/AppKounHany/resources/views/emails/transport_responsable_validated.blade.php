<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demande Validée par Responsable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #000;
            background-color: #fff;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
        }
        h2 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 20px;
            color: #2c5282;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        td {
            border: 1px solid #000;
            padding: 8px 10px;
        }
        .label {
            font-weight: bold;
            text-align: left;
            width: 40%;
            background-color: #f9f9f9;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c5282;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            border: none;
            margin-top: 25px;
            transition: background-color 0.3s ease;
        }
        .action-button:hover {
            background-color: #1e3f66;
        }
        .validation-section {
            margin-top: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Demande de Transport Validée</h2>

        <table>
            <tr>
                <td class="label">Châssis</td>
                <td>{{ $transport->chassis }}</td>
            </tr>
            <tr>
                <td class="label">Point de départ</td>
                <td>{{ $transport->pointdepart }}</td>
            </tr>
            <tr>
                <td class="label">Point d'arrivée</td>
                <td>{{ $transport->poinarrive }}</td>
            </tr>
            <tr>
                <td class="label">Créée par</td>
                <td>{{ $transport->nameUser->name ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="validation-section">
            <table>
                <tr>
                    <td class="label">Validée par (Chef)</td>
                    <td>
                        {{ $transport->chefUser->name }}<br>
                        <small>{{ $transport->chefUser->email }}</small>
                    </td>
                </tr>
                <tr>
                    <td class="label">Validée par (Responsable)</td>
                    <td>
                        {{ $transport->responsableUser->name }}<br>
                        <small>{{ $transport->responsableUser->email }}</small>
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
