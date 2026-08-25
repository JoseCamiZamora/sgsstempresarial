<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\CommitteeMeeting;
use App\Models\TrainingSession;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'committee_meeting' => CommitteeMeeting::class,
            'training_session' => TrainingSession::class,
        ]);
        //
        Paginator::useBootstrapFive();
    }
}
