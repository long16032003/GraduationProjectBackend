<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $customer;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation, Customer $customer = null)
    {
        $this->reservation = $reservation;
        $this->customer = $customer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: site_setting('contact_email'),
            subject: 'Xác nhận đặt bàn - ' . site_setting('site_name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmation',
            with: [
                'reservation' => $this->reservation,
                'customer' => $this->customer,
                'restaurantName' => site_setting('site_name'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
