<?php
namespace Rate\Listener;

use Rate\Plugin;
use Tk\Event\Subscriber;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CourseEditHandler implements Subscriber
{

    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     * @throws \Exception
     */
    public function onControllerInit(\Tk\Event\Event $event)
    {
        /** @var \Tk\Controller\Iface $controller */
        $controller = $event->get('controller');
        if ($controller instanceof \App\Controller\Course\Edit) {
            if (!Plugin::getInstance()->isZonePluginEnabled(\Rate\Plugin::ZONE_COURSE, $controller->getCourse()->getId())) return;
            if ($controller->getAuthUser()->isStaff() && $controller->getCourse()) {
                /** @var \Tk\Ui\Admin\ActionPanel $actionPanel */
                $actionPanel = $controller->getActionPanel();
                $actionPanel->append(\Tk\Ui\Link::createBtn('Rating Questions',
                    \Uni\Uri::createHomeUrl('/ratingQuestionManager.html')->set('courseId', $controller->getCourse()->getId()), 'fa fa-star'));
            }
        }
    }

    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     */
    public function onControllerShow(\Tk\Event\Event $event) { }


    /**
     * @return array The event names to listen to
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\PageEvents::CONTROLLER_INIT => array('onControllerInit', 0),
            \Tk\PageEvents::CONTROLLER_SHOW => array('onControllerShow', 0)
        );
    }
    
}