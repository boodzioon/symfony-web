<?php

namespace App\EventSubscriber;

use App\Utils\Interfaces\CacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AdminChangesDataSubscriber implements EventSubscriberInterface
{

    protected $routeNamesThatMustClearCache = [
        'admin_categories.POST',
        'admin_edit_category.POST',
        'admin_delete_category.GET',
        'admin_upload_video_locally.POST',
        'admin_upload_video_to_vimeo.POST',
        'admin_delete_video.GET',
        'admin_set_video_duration.GET',
        'admin_update_video_category.POST',
        'like_video.POST',
        'dislike_video.POST',
        'unlike_video.POST',
        'undo_dislike_video.POST',
    ];

    public function __construct(private CacheInterface $cache) {}

    public function onResponseEvent(ResponseEvent $event): void
    {
        $routeName = $event->getRequest()->attributes->get('_route') . '.' . $event->getRequest()->getMethod();
        if (!in_array($routeName, $this->routeNamesThatMustClearCache)) {
            return;
        }

        $this->cache->cache->clear();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onResponseEvent',
        ];
    }
}
