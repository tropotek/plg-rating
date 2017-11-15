<?php
namespace Rate\Listener;

use Tk\Event\Subscriber;
use Rate\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CompanyViewHandler implements Subscriber
{

    /**
     * @var \App\Controller\Placement\ReportEdit
     */
    private $controller = null;


    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     */
    public function onControllerInit(\Tk\Event\Event $event)
    {
        /** @var \Tk\Controller\Iface $controller */
        $this->controller = $event->get('controller');
        if ($this->controller instanceof \App\Controller\Company\View) {
            if ($this->controller->getUser()->isStaff() || $this->controller->getUser()->isStudent()) {
                $template = $this->controller->getTemplate();
                $company = $this->controller->getCompany();

                // Company Profile Total
                $value = (float)\Rate\Db\ValueMap::create()->findAverage(array('companyId' => $company->getId()));
                $html = sprintf('<div class="rate-star-rating pull-right"><em>Star Rating</em><br/>%s</div>', \Rate\Ui\Stars::create($value, true));
                $template->appendHtml('top-col-right', $html);

                $template->appendCssUrl(\Tk\Uri::create(Plugin::getInstance()->getPluginPath().'/assets/rating.less'));

                // Individual rating question list
                $questionList = \Rate\Db\QuestionMap::create()->findFiltered(array('profileId' => $company->profileId));
                $html = '';
                foreach ($questionList as $question) {
                    $value = (float)\Rate\Db\ValueMap::create()->findAverage(array('companyId' => $company->getId(), 'questionId' => $question->getId()));
                    $html .= sprintf('<li class="rating-question-value"><div class="pull-right">%s</div><span>%s</span></li>',
                        \Rate\Ui\Stars::create($value, true), $question->text);
                }
                if ($html) {
                    $tpl = <<<HTML
<section class="companyRating">
  <h5 class="content-title">Company Rating</h5>
  <ul class="star-rating-list">
    %s
  </ul>
</section>
HTML;

                    $html = sprintf($tpl, $html);
                    $template->appendHtml('right-col', $html);
                }
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