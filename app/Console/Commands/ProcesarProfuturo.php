<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcesarProfuturo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:procesar-profuturo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando importación pesada...');
    
        // El archivo debe estar en storage/app/reporte.csv
        Excel::import(new DocentesImport, 'reporte.csv'); 
        
        $this->info('Importación finalizada con éxito.');
    }
}
