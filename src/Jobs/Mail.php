<?php

namespace Exonos\Mailapi\Jobs;

use Exonos\Mailapi\Mailer;
use Exonos\Mailapi\Models\Batch;
use Exonos\Mailapi\Models\Mail as MailModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Throwable;

class Mail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batch;
    public $recipient;
    public $tries = 2;
    public $maxExceptions = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Batch $batch, MailModel $recipient)
    {
        $this->batch = $batch;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            FacadesMail::to($this->recipient->email)->send(new Mailer(
                $this->recipient
            ));
        } catch (Swift_TransportException $e) {
            Log::error($e->getMessage());
            return $this->release(60);
        }
        DB::transaction(function () {
            $this->recipient->status = MailModel::STATUS_SENT;
            $this->recipient->save();
            $this->batch->decrement('pending_mail');
            $this->batch->refresh();
            if ($this->batch->pending_mail === 0) {
                $this->batch->status = Batch::STATUS_COMPLETED;
                $this->batch->save();
            }
        });
    }

    public function failed(Throwable $e)
    {
        Log::error($e->getMessage());
        $this->recipient->status = MailModel::STATUS_FAILED;
        $this->recipient->save();
    }
}