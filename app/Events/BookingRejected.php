<?php

namespace App\Events;

class BookingRejected extends BookingStatusEvent
{
    public function broadcastAs(): string
    {
        return 'booking.rejected';
    }
}
