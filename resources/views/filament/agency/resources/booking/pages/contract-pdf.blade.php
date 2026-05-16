<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat de Location - #{{ $booking->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #f59e0b; padding-bottom: 20px; }
        .header h1 { color: #f59e0b; margin: 0; }
        .section { margin: 20px 0; }
        .section h2 { background: #f59e0b; color: white; padding: 10px; font-size: 16px; margin-bottom: 15px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item { margin-bottom: 8px; }
        .info-label { font-weight: bold; color: #666; }
        .total { font-size: 18px; font-weight: bold; color: #f59e0b; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
        .signature { display: flex; justify-content: space-between; margin-top: 50px; }
        .signature-box { width: 45%; text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 30px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contrat de Location de Véhicule</h1>
        <p>Contrat N°: {{ $booking->id }}</p>
    </div>

    <div class="section">
        <h2>Informations de l'Agence</h2>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Agence:</span> {{ $agency->name ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Email:</span> {{ $agency->email ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Téléphone:</span> {{ $agency->phone ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Adresse:</span> {{ $agency->address ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Informations du Client</h2>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Nom:</span> {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}</div>
            <div class="info-item"><span class="info-label">Téléphone:</span> {{ $customer->phone ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Permis de conduire:</span> {{ $customer->license_number ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Nationalité:</span> {{ $customer->nationality ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Informations du Véhicule</h2>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Marque:</span> {{ $vehicle->brand ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Modèle:</span> {{ $vehicle->model ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Immatriculation:</span> {{ $vehicle->plate_number ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Année:</span> {{ $vehicle->year ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Carburant:</span> {{ ucfirst($vehicle->fuel_type ?? 'N/A') }}</div>
            <div class="info-item"><span class="info-label">Transmission:</span> {{ ucfirst($vehicle->transmission ?? 'N/A') }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Détails de la Location</h2>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Date de retrait:</span> {{ $booking->pickup_date?->format('d/m/Y') ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Ville de retrait:</span> {{ $booking->pickupCity->name ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Date de retour:</span> {{ $booking->return_date?->format('d/m/Y') ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Ville de retour:</span> {{ $booking->returnCity->name ?? 'N/A' }}</div>
            <div class="info-item"><span class="info-label">Nombre de jours:</span> {{ $booking->total_days ?? 0 }}</div>
            <div class="info-item"><span class="info-label">Prix par jour:</span> {{ number_format($booking->price_per_day ?? 0, 2) }} MAD</div>
        </div>
    </div>

    <div class="section">
        <h2>Conditions Financières</h2>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Sous-total:</span> {{ number_format($booking->subtotal ?? 0, 2) }} MAD</div>
            <div class="info-item"><span class="info-label">Extras:</span> {{ number_format($booking->extras_price ?? 0, 2) }} MAD</div>
            <div class="info-item"><span class="info-label">Caution:</span> {{ number_format($booking->deposit_amount ?? 0, 2) }} MAD</div>
            <div class="info-item"><span class="info-label">Statut de la caution:</span> {{ ucfirst($booking->deposit_status ?? 'N/A') }}</div>
        </div>
        <div style="margin-top: 15px;">
            <span class="info-label">Prix total:</span>
            <span class="total">{{ number_format($booking->total_price ?? 0, 2) }} MAD</span>
        </div>
    </div>

    @if($booking->notes)
    <div class="section">
        <h2>Notes</h2>
        <p>{{ $booking->notes }}</p>
    </div>
    @endif

    <div class="signature">
        <div class="signature-box">
            <p>Signature du client</p>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <p>Signature de l'agence</p>
            <div class="signature-line"></div>
        </div>
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>CarRental.ma - Système de gestion de location de véhicules</p>
    </div>
</body>
</html>