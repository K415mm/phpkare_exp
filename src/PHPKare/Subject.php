<?php

namespace PHPKare;

class Subject
{
    private $observers = [];

    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    public function notify($event_info)
    {
        foreach ($this->observers as $observer) {
            $observer->update($event_info);
        }
    }

    public function triggerEvent($event_info)
    {
        // Some event logic
        $this->notify($event_info);
    }
}
