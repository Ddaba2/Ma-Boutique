<?php

namespace App\Mail;

use App\Models\Boutique;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AlerteStockMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Boutique $boutique,
        public Collection $enRupture,
        public Collection $stockFaible,
    ) {}

    public function build(): self
    {
        return $this->subject("Alerte stock — {$this->boutique->nom}")
            ->view('emails.alerte-stock');
    }
}
