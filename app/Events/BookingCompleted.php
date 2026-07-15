<?php

namespace App\Events;

class BookingCompleted extends BookingStatusEvent
{
    public function broadcastAs(): string
    {
        return 'booking.completed';
    }
}
