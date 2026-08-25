<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE documento_cambios MODIFY fecha_vigencia_inicio DATE NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE documento_cambios MODIFY fecha_vigencia_inicio DATE NOT NULL');
    }
};
