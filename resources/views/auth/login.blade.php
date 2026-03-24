@extends('layouts.auth')

@section('content')
<style>
    /* Fondo general de la página (puedes ajustarlo si ya tienes uno en layouts.auth) */
    body, html {
        height: 100%;
        background-color: #f0f4f8; /* Gris muy suave */
        margin: 0;
    }

    /* Contenedor principal que simula la tarjeta grande */
    .login-container {
        width: 100%;
        max-width: 1000px;
        height: 600px;
        background: #fff;
        border-radius: 30px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    /* Lado Izquierdo - Gradiente y Diseño */
    .login-left {
        /* Gradiente azul moderno estilo SST */
        background: linear-gradient(135deg, #4A90E2 0%, #003973 100%);
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: white;
    }

    /* Lado Derecho - Formulario */
    .login-right {
        padding: 40px 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Estilo de los Inputs (Línea inferior como en tu imagen) */
    .custom-input .form-control {
        border: none;
        border-bottom: 2px solid #e0e0e0;
        border-radius: 0;
        padding-left: 0;
        background-color: transparent;
        box-shadow: none;
        transition: border-color 0.3s;
    }

    .custom-input .form-control:focus {
        border-bottom: 2px solid #4A90E2; /* Color de Sinergia SST */
    }

    .custom-input label {
        color: #4A90E2;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0;
    }

    /* Pestañas falsas superiores (Ingresar / Registrarse) */
    .auth-tabs {
        margin-bottom: 30px;
        text-align: center;
    }
    .auth-tabs span {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        margin-right: 20px;
        border-bottom: 3px solid #4A90E2;
        padding-bottom: 5px;
    }
    .auth-tabs span.inactive {
        color: #aaa;
        border-bottom: transparent;
        font-weight: normal;
        cursor: pointer;
    }

    /* Botón */
    .btn-sinergia {
        background-color: #4A90E2;
        border: none;
        color: white;
        padding: 12px;
        border-radius: 8px;
        font-weight: bold;
        letter-spacing: 1px;
        transition: 0.3s;
    }
    .btn-sinergia:hover {
        background-color: #003973;
        transform: translateY(-2px);
    }
</style>

<div class="container-fluid d-flex justify-content-center align-items-center h-100">
    <div class="login-container d-flex flex-wrap flex-md-nowrap">
        
        <div class="col-md-6 login-left d-none d-md-flex">
            <div>
                <h3 class="font-weight-bold mb-0">
                    <i class="fa fa-shield"></i> Sinergia SST
                </h3>
            </div>

            <div class="text-center my-auto">
                <img src="{{ asset('assets/img/synergy.png') }}" alt="Ilustración SST" style="max-width: 60%; filter: drop-shadow(0px 10px 10px rgba(0,0,0,0.3));">
                <h5 class="mt-4 font-weight-bold" style="color: #fff;">Gestión Segura y Confiable</h5>
                <p class="small text-white-50">Protegiendo el capital más importante de su empresa.</p>
            </div>

            <div class="small text-white-50">
                Copyright © {{ date('Y') }} Sinergia SST.<br>Todos los derechos reservados.
            </div>
        </div>

        <div class="col-md-6 login-right bg-white">
            
            <div class="card shadow-sm border-0" style="border-radius: 20px; padding: 30px;">
                
                <div class="auth-tabs">
                    <span>Ingresar</span>
                    <span class="inactive">Registrarse</span>
                </div>

                @if ($errors->count() > 0)
                    <div class="alert alert-danger p-2 small">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="form_login" method="post" autocomplete="off" action="{{ url('/login_externo') }}">
                    @csrf
                    
                    <div class="form-group custom-input mb-4">
                        <label>Correo Electrónico</label>
                        <input autocomplete="off" type="email" class="form-control" id="email" name="email" placeholder="usuario@empresa.com" required autofocus>
                    </div>

                    <div class="form-group custom-input mb-5">
                        <label>Contraseña</label>
                        <input autocomplete="off" type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-sinergia btn-block w-100 shadow-sm">
                        Ingresar
                    </button>

                    <div class="text-center mt-3">
                        <a href="#" class="small" style="color: #ff6b6b; font-weight:600;">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>

            <div class="text-center mt-4">
                <div class="d-flex justify-content-center mb-2">
                    <a href="#" class="text-muted mx-2"><i class="fa fa-linkedin"></i></a>
                    <a href="#" class="text-muted mx-2"><i class="fa fa-instagram"></i></a>
                    <a href="#" class="text-muted mx-2"><i class="fa fa-facebook"></i></a>
                </div>
                <div class="small text-muted">
                    <i class="fa fa-phone"></i> +57 300 000 0000 &nbsp; | &nbsp; <i class="fa fa-envelope"></i> info@sinergiasst.com
                </div>
            </div>

        </div>
    </div>
</div>
@endsection