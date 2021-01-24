<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contact;
use App\Product;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $contact;
    protected $product;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Contact $contact, Product $product = null)
    {
        $this->contact = $contact;
        if ($product) {
            $this->product = $product;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contact', ['contact' => $this->contact, 'product' => $this->product]);
    }
}

