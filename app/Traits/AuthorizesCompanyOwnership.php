<?php

namespace App\Traits;

trait AuthorizesCompanyOwnership
{
    protected function own($model): void
    {
        abort_unless((int) $model->company_id === (int) auth()->user()->company_id, 404);
    }
}
