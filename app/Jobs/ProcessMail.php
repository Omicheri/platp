<?php

namespace App\Jobs;

use App\Models\Plat;
use App\Notifications\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMail implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**     * Create a new job instance.     */
    public function __construct(public Plat $plat)
    {

    }

    /**     * Execute the job.     */
    public function handle(): void
    {   $user = $this->plat->user;

        $user->notify(new SendMail($this->plat));   }}
