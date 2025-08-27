<body style="font-family: Arial, sans-serif; padding: 1px;">

    <!-- Header Section -->
    <div style="margin-bottom: 5px;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h2 style="font-size: 18px; font-weight: bold;">Morocco M-Automotiv Retail</h2>
                </td>
                <td style="text-align: right;">
                    CASABLANCA LE: {{ now()->format('d/m/Y') }}<br>
                    N°: KH-{{ $transport->id }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Title Box -->
    <div style="text-align: center; margin: 20px 0;">
        <div style="border: 1px solid #000; padding: 10px 20px; display: inline-block; font-weight: bold; font-size: 16px; background-color: #f0f0f0;">
            BON DE TRANSFERT INTER-SITES VN/VO
        </div>
    </div>

    <!-- Main Content -->
    <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
        <p style="line-height: 1.6; margin: 0;">
            <span style="font-weight: bold;">Nous, soussignés Morocco Automotiv Retail,</span>
            mandatons par la présente la société 
            <span style="font-weight: bold;">{{$transport->prestataire}}</span>
            pour récupérer le véhicule 
            <span style="font-weight: bold;">VIN : {{ $transport->chassis }}</span>
            actuellement stocké chez 
            <span style="font-weight: bold;">{{ $transport->pointdepart }}</span>
            et le transporter jusqu'à notre  
            <span style="font-weight: bold;">{{ $transport->poinarrive }}</span>.
            @if($transport->chefvalid && $transport->chefUser)
                Validé par <strong>{{ $transport->chefUser->name }}</strong>
                @if($transport->responsablevalid && $transport->responsableUser)
                    et <strong>{{ $transport->responsableUser->name }}</strong>.
                @else
                    .
                @endif
            @endif
        </p>
    </div>

<!-- Equipment Table and Car Diagram Side-by-Side -->
<table width="100%" style="border-collapse: collapse;">
    <tr>
        <!-- Equipment Table (left side) -->
        <td style="vertical-align: top; width: 50%;">
            <table border="1" width="100%" style="border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="border: 1px solid #000; padding: 2px; text-align: left;">Objet</th>
                        <th style="border: 1px solid #000; padding: 2px; width: 40px;">OUI</th>
                        <th style="border: 1px solid #000; padding: 2px; width: 40px;">NON</th>
                        <th style="border: 1px solid #000; padding: 2px; width: 40px;">Nbre</th>
                        <th style="border: 1px solid #000; padding: 2px; width: 60px;">État</th>
                        <th style="border: 1px solid #000; padding: 2px;">Observations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['Phare supplémentaire', 'Rétroviseur', 'Essuie-glace', 'Antenne', 'Porte bagage', 'Enjoliveurs', 'Radio', 'Allume cigare', 'Appui tête', 'Housses', 'Tapis', 'Batterie', 'Roue de secours', 'Cric', 'Manivelle', 'Boîte à outil', 'Attelage', 'Outillage', 'Autres équipements'] as $item)
                    <tr>
                        <td style="border: 1px solid #000; padding: 2px;">{{ $item }}</td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center;"></td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center;"></td>
                        <td style="border: 1px solid #000; padding: 2px; text-align: center;"></td>
                        <td style="border: 1px solid #000; padding: 2px;"></td>
                        <td style="border: 1px solid #000; padding: 2px;"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </td>

        <!-- Car Diagram and Observation (right side, stacked vertically) -->
        <td style="vertical-align: top; width: 50%;">
            <div style="text-align: center; padding-bottom: 10px;">
                <img src="{{ public_path('images/car-diagram.png') }}" style="max-width: 100%; height: auto;">
            </div>
            <div style="border: 1px solid #000; height: 88px; width: 100%; box-sizing: border-box; padding: 10px;">
                <strong> Observation :</strong>
                <br><br>
                <!-- Optional: blank lines or notes -->
            </div>
        </td>
    </tr>
</table>





    <!-- Footer Section with cachet and text -->
    <table width="100%" style="margin-top: 50px;">
        <tr>
            <td style="width: 40%; vertical-align: top;">
                @if (!empty($transport->nameUser->cachet))
                    <img src="{{ public_path(trim($transport->nameUser->cachet)) }}" style="width: 150px; height: auto;">
                @endif
            
            <td style="width: 60%; vertical-align: top;">
                <div style="border: 1px solid #000; padding: 10px; font-size: 14px;">
                    <strong>N.B :</strong> Toutes les avaries doivent être signalées sur place.<br>
                    Aucune réclamation ultérieure ne sera acceptée.
                </div>

            </td>
        </tr>
    </table>


</body>