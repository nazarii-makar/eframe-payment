<?php

namespace EFrame\Payment\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

abstract class Event
{
    use SerializesModels;
}
