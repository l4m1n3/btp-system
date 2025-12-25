<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        /* Variables de couleur */
        :root {
            --primary-color: #1a237e;
            --secondary-color: #283593;
            --accent-color: #ff9800;
            --light-gray: #f5f5f5;
            --dark-gray: #424242;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
        }

        body { 
            font-family: 'DejaVu Sans', 'Arial', sans-serif; 
            font-size: 11px; 
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        /* En-tête */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--primary-color);
        }

        .company-info {
            flex: 1;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0 0 5px 0;
        }

        .company-subtitle {
            font-size: 14px;
            color: var(--secondary-color);
            font-weight: 600;
            margin: 0 0 8px 0;
        }

        .company-details {
            font-size: 10px;
            color: var(--dark-gray);
            line-height: 1.3;
        }

        .report-info {
            text-align: right;
            border-left: 2px solid var(--light-gray);
            padding-left: 15px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0 0 10px 0;
        }

        .period {
            font-size: 12px;
            color: var(--dark-gray);
            font-weight: 600;
            background: var(--light-gray);
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 30px 0;
            page-break-inside: auto;
        }

        thead {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            border: none;
        }

        tbody tr {
            border-bottom: 1px solid #e0e0e0;
            page-break-inside: avoid;
            page-break-after: auto;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f7ff;
        }

        td {
            padding: 8px;
            font-size: 10px;
            border: none;
        }

        /* Styles spécifiques aux colonnes */
        .date-cell {
            width: 80px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .chantier-cell {
            width: 120px;
            font-weight: 500;
        }

        .categorie-cell {
            width: 100px;
        }

        .description-cell {
            min-width: 180px;
            max-width: 250px;
        }

        .montant-cell {
            width: 100px;
            text-align: right;
            font-weight: 600;
            color: var(--dark-gray);
        }

        /* Badges de catégorie */
        .categorie-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .categorie-materiaux {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .categorie-main-oeuvre {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .categorie-transport {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .categorie-equipement {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .categorie-divers {
            background-color: #f5f5f5;
            color: #616161;
        }

        /* Résumé */
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            border-left: 5px solid var(--accent-color);
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0 0 15px 0;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .total-label {
            font-size: 12px;
            color: var(--dark-gray);
            font-weight: 600;
        }

        .summary-stats {
            display: flex;
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 10px 15px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 10px;
            color: var(--dark-gray);
            margin-top: 5px;
        }

        /* Pied de page */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid var(--light-gray);
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        .footer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .generated-date {
            font-style: italic;
        }

        /* Indicateurs visuels */
        .montant-high {
            color: var(--danger-color);
            font-weight: bold;
        }

        .montant-medium {
            color: var(--warning-color);
        }

        .montant-low {
            color: var(--success-color);
        }

        /* Break pour PDF */
        .page-break {
            page-break-before: always;
        }

        /* En-tête de section */
        .section-header {
            background: var(--light-gray);
            padding: 8px 12px;
            margin: 15px 0 10px 0;
            border-left: 4px solid var(--primary-color);
            font-weight: 600;
            color: var(--primary-color);
            font-size: 12px;
        }

        /* Responsive pour PDF */
        @media print {
            body {
                padding: 15px;
            }
            .page-break {
                page-break-before: always;
            }
        }
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
        <h2 class="report-title">Rapport Hebdomadaire des Dépenses</h2>
        <div class="period">
            Période : {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}
        </div>
        <div style="margin-top: 10px; font-size: 10px; color: #666;">
            Généré le : {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="summary-stats" style="margin-bottom: 20px;">
    <div class="stat-item">
        <div class="stat-value">{{ $depenses->count() }}</div>
        <div class="stat-label">Dépenses</div>
    </div>
    <div class="stat-item">
        @php
            $categories = $depenses->groupBy('categorie')->map->count();
            $topCategorie = $categories->sortDesc()->keys()->first() ?? '-';
        @endphp
        <div class="stat-value">{{ $topCategorie }}</div>
        <div class="stat-label">Catégorie principale</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">
            @php
                $chantiers = $depenses->groupBy('chantier_id')->map->count();
                $nbChantiers = $chantiers->count();
            @endphp
            {{ $nbChantiers }}
        </div>
        <div class="stat-label">Chantiers concernés</div>
    </div>
</div>

<!-- Tableau des dépenses -->
<div class="section-header">DÉTAIL DES DÉPENSES</div>

<table>
    <thead>
        <tr>
            <th style="width: 80px;">Date</th>
            <th style="width: 120px;">Chantier</th>
            <th style="width: 100px;">Catégorie</th>
            <th style="min-width: 180px;">Description</th>
            <th style="width: 100px; text-align: right;">Montant (FCFA)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
            $categories = [];
        @endphp
        
        @foreach($depenses as $depense)
            @php
                $total += $depense->montant;
                $categories[$depense->categorie] = ($categories[$depense->categorie] ?? 0) + $depense->montant;
                
                // Déterminer la classe CSS pour le montant
                $montantClass = '';
                if ($depense->montant > 100000) {
                    $montantClass = 'montant-high';
                } elseif ($depense->montant > 50000) {
                    $montantClass = 'montant-medium';
                } else {
                    $montantClass = 'montant-low';
                }
                
                // Déterminer la classe CSS pour la catégorie
                $categorieClass = 'categorie-divers';
                $categorieLower = strtolower($depense->categorie);
                if (str_contains($categorieLower, 'matériau')) {
                    $categorieClass = 'categorie-materiaux';
                } elseif (str_contains($categorieLower, 'main') || str_contains($categorieLower, 'œuvre')) {
                    $categorieClass = 'categorie-main-oeuvre';
                } elseif (str_contains($categorieLower, 'transport')) {
                    $categorieClass = 'categorie-transport';
                } elseif (str_contains($categorieLower, 'équipement')) {
                    $categorieClass = 'categorie-equipement';
                }
            @endphp
            
            <tr>
                <td class="date-cell">
                    {{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}
                </td>
                <td class="chantier-cell">
                    {{ $depense->chantier->nom ?? '-' }}
                    @if($depense->chantier->client ?? false)
                        <br><small style="color: #666;">{{ $depense->chantier->client }}</small>
                    @endif
                </td>
                <td class="categorie-cell">
                    <span class="categorie-badge {{ $categorieClass }}">
                        {{ $depense->categorie }}
                    </span>
                </td>
                <td class="description-cell">
                    {{ $depense->description }}
                    @if($depense->responsable)
                        <br><small style="color: #666;">Responsable: {{ $depense->responsable }}</small>
                    @endif
                </td>
                <td class="montant-cell {{ $montantClass }}">
                    {{ number_format($depense->montant, 0, ',', ' ') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Résumé détaillé -->
<div class="summary">
    <h3 class="summary-title">RÉCAPITULATIF FINANCIER</h3>
    
    <div class="summary-content">
        <div>
            <div class="total-label">TOTAL DES DÉPENSES</div>
            <div class="total-amount">{{ number_format($total, 0, ',', ' ') }} FCFA</div>
        </div>
        
        {{-- <div>
            <div class="total-label">MONTANT MOYEN</div>
            <div class="total-amount" style="font-size: 18px;">
                {{ number_format($depenses->avg('montant'), 0, ',', ' ') }} FCFA
            </div>
        </div> --}}
    </div>
    
    <!-- Répartition par catégorie -->
    @if(!empty($categories))
        <div style="margin-top: 20px;">
            <div style="font-size: 12px; font-weight: 600; color: var(--primary-color); margin-bottom: 10px;">
                RÉPARTITION PAR CATÉGORIE
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($categories as $categorie => $montant)
                    @php
                        $percentage = $total > 0 ? ($montant / $total * 100) : 0;
                        // Déterminer la classe CSS pour la catégorie
                        $categorieClass = 'categorie-divers';
                        $categorieLower = strtolower($categorie);
                        if (str_contains($categorieLower, 'matériau')) {
                            $categorieClass = 'categorie-materiaux';
                        } elseif (str_contains($categorieLower, 'main') || str_contains($categorieLower, 'œuvre')) {
                            $categorieClass = 'categorie-main-oeuvre';
                        } elseif (str_contains($categorieLower, 'transport')) {
                            $categorieClass = 'categorie-transport';
                        } elseif (str_contains($categorieLower, 'équipement')) {
                            $categorieClass = 'categorie-equipement';
                        }
                    @endphp
                    <div style="background: white; padding: 8px 12px; border-radius: 6px; min-width: 150px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="categorie-badge {{ $categorieClass }}" style="font-size: 8px;">
                                {{ $categorie }}
                            </span>
                            <span style="font-weight: 600; color: var(--primary-color);">
                                {{ number_format($montant, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        <div style="margin-top: 5px;">
                            <div style="height: 4px; background: #e0e0e0; border-radius: 2px;">
                                <div style="height: 100%; width: {{ $percentage }}%; background: var(--accent-color); border-radius: 2px;"></div>
                            </div>
                            <div style="font-size: 9px; color: #666; text-align: right; margin-top: 2px;">
                                {{ number_format($percentage, 1) }}%
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Notes -->
<div style="margin-top: 25px; padding: 15px; background: #f9f9f9; border-radius: 6px; border-left: 4px solid var(--accent-color);">
    <div style="font-size: 11px; font-weight: 600; color: var(--primary-color); margin-bottom: 8px;">
        📋 NOTES
    </div>
    <div style="font-size: 10px; color: #666;">
        • Tous les montants sont exprimés en Francs CFA (FCFA)<br>
        • Ce rapport inclut l'ensemble des dépenses validées pendant la période<br>
        • Les justificatifs sont disponibles sur demande<br>
        • Rapport généré automatiquement par le système BTP Pro
    </div>
</div>

<!-- Pied de page -->
<div class="footer">
    <div class="footer-info">
        <div>
            <strong>BTP PRO</strong> - Gestion de chantiers professionnelle
        </div>
        <div class="generated-date">
            Document généré le {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>
    <div style="font-size: 8px; color: #999; margin-top: 5px;">
        Ce document est confidentiel et destiné à un usage interne exclusivement.
        Page 1/1 - Réf: RAPP-{{ strtoupper(Str::random(6)) }}
    </div>
</div>

</body>
</html>