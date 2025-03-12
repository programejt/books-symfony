<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Translation\LocaleSwitcher;

readonly final class LocaleSubscriber implements EventSubscriberInterface
{
  public function __construct(
    private ParameterBagInterface $params,
    private LocaleSwitcher $localeSwitcher,
  ) {}

  public function onKernelController(ControllerEvent $event): void
  {
    $request = $event->getRequest();
    $locales = $this->params->get('kernel.enabled_locales');
    $localeCookie = $request->cookies->get('locale');
    $userLocale = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;

    $request->attributes->set('locales', $locales);

    $locale = match (true) {
      $localeCookie !== null => $localeCookie,
      $userLocale != null    => \substr($userLocale, 0, 2),
      default                => null,
    };

    if (\in_array($locale, $locales)) {
      $request->setLocale($locale);
      $this->localeSwitcher->setLocale($locale);
    }
  }

  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::CONTROLLER => 'onKernelController',
    ];
  }
}
