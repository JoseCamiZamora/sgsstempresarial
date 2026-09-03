<?php

namespace App\Http\Controllers;

use App\Exports\TransportPassengersImportTemplateExport;
use App\Http\Requests\StoreTransportPassengerRequest;
use App\Imports\TransportPassengersImport;
use App\Models\TransportPassenger;
use App\Services\TransportAuditService;
use App\Traits\AuthorizesCompanyOwnership;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransportPassengerController extends Controller
{
    use AuthorizesCompanyOwnership;

    public function index()
    {
        return view('transport.passengers.index', ['passengers' => TransportPassenger::forCompany(auth()->user()->company_id)->latest()->paginate(20)]);
    }

    public function store(StoreTransportPassengerRequest $r, TransportAuditService $audit)
    {
        $p = TransportPassenger::create($r->validated() + ['company_id' => $r->user()->company_id, 'created_by' => $r->user()->id]);
        $audit->record($p->company_id, 'transport_passenger_created', 'passenger', $p->id, $r->user()->id);
        return back()->with('success', 'Pasajero registrado.');
    }

    public function update(StoreTransportPassengerRequest $r, TransportPassenger $passenger, TransportAuditService $audit)
    {
        $this->own($passenger);
        $passenger->update($r->validated() + ['updated_by' => $r->user()->id]);
        $audit->record($passenger->company_id, 'transport_passenger_updated', 'passenger', $passenger->id, $r->user()->id);
        return back()->with('success', 'Pasajero actualizado.');
    }

    public function destroy(TransportPassenger $passenger, TransportAuditService $audit)
    {
        $this->own($passenger);
        $passenger->update(['status' => 'inactive', 'updated_by' => auth()->id()]);
        $audit->record($passenger->company_id, 'transport_passenger_deactivated', 'passenger', $passenger->id, auth()->id());
        return back()->with('success', 'Pasajero desactivado sin eliminar su historial.');
    }

    public function activate(TransportPassenger $passenger, TransportAuditService $audit)
    {
        $this->own($passenger);
        $passenger->update(['status' => 'active', 'updated_by' => auth()->id()]);
        $audit->record($passenger->company_id, 'transport_passenger_activated', 'passenger', $passenger->id, auth()->id());
        return back()->with('success', 'Pasajero activado.');
    }

    public function importTemplate()
    {
        return Excel::download(new TransportPassengersImportTemplateExport, 'plantilla_carga_pasajeros.xlsx');
    }

    public function importMasivo(Request $r, TransportAuditService $audit)
    {
        $r->validate(['archivo_excel' => 'required|file|mimes:xlsx,xls|max:5120']);

        $import = new TransportPassengersImport($r->user()->company_id, $r->user()->id);
        Excel::import($import, $r->file('archivo_excel'));

        $audit->record($r->user()->company_id, 'transport_passengers_imported', 'passenger', 0, $r->user()->id, ['creados' => $import->created, 'omitidos' => $import->skipped]);

        session([
            'transport_passenger_import_resultado' => $import->results,
            'transport_passenger_import_creados' => $import->created,
            'transport_passenger_import_omitidos' => $import->skipped,
        ]);

        return redirect()->route('transport.pasajeros.import.resultado');
    }

    public function importResultado()
    {
        if (!session()->has('transport_passenger_import_resultado')) {
            return redirect()->route('transport.pasajeros.index');
        }

        return view('transport.passengers.import_resultado', [
            'resultados' => session('transport_passenger_import_resultado', []),
            'creados' => session('transport_passenger_import_creados', 0),
            'omitidos' => session('transport_passenger_import_omitidos', 0),
        ]);
    }
}
