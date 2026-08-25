<!doctype html><html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans;font-size:9px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #777;padding:4px}</style></head><body>
<h2>PROGRAMA ANUAL DE CAPACITACIÓN SG-SST</h2>
<p><b>Empresa:</b> {{ $p->company->razon_social }} · <b>NIT:</b> {{ $p->company->nit }}<br><b>Vigencia:</b> {{ $p->year }} · <b>Versión:</b> {{ $p->version }} · <b>Estado:</b> {{ $p->status }}</p>
<h3>Objetivo</h3><p>{{ $p->general_objective }}</p><h3>Alcance</h3><p>{{ $p->scope }}</p>
<p><b>Marco normativo de referencia:</b> {{ config('training.legal_reference') }}</p>
<table><thead><tr><th>Actividad</th><th>Necesidad/origen</th><th>Población</th><th>Responsable</th><th>Período</th><th>Modalidad</th><th>Estado</th></tr></thead><tbody>
@foreach($p->items as $item)
<tr><td>{{ $item->title }}</td><td>@foreach($item->needs as $need){{ $need->title }} ({{ config('training.need_origins.'.$need->origin_type) }})<br>@endforeach</td><td>{{ $item->target_population_description }}</td><td>{{ $item->responsible?->nombre_completo ?? $item->external_responsible }}</td><td>Mes {{ $item->planned_month }} {{ $item->planned_date?->format('d/m/Y') }}</td><td>{{ config('training.modalities.'.$item->planned_modality) }}</td><td>{{ $item->status }}</td></tr>
@endforeach
</tbody></table>
<h3>Revisión y mejora</h3>
@foreach($p->reviews as $review)<p>{{ $review->review_date->format('d/m/Y') }} · COPASST/Vigía: {{ $review->copasst_or_vigia_participation ? 'Sí' : 'No' }} · Alta dirección: {{ $review->senior_management_participation ? 'Sí' : 'No' }}<br>{{ $review->conclusions }}<br><b>Acciones:</b> {{ $review->improvement_actions }}</p>@endforeach
<p>Este documento acredita planeación. No afirma que las actividades hayan sido ejecutadas.</p>
</body></html>
