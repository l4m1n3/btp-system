<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>{{ $titre ?? 'Document' }}</title>
<style>
    @page { margin: 22mm 14mm 18mm 14mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #1e293b; }
    header { position: fixed; top: -16mm; left: 0; right: 0; height: 14mm; border-bottom: 2px solid #e8520a; }
    header .nom { font-size: 13px; font-weight: bold; color: #0b2545; }
    header .info { font-size: 8px; color: #64748b; }
    header .doc { position: absolute; right: 0; top: 0; text-align: right; }
    footer { position: fixed; bottom: -12mm; left: 0; right: 0; font-size: 7.5px; color: #94a3b8; text-align: center; }
    footer .page:after { content: counter(page); }
    h1 { font-size: 15px; color: #0b2545; margin: 0 0 2px; }
    h2 { font-size: 11px; color: #0b2545; margin: 16px 0 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; }
    .muted { color: #64748b; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0b2545; color: #fff; font-size: 8px; text-transform: uppercase; text-align: left; padding: 5px 6px; }
    td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
    tr:nth-child(even) td { background: #f8fafc; }
    .r { text-align: right; white-space: nowrap; }
    .b { font-weight: bold; }
    .total td { background: #fff4ed !important; font-weight: bold; border-top: 2px solid #e8520a; }
    .kpi { width: 100%; margin: 10px 0; }
    .kpi td { border: 1px solid #e2e8f0; background: #f8fafc; padding: 6px 8px; width: 25%; }
    .kpi .v { font-size: 12px; font-weight: bold; color: #0b2545; }
    .kpi .l { font-size: 7.5px; text-transform: uppercase; color: #64748b; }
    .rouge { color: #dc2626; } .vert { color: #059669; }
</style>
</head>
<body>
<header>
    <div class="nom">{{ config('btp.entreprise.nom') }}</div>
    <div class="info">{{ config('btp.entreprise.adresse') }}{{ config('btp.entreprise.telephone') ? ' · Tél. '.config('btp.entreprise.telephone') : '' }}{{ config('btp.entreprise.nif') ? ' · NIF '.config('btp.entreprise.nif') : '' }}{{ config('btp.entreprise.rccm') ? ' · RCCM '.config('btp.entreprise.rccm') : '' }}</div>
    <div class="doc"><b>{{ $titre ?? '' }}</b><br><span class="info">Édité le {{ now()->format('d/m/Y à H:i') }}</span></div>
</header>
<footer>{{ config('btp.entreprise.nom') }} — document généré par le système de gestion interne — page <span class="page"></span></footer>
@yield('contenu')
</body>
</html>
