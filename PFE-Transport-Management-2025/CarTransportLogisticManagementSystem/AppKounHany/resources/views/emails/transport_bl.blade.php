<!DOCTYPE html>
<html>
<head>
    <title>Bon de Transfert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #000000;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .message {
            margin-bottom: 20px;
        }
        .mention {
            font-weight: bold;
        }
        .document-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 14px;
        }
        .document-table th, 
        .document-table td {
            border: 1px solid #dddddd;
            padding: 8px 12px;
            text-align: left;
        }
        .document-table th {
            background-color: #f2f2f2;
            font-weight: normal;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
        }
        .attachment {
            margin-top: 30px;
            font-size: 12px;
            color: #666666;
        }
    </style>
</head>
<body>
    
    @php
        $isOmsan = Str::contains($transport->pointdepart, ['OMSAN', 'Omsan']);
        $isTirso = Str::contains($transport->pointdepart, 'Tirso');
    @endphp

    @if($isOmsan)
        <div class="mb-4 p-3 bg-blue-100 text-blue-800 rounded">
            Merci d'informer <strong>OMSAN</strong>
        </div>
    @elseif($isTirso)
        <div class="mb-4 p-3 bg-blue-100 text-blue-800 rounded">
            Merci d'informer <strong>Tirso</strong>
        </div>
    @endif

    <table class="document-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>VIN</th>
                <th>Société</th>
                <th>Destination</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $transport->created_at->format('d/m/Y') }}</td>
                <td>{{ $transport->chassis }}</td>
                <td>{{ $transport->prestataire ?? '' }}</td>
                <td>{{ $transport->poinarrive }}</td>
            </tr>
        </tbody>
    </table>

    {{-- <div class="footer">
        Cdt.
    </div> --}}

    <div class="attachment">
        1 pièce jointe - Analyse effectuée par Gmail 😊<br>
    </div>
</body>
</html>