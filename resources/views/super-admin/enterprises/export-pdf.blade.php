<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Export Entreprises</title>
  <style>
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #333; }
    h2 { margin: 0 0 12px 0; font-size: 18px; }
    .meta { margin-bottom: 12px; font-size: 11px; color: #666; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; }
    th { background: #f3f4f6; text-align: left; font-weight: 600; }
    .text-center { text-align: center; }
    .small { font-size: 11px; color: #666; }
  </style>
</head>
<body>
  <h2>Liste des Entreprises</h2>
  <div class="meta">
    Généré le: {{ now()->format('Y-m-d H:i') }}
  </div>
  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Ville</th>
        <th>Pays</th>
        <th>Plan</th>
        <th>Statut</th>
        <th>Début</th>
        <th>Fin</th>
        <th class="text-center">Actif</th>
      </tr>
    </thead>
    <tbody>
      @forelse($companies as $c)
        <tr>
          <td>{{ $c->name }}</td>
          <td>{{ $c->email }}</td>
          <td>{{ $c->phone }}</td>
          <td>{{ $c->city }}</td>
          <td>{{ $c->country }}</td>
          <td>{{ $c->plan_id }}</td>
          <td>{{ $c->subscription_status ?? 'N/A' }}</td>
          <td>{{ optional($c->subscription_start_date)->format('Y-m-d') }}</td>
          <td>{{ optional($c->subscription_end_date)->format('Y-m-d') }}</td>
          <td class="text-center">{{ $c->is_active ? 'Oui' : 'Non' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center small">Aucune entreprise</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
