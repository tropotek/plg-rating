<?php
namespace Rate\Listener;

use Tk\Event\Subscriber;
use Rate\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class PlacementViewHandler implements Subscriber
{

    /**
     * @var \App\Controller\Placement\ReportEdit
     */
    private $controller = null;


    /**
     * @param \Tk\Event\Event $event
     * @throws \Exception
     */
    public function onControllerInit(\Tk\Event\Event $event)
    {
        /** @var \App\Controller\Student\Placement\View $controller */
        $controller = $event->get('controller');
        if ($controller instanceof \App\Controller\Student\Placement\View) {
            $this->controller = $controller;
            $view = $this->controller->getReportView();
            if (!$view) return;
            $template = $view->getTemplate();
            $report = $view->getReport();
            $placement = $report->getPlacement();
            if (!\Rate\Plugin::getInstance()->isProfileActive($placement->getSubject()->getProfileId())) {
                return;
            }
            $ratingStr = \App\Db\Phrase::findValue('star-rating', $placement->getSubject()->getProfileId());

            $template->appendCssUrl(\Tk\Uri::create(Plugin::getInstance()->getPluginPath().'/assets/rating.less'));

            $list = \Rate\Db\ValueMap::create()->findFiltered(array('placementId' => $placement->getId()));
            $view = \Rate\Ui\RatingListView::create($list);
            $template->appendHtml('report-info',
                sprintf('<dt>%s:</dt> <dd>%s</dd>', $ratingStr, $view->show()->toString()));
        }
    }

    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     */
    public function onControllerShow(\Tk\Event\Event $event) {}

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