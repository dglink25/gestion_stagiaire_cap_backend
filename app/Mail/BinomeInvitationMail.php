<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class BinomeInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Binome Invitation Mail',
        );
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */

    public function attachments(): array {
        return [];
    }

    public function build(){
        $url = url('/accept-invitation/'.$this->user->invite_token);

        return $this->subject('Invitation binôme')
            ->view('emails.binome_invitation')
            ->with([
                'url' => $url,
                'name' => $this->user->name
            ]);
    }
}
