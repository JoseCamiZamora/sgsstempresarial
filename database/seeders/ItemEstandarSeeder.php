<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ItemEstandar;

class ItemEstandarSeeder extends Seeder
{
    public function run()
    {
        // Limpiamos la tabla para evitar duplicados
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ItemEstandar::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $estandares = [
            // ==========================================
            // GRUPO 1: LOS 7 ESTÁNDARES BÁSICOS (Aplica a todos)
            // ==========================================
            ['ciclo' => 'Planear', 'numeral' => '1.1.1', 'tipo_plantilla' => 7, 'porcentaje' => 0.5, 'nombre' => 'Asignación de persona que diseña el SG-SST', 'modo_verificacion' => 'Documento de asignación y hoja de vida con soportes.'],
            ['ciclo' => 'Planear', 'numeral' => '1.2.1', 'tipo_plantilla' => 7, 'porcentaje' => 0.5, 'nombre' => 'Afiliación al Sistema de Seguridad Social Integral', 'modo_verificacion' => 'Planilla de pago de aportes (PILA).'],
            ['ciclo' => 'Planear', 'numeral' => '1.2.2', 'tipo_plantilla' => 7, 'porcentaje' => 2.0, 'nombre' => 'Capacitación en SST', 'modo_verificacion' => 'Programa de capacitación y soportes de asistencia.'],
            ['ciclo' => 'Planear', 'numeral' => '2.1.1', 'tipo_plantilla' => 7, 'porcentaje' => 2.0, 'nombre' => 'Plan Anual de Trabajo', 'modo_verificacion' => 'Documento del plan de trabajo anual firmado.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.1', 'tipo_plantilla' => 7, 'porcentaje' => 1.0, 'nombre' => 'Evaluaciones médicas ocupacionales', 'modo_verificacion' => 'Conceptos de aptitud médica de los trabajadores.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.1.1', 'tipo_plantilla' => 7, 'porcentaje' => 15.0, 'nombre' => 'Identificación de peligros, evaluación y valoración de riesgos', 'modo_verificacion' => 'Matriz de identificación de peligros y riesgos.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.2.1', 'tipo_plantilla' => 7, 'porcentaje' => 15.0, 'nombre' => 'Medidas de prevención y control frente a peligros/riesgos identificados', 'modo_verificacion' => 'Evidencias de la ejecución de las medidas de control.'],

            // ==========================================
            // GRUPO 2: LOS 14 ESTÁNDARES ADICIONALES (Para completar los 21)
            // ==========================================
            ['ciclo' => 'Planear', 'numeral' => '1.1.3', 'tipo_plantilla' => 21, 'porcentaje' => 0.5, 'nombre' => 'Asignación de responsabilidades en SST', 'modo_verificacion' => 'Documento de asignación de responsabilidades firmado.'],
            ['ciclo' => 'Planear', 'numeral' => '1.1.4', 'tipo_plantilla' => 21, 'porcentaje' => 0.5, 'nombre' => 'Conformación del COPASST / Vigía', 'modo_verificacion' => 'Actas de conformación y reuniones.'],
            ['ciclo' => 'Planear', 'numeral' => '1.1.5', 'tipo_plantilla' => 21, 'porcentaje' => 0.5, 'nombre' => 'Conformación Comité de Convivencia Laboral', 'modo_verificacion' => 'Actas de conformación y reuniones.'],
            ['ciclo' => 'Planear', 'numeral' => '1.1.6', 'tipo_plantilla' => 21, 'porcentaje' => 0.5, 'nombre' => 'Programa de capacitación anual', 'modo_verificacion' => 'Programa documentado de capacitación.'],
            ['ciclo' => 'Planear', 'numeral' => '1.1.7', 'tipo_plantilla' => 21, 'porcentaje' => 0.5, 'nombre' => 'Inducción y reinducción en SST', 'modo_verificacion' => 'Registros de asistencia a inducción/reinducción.'],
            ['ciclo' => 'Planear', 'numeral' => '1.1.8', 'tipo_plantilla' => 21, 'porcentaje' => 0.5, 'nombre' => 'Curso Virtual de 50 horas de SST', 'modo_verificacion' => 'Certificado del curso virtual de 50 horas del responsable.'],
            ['ciclo' => 'Planear', 'numeral' => '1.2.3', 'tipo_plantilla' => 21, 'porcentaje' => 1.0, 'nombre' => 'Política de Seguridad y Salud en el Trabajo', 'modo_verificacion' => 'Política firmada, fechada y socializada.'],
            ['ciclo' => 'Planear', 'numeral' => '2.8.1', 'tipo_plantilla' => 21, 'porcentaje' => 2.0, 'nombre' => 'Archivo y retención documental del SG-SST', 'modo_verificacion' => 'Sistema de archivo de documentos del SG-SST.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.2', 'tipo_plantilla' => 21, 'porcentaje' => 1.0, 'nombre' => 'Restricciones y recomendaciones médicas', 'modo_verificacion' => 'Soporte de entrega y cumplimiento de recomendaciones.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.3', 'tipo_plantilla' => 21, 'porcentaje' => 1.0, 'nombre' => 'Reporte de accidentes y enfermedades laborales', 'modo_verificacion' => 'Reportes a la ARL y EPS (FURAT/FUREL).'],
            ['ciclo' => 'Hacer',   'numeral' => '3.2.1', 'tipo_plantilla' => 21, 'porcentaje' => 2.0, 'nombre' => 'Investigación de incidentes y accidentes', 'modo_verificacion' => 'Informes de investigación de accidentes.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.2.6', 'tipo_plantilla' => 21, 'porcentaje' => 2.5, 'nombre' => 'Entrega de Elementos de Protección Personal (EPP)', 'modo_verificacion' => 'Registros de entrega de EPP a los trabajadores.'],
            ['ciclo' => 'Hacer',   'numeral' => '5.1.1', 'tipo_plantilla' => 21, 'porcentaje' => 5.0, 'nombre' => 'Plan de prevención y respuesta ante emergencias', 'modo_verificacion' => 'Documento del plan de emergencias actualizado.'],
            ['ciclo' => 'Hacer',   'numeral' => '5.1.2', 'tipo_plantilla' => 21, 'porcentaje' => 5.0, 'nombre' => 'Brigada de prevención y respuesta ante emergencias', 'modo_verificacion' => 'Documento de conformación y capacitación de brigadas.'],

            // ==========================================
            // GRUPO 3: LOS 39 ESTÁNDARES FINALES (Para completar los 60)
            // ==========================================
            ['ciclo' => 'Planear', 'numeral' => '1.1.2', 'tipo_plantilla' => 60, 'porcentaje' => 0.5, 'nombre' => 'Asignación de recursos para el SG-SST', 'modo_verificacion' => 'Presupuesto aprobado para SST.'],
            ['ciclo' => 'Planear', 'numeral' => '2.2.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Objetivos de Seguridad y Salud en el Trabajo', 'modo_verificacion' => 'Objetivos definidos, claros y medibles.'],
            ['ciclo' => 'Planear', 'numeral' => '2.3.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Evaluación inicial del SG-SST', 'modo_verificacion' => 'Documento de la evaluación inicial.'],
            ['ciclo' => 'Planear', 'numeral' => '2.4.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Matriz legal', 'modo_verificacion' => 'Matriz legal actualizada.'],
            ['ciclo' => 'Planear', 'numeral' => '2.5.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Mecanismos de comunicación', 'modo_verificacion' => 'Evidencia de mecanismos de comunicación interna y externa.'],
            ['ciclo' => 'Planear', 'numeral' => '2.6.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Gestión para la adquisición de bienes y servicios', 'modo_verificacion' => 'Procedimiento de compras con criterios de SST.'],
            ['ciclo' => 'Planear', 'numeral' => '2.7.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Gestión de contratistas', 'modo_verificacion' => 'Procedimiento y evaluación de contratistas en SST.'],
            ['ciclo' => 'Planear', 'numeral' => '2.9.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Rendición de cuentas', 'modo_verificacion' => 'Registro de la rendición de cuentas anual.'],
            ['ciclo' => 'Planear', 'numeral' => '2.10.1','tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Matriz de indicadores del SG-SST', 'modo_verificacion' => 'Fichas técnicas de los indicadores.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.4', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Análisis de condiciones de salud', 'modo_verificacion' => 'Diagnóstico de condiciones de salud actualizado.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.5', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Programas de vigilancia epidemiológica', 'modo_verificacion' => 'Documentos de los PVE según riesgos.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.6', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Evaluación de los programas de vigilancia epidemiológica', 'modo_verificacion' => 'Informes de evaluación de los PVE.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.7', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Estilos de vida y entorno saludable', 'modo_verificacion' => 'Programas de fomento de estilos de vida saludables.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.8', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Servicios de higiene', 'modo_verificacion' => 'Evidencia de suministro de agua y baterías sanitarias.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.1.9', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Manejo de residuos', 'modo_verificacion' => 'Evidencia de recolección y disposición de residuos.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.2.2', 'tipo_plantilla' => 60, 'porcentaje' => 2.0, 'nombre' => 'Registro y análisis estadístico de accidentes', 'modo_verificacion' => 'Estadísticas de accidentalidad.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.3.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Medición de severidad de accidentalidad', 'modo_verificacion' => 'Cálculo del indicador de severidad.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.3.2', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Medición de frecuencia de accidentalidad', 'modo_verificacion' => 'Cálculo del indicador de frecuencia.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.3.3', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Medición de mortalidad por accidentes', 'modo_verificacion' => 'Cálculo del indicador de mortalidad.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.3.4', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Medición de prevalencia de enfermedad laboral', 'modo_verificacion' => 'Cálculo de prevalencia.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.3.5', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Medición de incidencia de enfermedad laboral', 'modo_verificacion' => 'Cálculo de incidencia.'],
            ['ciclo' => 'Hacer',   'numeral' => '3.3.6', 'tipo_plantilla' => 60, 'porcentaje' => 1.0, 'nombre' => 'Ausentismo por causa médica', 'modo_verificacion' => 'Registro y seguimiento al ausentismo.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.1.2', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Identificación de peligros con participación', 'modo_verificacion' => 'Evidencia de participación de trabajadores en la matriz.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.1.3', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Identificación de sustancias catalogadas como carcinógenas', 'modo_verificacion' => 'Inventario de sustancias químicas y hojas de seguridad.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.1.4', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Mediciones ambientales', 'modo_verificacion' => 'Estudios de mediciones de higiene industrial.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.2.2', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Inspecciones a instalaciones, maquinaria y equipos', 'modo_verificacion' => 'Formatos de inspecciones de seguridad ejecutadas.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.2.3', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Mantenimiento preventivo y correctivo', 'modo_verificacion' => 'Cronograma y registros de mantenimiento.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.2.4', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Entrega de EPP y capacitación', 'modo_verificacion' => 'Registros de entrega y capacitación en uso de EPP.'],
            ['ciclo' => 'Hacer',   'numeral' => '4.2.5', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Plan de prevención y control (Peligros prioritarios)', 'modo_verificacion' => 'Acciones ejecutadas sobre riesgos críticos.'],
            ['ciclo' => 'Verificar','numeral' => '6.1.1', 'tipo_plantilla' => 60, 'porcentaje' => 1.25,'nombre' => 'Auditoría anual', 'modo_verificacion' => 'Programa y reporte de la auditoría interna anual.'],
            ['ciclo' => 'Verificar','numeral' => '6.1.2', 'tipo_plantilla' => 60, 'porcentaje' => 1.25,'nombre' => 'Revisión por la alta dirección', 'modo_verificacion' => 'Documento de revisión gerencial del SG-SST.'],
            ['ciclo' => 'Verificar','numeral' => '6.1.3', 'tipo_plantilla' => 60, 'porcentaje' => 1.25,'nombre' => 'Investigación de accidentes e incidentes', 'modo_verificacion' => 'Metodología y ejecución de investigaciones.'],
            ['ciclo' => 'Verificar','numeral' => '6.1.4', 'tipo_plantilla' => 60, 'porcentaje' => 1.25,'nombre' => 'Investigación de enfermedades laborales', 'modo_verificacion' => 'Investigaciones de EL diagnosticadas.'],
            ['ciclo' => 'Actuar',  'numeral' => '7.1.1', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Acciones preventivas y correctivas (No conformidades)', 'modo_verificacion' => 'Evidencia de gestión de no conformidades.'],
            ['ciclo' => 'Actuar',  'numeral' => '7.1.2', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Acciones de mejora conforme a revisión de la Alta Dirección', 'modo_verificacion' => 'Planes de acción derivados de la revisión.'],
            ['ciclo' => 'Actuar',  'numeral' => '7.1.3', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Acciones de mejora conforme a investigación de accidentes', 'modo_verificacion' => 'Planes de acción derivados de accidentes.'],
            ['ciclo' => 'Actuar',  'numeral' => '7.1.4', 'tipo_plantilla' => 60, 'porcentaje' => 2.5, 'nombre' => 'Plan de mejoramiento continuo', 'modo_verificacion' => 'Evidencia de la mejora continua del sistema.']
        ];

        foreach ($estandares as $estandar) {
            ItemEstandar::create([
                'ciclo'             => $estandar['ciclo'],
                'numeral'           => $estandar['numeral'],
                'nombre'            => $estandar['nombre'],
                'modo_verificacion' => $estandar['modo_verificacion'],
                'tipo_plantilla'    => $estandar['tipo_plantilla'],
                'porcentaje'        => $estandar['porcentaje'],
                'activo'            => 1,
            ]);
        }
    }
}