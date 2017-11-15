<?php
namespace Rate\Listener;

use Tk\Event\Subscriber;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class StaffSideMenuHandler implements Subscriber
{

    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     */
    public function onControllerInit(\Tk\Event\Event $event)
    {
        /** @var \App\Controller\Iface $controller */
        $controller = $event->get('controller');
        if ($controller->getCourse() && $controller->getUser()->isStaff()) {
            /** @var \App\Ui\Sidebar\StaffMenu $sideBar */
            $sideBar = $controller->getPage()->getSidebar();
            if ($sideBar instanceof \App\Ui\Sidebar\StaffMenu) {
                $sideBar->addReportUrl(\Tk\Ui\Link::create('Animals', \App\Uri::createCourseUrl('/animalTypeReport.html'), 'fa fa-paw'));
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