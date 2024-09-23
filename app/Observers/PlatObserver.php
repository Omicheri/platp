<?php

namespace App\Observers;

use App\Jobs\ProcessMail;
use App\Models\Plat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlatObserver
{
    /**
     * Handle the Plat "created" event.
     */
    public function created(Plat $plat): void
    {
        if (Auth::check()) {


            ProcessMail::dispatchSync($plat);

        }


    }

    /**
     * Handle the Plat "updated" event.
     */
    public function updated(Plat $plat): void
    {
        //
    }

    /**
     * Handle the Plat "deleted" event.
     */
    public function deleted(Plat $plat): void
    {
        //nettoyer les favoris associés à un plat lorsqu’il est supprimé, sans utiliser onDelete('cascade')
        DB::table('favoris')->where('plat_id', $plat->id)->delete();
    }

    /**
     * Handle the Plat "restored" event.
     */
    public function restored(Plat $plat): void
    {
        //
    }

    /**
     * Handle the Plat "force deleted" event.
     */
    public function forceDeleted(Plat $plat): void
    {
        //
    }
}
