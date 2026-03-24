@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-end mb-3" style="margin-right: 321px;">
        <a href="{{ route('home') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
        </a>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h5 class="m-0 font-weight-bold text-primary"><i class="fa fa-building mr-2"></i> Perfil de la Empresa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('perfil.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <label class="font-weight-bold">Logo Corporativo</label>
                                <div class="mt-2">
                                    @if($perfil && $perfil->logo_path)
                                        <img src="{{ asset('storage/' . $perfil->logo_path) }}" class="img-thumbnail" style="max-height: 150px;">
                                    @else
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="fa fa-image fa-3x text-gray-300"></i>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="logo" class="form-control-file mt-3">
                            </div>
                            
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nombre de la Empresa</label>
                                    <input type="text" name="nombre_empresa" class="form-control" value="{{ $perfil->nombre_empresa ?? '' }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>NIT</label>
                                        <input type="text" name="nit" class="form-control" value="{{ $perfil->nit ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Licencia SST</label>
                                        <input type="text" name="licencia_sst" class="form-control" value="{{ $perfil->licencia_sst ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Representante Legal</label>
                                <input type="text" name="representante_legal" class="form-control" value="{{ $perfil->representante_legal ?? '' }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Correo de Contacto</label>
                                <input type="email" name="correo_contacto" class="form-control" value="{{ $perfil->correo_contacto ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dirección Física</label>
                            <input type="text" name="direccion" class="form-control" value="{{ $perfil->direccion ?? '' }}">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-save mr-1"></i> Guardar Configuración
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection