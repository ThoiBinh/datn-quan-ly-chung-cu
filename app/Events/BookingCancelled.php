<?php

namespace App\Events;

class BookingCancelled extends BookingStatusEvent
{
    public function broadcastAs(): string
    {
        return 'booking.cancelled';
    }
}
