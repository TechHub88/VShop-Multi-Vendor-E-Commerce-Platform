<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $salesData;
    public $invoiceTemplate;
    public $totalSales;
    public $commission;
    public $gstRate;
    public $gstAmount;
    public $netAmount;
    public $startDate;
    public $endDate;

    /**
     * Create a new message instance.
     */
    public function __construct($seller, $salesData, $invoiceTemplate, $totalSales, $commission, $gstRate, $gstAmount, $netAmount, $startDate, $endDate)
    {
        $this->seller = $seller;
        $this->salesData = $salesData;
        $this->invoiceTemplate = $invoiceTemplate;
        $this->totalSales = $totalSales;
        $this->commission = $commission;
        $this->gstRate = $gstRate;
        $this->gstAmount = $gstAmount;
        $this->netAmount = $netAmount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $prefix = $this->invoiceTemplate['invoicePrefix'] ?? 'INV';
        return new Envelope(
            subject: 'Invoice #' . $prefix . '-' . $this->seller->id . '-' . date('Ymd'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
