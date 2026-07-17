<?php

namespace App\Events;

class BookingRescheduled extends BookingStatusEvent
{
    public function broadcastAs(): string
    {
        return 'booking.rescheduled';
    }
}
