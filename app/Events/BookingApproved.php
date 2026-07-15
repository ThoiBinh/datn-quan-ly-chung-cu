<?php

namespace App\Events;

class BookingApproved extends BookingStatusEvent
{
    public function broadcastAs(): string
    {
        return 'booking.approved';
    }
}
