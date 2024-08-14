<?php

namespace App\EventSubscriber;

use App\Repository\ConferenceRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor function to inject dependencies
     *
     * @param Environment $twig Twig environment instance
     * @param ConferenceRepository $conferenceRepository Conference repository instance
     */
    public function __construct(
        private Environment $twig,
        private ConferenceRepository $conferenceRepository
    ) {}

    /**
     * Event listener for ControllerEvent
     * Adds all conferences to the Twig global variable 'conferences'
     *
     * @param ControllerEvent $event The controller event object
     * 
     * @return void
     */
    public function onControllerEvent(ControllerEvent $event): void
    {
        $this->twig->addGlobal('conferences', $this->conferenceRepository->findAll());
    }

    /**
     * Returns the subscribed events for this event listener
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
