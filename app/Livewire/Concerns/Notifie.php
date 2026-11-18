<?php

namespace App\Livewire\Concerns;

trait Notifie
{
    protected function succes(string $message): void
    {
        $this->dispatch('notify', message: $message, type: 'success');
    }

    protected function erreur(string $message): void
    {
        $this->dispatch('notify', message: $message, type: 'error');
    }
}
