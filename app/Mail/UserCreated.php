<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public readonly User $user;
    public readonly string $password;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $from_addr = config('mail.from.address') ?? 'admin@weather_app.com';
        return $this->from($from_addr, config('mail.from.name'))
            ->subject('Your Account Details')
            ->view('emails.user_created')
            ->with([
                'user' => $this->user,
                'password' => $this->password,
            ]);
    }
}
