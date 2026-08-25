<?php

return [
    'regulations' => [
        'COPASST' => [
            'reference' => 'Resolución 2013 de 1986, artículo 2; Decreto Ley 1295 de 1994, artículos 35 y 63',
            'period_years' => 2,
            'ranges' => [
                ['min' => 0, 'max' => 9, 'mode' => 'VIGIA_SST', 'representatives' => 1, 'substitutes' => 0, 'employer_representatives' => 0],
                ['min' => 10, 'max' => 49, 'mode' => 'COPASST', 'representatives' => 1, 'substitutes' => 1],
                ['min' => 50, 'max' => 499, 'mode' => 'COPASST', 'representatives' => 2, 'substitutes' => 2],
                ['min' => 500, 'max' => 999, 'mode' => 'COPASST', 'representatives' => 3, 'substitutes' => 3],
                ['min' => 1000, 'max' => null, 'mode' => 'COPASST', 'representatives' => 4, 'substitutes' => 4],
            ],
        ],
        'CCL' => [
            'reference' => 'Resolución 3461 de 2025, artículos 3 y 5',
            'period_years' => 2,
            // La Resolución usa “menos de 5”, “más de 5 y menos de 20” y “más de 20”.
            // Los límites 5 y 20 se interpretan operacionalmente como 5-19 y 20+.
            'boundary_interpretation' => 'Los valores 5 y 20 se incluyen en el rango superior operativo. Regla centralizada para revisión normativa.',
            'ranges' => [
                ['min' => 0, 'max' => 4, 'mode' => 'CCL', 'representatives' => 1, 'substitutes' => 0],
                ['min' => 5, 'max' => 19, 'mode' => 'CCL', 'representatives' => 1, 'substitutes' => 1],
                ['min' => 20, 'max' => null, 'mode' => 'CCL', 'representatives' => 2, 'substitutes' => 2],
            ],
        ],
    ],
    'operations' => [
        'COPASST' => [
            'meeting_frequency_months' => 1, 'quorum' => 'half_plus_one', 'quarterly_report' => false, 'annual_report' => false,
            'meeting_reference' => 'Resolución 2013 de 1986, artículos 7 y 8',
            'functions' => [
                ['code'=>'COP-01','name'=>'Proponer medidas preventivas','description'=>'Proponer a la administración medidas y actividades que procuren y mantengan la salud en los lugares y ambientes de trabajo.','regulation'=>'Resolución 2013 de 1986','article'=>'11','phva_stage'=>'P'],
                ['code'=>'COP-02','name'=>'Participar en capacitación','description'=>'Proponer y participar en actividades de capacitación en seguridad y salud en el trabajo.','regulation'=>'Resolución 2013 de 1986','article'=>'11','phva_stage'=>'H'],
                ['code'=>'COP-03','name'=>'Vigilar el SG-SST','description'=>'Vigilar el desarrollo de las actividades de medicina, higiene y seguridad industrial y promover su divulgación.','regulation'=>'Resolución 2013 de 1986','article'=>'11','phva_stage'=>'V'],
                ['code'=>'COP-04','name'=>'Realizar inspecciones','description'=>'Inspeccionar periódicamente lugares, equipos y operaciones e informar factores de riesgo y sugerir medidas correctivas.','regulation'=>'Resolución 2013 de 1986','article'=>'11','phva_stage'=>'H'],
                ['code'=>'COP-05','name'=>'Analizar condiciones de trabajo','description'=>'Colaborar en el análisis de las causas relacionadas con accidentes y enfermedades y proponer medidas correctivas.','regulation'=>'Resolución 2013 de 1986','article'=>'11','phva_stage'=>'A'],
                ['code'=>'COP-06','name'=>'Estudiar sugerencias','description'=>'Estudiar y considerar las sugerencias presentadas por los trabajadores en materia de seguridad y salud.','regulation'=>'Resolución 2013 de 1986','article'=>'11','phva_stage'=>'V'],
                ['code'=>'COP-07','name'=>'Mantener archivo','description'=>'Mantener un archivo de actas y actividades del comité a disposición de las autoridades y trabajadores.','regulation'=>'Resolución 2013 de 1986','article'=>'11','phva_stage'=>'V'],
            ],
        ],
        'CCL' => [
            'meeting_frequency_months' => 1, 'quorum' => 'half_plus_one', 'quarterly_report' => true, 'annual_report' => true,
            'meeting_reference' => 'Resolución 3461 de 2025',
            'functions' => [
                ['code'=>'CCL-01','name'=>'Prevención del acoso laboral','description'=>'Desarrollar actuaciones preventivas, orientadoras, conciliadoras y canalizadoras dentro de las competencias normativas del comité.','regulation'=>'Resolución 3461 de 2025','article'=>'Funciones del comité','phva_stage'=>'H'],
                ['code'=>'CCL-02','name'=>'Seguimiento preventivo','description'=>'Realizar seguimiento a las recomendaciones y compromisos preventivos sin divulgar información confidencial.','regulation'=>'Resolución 3461 de 2025','article'=>'Funciones del comité','phva_stage'=>'V'],
                ['code'=>'CCL-03','name'=>'Recomendaciones institucionales','description'=>'Formular recomendaciones para fortalecer medidas preventivas y correctivas de convivencia laboral.','regulation'=>'Resolución 3461 de 2025','article'=>'Funciones del comité','phva_stage'=>'A'],
                ['code'=>'CCL-04','name'=>'Informes de gestión','description'=>'Elaborar informes trimestrales y el informe anual de gestión con información agregada y no confidencial.','regulation'=>'Resolución 3461 de 2025','article'=>'Funciones del comité','phva_stage'=>'V'],
            ],
            'warning' => 'Los casos de presunto acoso sexual no son competencia conciliatoria del Comité de Convivencia Laboral y deben seguir el procedimiento institucional correspondiente.',
        ],
    ],
    'indicators' => ['adequate'=>85,'attention'=>60],
];
