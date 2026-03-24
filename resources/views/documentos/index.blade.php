@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
            <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">
                <i class="fa fa-folder-open text-primary mr-2"></i> Gestor Documental SST
            </h2>
            <p class="text-muted small mt-1">Biblioteca oficial de políticas, manuales y formatos del sistema.</p>
        </div>
        
        @hasanyrole('Super Admin|Administrador SGSST')
        <a href="{{ route('documentos.create') }}" class="btn shadow-sm" style="background-color: #36b9cc; color: white; font-weight: bold;">
            <i class="fa fa-upload mr-1"></i> Subir Documento
        </a>
        @endhasanyrole
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 text-sm align-middle">
                    <thead style="background-color: #f8f9fc; color: #4e73df;">
                        <tr>
                            <th class="text-center" style="width: 60px;">Tipo</th>
                            <th>Título del Documento</th>
                            <th>Categoría</th>
                            <th>Subido Por</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr>
                            <td class="text-center">
                                @if(in_array(strtolower($doc->extension_archivo), ['pdf']))
                                    <i class="fa fa-file-pdf-o fa-2x text-danger" title="PDF"></i>
                                @elseif(in_array(strtolower($doc->extension_archivo), ['doc', 'docx']))
                                    <i class="fa fa-file-word-o fa-2x text-primary" title="Word"></i>
                                @elseif(in_array(strtolower($doc->extension_archivo), ['xls', 'xlsx']))
                                    <i class="fa fa-file-excel-o fa-2x text-success" title="Excel"></i>
                                @else
                                    <i class="fa fa-file-o fa-2x text-secondary"></i>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $doc->titulo }}</strong>
                                @if($doc->descripcion)
                                    <small class="text-muted">{{ $doc->descripcion }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light border px-2 py-1 text-secondary">
                                    {{ $doc->categoria }}
                                </span>
                            </td>
                            <td class="small">{{ $doc->autor->name ?? 'Usuario Eliminado' }}</td>
                            <td class="text-center small text-muted">
                                {{ $doc->created_at->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ asset('storage/' . $doc->archivo_ruta) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2 mb-1" title="Ver / Descargar">
                                    <i class="fa fa-download"></i> Descargar
                                </a>

                                @hasanyrole('Super Admin|Administrador SGSST')
                                <form action="{{ route('documentos.destroy', $doc->id) }}" method="POST" class="d-inline form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 mb-1" title="Eliminar Documento">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                @endhasanyrole
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa fa-folder-open-o fa-3x mb-3 d-block text-gray-300"></i>
                                La biblioteca de documentos está vacía.<br>
                                @hasanyrole('Super Admin|Administrador SGSST')
                                    Comienza subiendo el primer manual o política oficial.
                                @else
                                    El área de SST aún no ha publicado documentos.
                                @endhasanyrole
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formularios = document.querySelectorAll('.form-eliminar');
        formularios.forEach(formulario => {
            formulario.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar este documento?',
                    text: "Se borrará de la base de datos y el archivo desaparecerá del servidor.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });
        });
    });
</script>
@endsection