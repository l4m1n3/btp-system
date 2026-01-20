<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; margin: 0; padding: 20px; color: #333; background: #fff;}
    .header { display:flex; justify-content:space-between; margin-bottom:25px; border-bottom:3px solid #1a237e; padding-bottom:15px; }
    .company-info { flex:1; }
    .company-name { font-size:22px; font-weight:bold; color:#1a237e; margin:0 0 5px 0; }
    .company-subtitle { font-size:14px; color:#283593; font-weight:600; margin:0 0 8px 0; }
    .company-details { font-size:10px; color:#424242; line-height:1.3; }
    .report-info { text-align:right; border-left:2px solid #f5f5f5; padding-left:15px; }
    .report-title { font-size:18px; font-weight:bold; color:#1a237e; margin:0 0 10px 0; }
    .period { font-size:12px; color:#424242; font-weight:600; background:#f5f5f5; padding:5px 10px; border-radius:4px; display:inline-block; }
    table { width:100%; border-collapse:collapse; margin:20px 0 30px 0; }
    thead { background: linear-gradient(135deg, #1a237e, #283593); color:white; }
    th { padding:10px 8px; text-align:left; font-weight:600; font-size:11px; border:none; }
    td { padding:8px; font-size:10px; border:none; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    .date-cell { width:80px; font-weight:600; color:#1a237e; }
    .chantier-cell { width:120px; font-weight:500; }
    .categorie-badge { display:inline-block; padding:2px 8px; border-radius:12px; font-size:9px; font-weight:600; text-transform:uppercase; }
    .categorie-materiaux { background:#e3f2fd; color:#1565c0; }
    .categorie-main-oeuvre { background:#f3e5f5; color:#7b1fa2; }
    .categorie-transport { background:#e8f5e8; color:#2e7d32; }
    .categorie-equipement { background:#fff3e0; color:#ef6c00; }
    .categorie-divers { background:#f5f5f5; color:#616161; }
    .montant-high { color:#f44336; font-weight:bold; }
    .montant-medium { color:#ff9800; }
    .montant-low { color:#4caf50; }
    .summary { margin-top:30px; padding:20px; background:#f8f9fa; border-radius:8px; border-left:5px solid #ff9800; }
    .summary-title { font-size:14px; font-weight:bold; color:#1a237e; margin:0 0 15px 0; }
    .summary-content { display:flex; justify-content:space-between; align-items:center; }
    .total-amount { font-size:24px; font-weight:bold; color:#1a237e; }
    .total-label { font-size:12px; color:#424242; font-weight:600; }
    .footer { margin-top:40px; padding-top:15px; border-top:2px solid #f5f5f5; font-size:9px; color:#666; text-align:center; }
</style>
</head>
<body>

<div class="header">
    <div class="company-info">
        <h1 class="company-name">BTP PRO</h1>
        <p class="company-subtitle">Gestion de chantiers & Dépenses</p>
        <div class="company-details">
            Quartier Zak 2<br>
            Niamey - Niger<br>
            Tél: +227 89667000<br>
            Email: mahamanelmn8@gmail.com<br>
        </div>
    </div>
    <div class="report-info">
        <h2 class="report-title">Rapport {{ ucfirst($type) }} des Dépenses</h2>
        <div class="period">Période : {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</div>
        <div style="margin-top:10px; font-size:10px; color:#666;">Généré le : {{ now()->format('d/m/Y à H:i') }}</div>
    </div>
</div>

<!-- Tableau des dépenses -->
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Chantier</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Montant (FCFA)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
        @foreach($depenses as $depense)
            @php
                $total += $depense->montant;
                $montantClass = $depense->montant>100000?'montant-high':($depense->montant>50000?'montant-medium':'montant-low');
                $categorieLower = strtolower($depense->categorie);
                $categorieClass = 'categorie-divers';
                if(str_contains($categorieLower,'matériau')) $categorieClass='categorie-materiaux';
                elseif(str_contains($categorieLower,'main') || str_contains($categorieLower,'œuvre')) $categorieClass='categorie-main-oeuvre';
                elseif(str_contains($categorieLower,'transport')) $categorieClass='categorie-transport';
                elseif(str_contains($categorieLower,'équipement')) $categorieClass='categorie-equipement';
            @endphp
            <tr>
                <td class="date-cell">{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</td>
                <td class="chantier-cell">{{ $depense->chantier->nom ?? '-' }}</td>
                <td><span class="categorie-badge {{ $categorieClass }}">{{ $depense->categorie }}</span></td>
                <td>{{ $depense->description }}</td>
                <td class="{{ $montantClass }}" style="text-align:right;">{{ number_format($depense->montant,0,',',' ') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Récapitulatif -->
<div class="summary">
    <h3 class="summary-title">RÉCAPITULATIF FINANCIER</h3>
    <div class="summary-content">
        <div>
            <div class="total-label">TOTAL DES DÉPENSES</div>
            <div class="total-amount">{{ number_format($total,0,',',' ') }} FCFA</div>
        </div>
    </div>
</div>

<!-- Pied de page -->
<div class="footer">
    BTP PRO — Document généré le {{ now()->format('d/m/Y à H:i') }} — Réf: RAPP-{{ strtoupper(\Illuminate\Support\Str::random(6)) }}
</div>

</body>
</html>
