<?php

namespace App\Jobs;

use App\Collector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $collector;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->collector->process();
    }
}
