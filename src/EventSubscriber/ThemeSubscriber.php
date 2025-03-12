<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly final class ThemeSubscriber implements EventSubscriberInterface
{
  public function onKernelController(ControllerEvent $event): void
  {
    $request = $event->getRequest();
    $themes = ['dark', 'light'];
    $themeCookie = $request->cookies->get('theme');

    $request->attributes->set(
      'theme',
      \in_array($themeCookie, $themes) ? $themeCookie : $themes[0],
    );

    $request->attributes->set('themes', $themes);
  }

  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::CONTROLLER => 'onKernelController',
    ];
  }
}
