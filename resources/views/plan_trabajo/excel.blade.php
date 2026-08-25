<table>
    <thead>
        <tr>
            <th colspan="16" style="text-align: center; font-size: 16px; font-weight: bold;">
                PLAN DE TRABAJO ANUAL SGSST - {{ $plan->anio }}
            </th>
        </tr>
        <tr>
            <th style="background-color: #4e73df; color: #ffffff;">Fase PHVA</th>
            <th style="background-color: #4e73df; color: #ffffff;">Actividad</th>
            <th style="background-color: #4e73df; color: #ffffff;">Objetivo</th>
            <th style="background-color: #4e73df; color: #ffffff;">Responsable</th>
            <th style="background-color: #4e73df; color: #ffffff;">Ene</th>
            <th style="background-color: #4e73df; color: #ffffff;">Feb</th>
            <th style="background-color: #4e73df; color: #ffffff;">Mar</th>
            <th style="background-color: #4e73df; color: #ffffff;">Abr</th>
            <th style="background-color: #4e73df; color: #ffffff;">May</th>
            <th style="background-color: #4e73df; color: #ffffff;">Jun</th>
            <th style="background-color: #4e73df; color: #ffffff;">Jul</th>
            <th style="background-color: #4e73df; color: #ffffff;">Ago</th>
            <th style="background-color: #4e73df; color: #ffffff;">Sep</th>
            <th style="background-color: #4e73df; color: #ffffff;">Oct</th>
            <th style="background-color: #4e73df; color: #ffffff;">Nov</th>
            <th style="background-color: #4e73df; color: #ffffff;">Dic</th>
            <th style="background-color: #1cc88a; color: #ffffff;">% Cumplimiento</th>
        </tr>
    </thead>
    <tbody>
        @php $fases = ['Planear', 'Hacer', 'Verificar', 'Actuar']; @endphp
        
        @foreach($fases as $fase)
            @foreach($actividades->where('fase_phva', $fase) as $actividad)
                <tr>
                    <td>{{ strtoupper($fase) }}</td>
                    <td>{{ $actividad->actividad }}</td>
                    <td>{{ $actividad->objetivo_especifico }}</td>
                    <td>{{ $actividad->responsable->name }}</td>
                    
                    {{-- Llenamos los meses --}}
                    @for($m = 1; $m <= 12; $m++)
                        @php 
                            $prog = $actividad->cronograma->where('mes', $m)->first();
                            $textoCelda = '';
                            if($prog) {
                                $textoCelda = $prog->ejecutado ? 'E (Ejecutado)' : 'P (Programado)';
                            }
                        @endphp
                        
                        {{-- Pintamos la celda dependiendo del estado --}}
                        <td style="{{ $prog && $prog->ejecutado ? 'background-color: #c3e6cb;' : ($prog ? 'background-color: #b8daff;' : '') }}">
                            {{ $textoCelda }}
                        </td>
                    @endfor

                    {{-- Calculamos el porcentaje igual que en la vista principal --}}
                    @php
                        $totalP = $actividad->cronograma->where('programado', true)->count();
                        $totalE = $actividad->cronograma->where('ejecutado', true)->count();
                        $cumplimiento = ($totalP > 0) ? round(($totalE / $totalP) * 100) : 0;
                    @endphp
                    <td style="font-weight: bold;">{{ $cumplimiento }}%</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>