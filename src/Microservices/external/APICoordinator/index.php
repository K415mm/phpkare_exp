<?php
namespace PHPKare\Microservices\external;

class APICoordinator {
    private $observers = [];

    public function addObserver($observer) {
        $this->observers[] = $observer;
    }

    public function notifyObservers($data) {
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }
}
