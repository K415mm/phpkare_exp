<?php

namespace PHPKare;

class ConcreteObserver implements Observer
{
    public function update($event_info)
    {
        // Handle the event
        echo "Event received: " . $event_info;
    }
}
