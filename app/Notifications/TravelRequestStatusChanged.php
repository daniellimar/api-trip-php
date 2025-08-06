<?php

namespace App\Notifications;


use App\Models\TravelRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TravelRequestStatusChanged extends Notification
{
    public function __construct(public string $status, public TravelRequest $request)
    {
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Status da Solicitação de Viagem Atualizado')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line("Sua solicitação de viagem para **{$this->request->destination}** foi **{$this->status}**.")
            ->action('Ver Solicitação', url('/solicitacoes/' . $this->request->id))
            ->line('Obrigado por utilizar nosso sistema!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'travel_request_id' => $this->request->id,
            'destination' => $this->request->destination,
            'status' => $this->status,
            'message' => "Sua solicitação de viagem teve o status atualizado para: {$this->status}.",
        ];
    }
}
