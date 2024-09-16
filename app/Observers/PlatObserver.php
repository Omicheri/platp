<?php

namespace App\Observers;

use App\Models\Plat;

use App\Notifications\SendMail;
use Illuminate\Support\Facades\Auth;

class PlatObserver
{
    /**
     * Handle the Plat "created" event.
     */
    public function created(Plat $plat): void
    {
        if (Auth::check()) {
            $messages["hi"] = "Salut, ton plat a bien été enregistré " . Auth::user()->name;
            $plat->user->notify(new SendMail($messages));
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
        //
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
