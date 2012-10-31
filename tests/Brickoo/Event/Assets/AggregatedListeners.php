<?php

    namespace Tests\Brickoo\Event\Assets;

    use Brickoo\Event\Listener;

    class AggregatedListeners implements \Brickoo\Event\Interfaces\ListenerAggregate {

        public function attachListeners(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $EventManager->attach(new Listener('test.event', array($this, 'listenerCallback'), 123));
        }

        public function listenerCallback(\Brickoo\Event\Interfaces\Event $Event, Brickoo\Event\Interfaces\Manager $EventManager) {}

    }