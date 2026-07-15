<?php

namespace App\Events;

class BookingCreated extends BookingStatusEvent
{
    public function broadcastAs(): string
    {
        return 'booking.created';
    }
}
