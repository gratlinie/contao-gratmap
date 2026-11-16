<?php

declare(strict_types=1);

namespace Gratlinie\GratMapBundle\EventListener\Backend;

use Contao\CoreBundle\Event\MenuEvent;
use Knp\Menu\Util\MenuManipulator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Lädt das Backend-CSS (Gruppen-Icon) und setzt die Gruppe „GratMap“ direkt hinter „Inhalte“.
 */
#[AsEventListener(priority: -16)]
class BackendMenuListener
{
    public function __invoke(MenuEvent $event): void
    {
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName() || null === ($group = $tree->getChild('gratmap'))) {
            return;
        }

        $GLOBALS['TL_CSS']['gratmap_backend'] = 'bundles/gratliniegratmap/css/backend.css|static';

        $position = array_search('content', array_keys($tree->getChildren()), true);
        (new MenuManipulator())->moveToPosition($group, false === $position ? 0 : $position + 1);
    }
}
