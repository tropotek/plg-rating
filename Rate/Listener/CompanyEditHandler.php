<?php
namespace Rate\Listener;

use Tk\Event\Subscriber;
use Rate\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CompanyEditHandler implements Subscriber
{

    /**
     * @var \App\Controller\Placement\ReportEdit
     */
    private $controller = null;


    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     * @throws \Dom\Exception
     * @throws \ReflectionException
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function onControllerInit(\Tk\Event\Event $event)
    {
        /** @var \Tk\Controller\Iface $controller */
        $this->controller = $event->get('controller');
        if ($this->controller instanceof \App\Controller\Company\Edit) {
            if (!\Rate\Plugin::getInstance()->isProfileActive($this->controller->getProfile()->getId())) return;
            if ($this->controller->getUser()->isStaff()) {
                $template = $this->controller->getTemplate();
                $company = $this->controller->getCompany();
                if (!$company->getId()) return;

                $template->appendCssUrl(\Tk\Uri::create(Plugin::getInstance()->getPluginPath().'/assets/rating.less'));

                // Company Profile Total
                $value = (float)\Rate\Db\ValueMap::create()->findAverage(array('companyId' => $company->getId()));
                $totalHtml = sprintf('<div class="rate-star-rating pull-right">%s</div>', \Rate\Ui\Stars::create($value, true));

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
  <div class="panel panel-default" choice="hide">
    <div class="panel-heading">
      %s
      <h4 class="panel-title"><i class="fa fa-star"></i> <span>%s</span></h4>
    </div>
    <div class="panel-body">
      <ul class="star-rating-list">
        %s
      </ul>
    </div>
  </div>
HTML;

                    $html = sprintf($tpl, $totalHtml, \App\Db\Phrase::findValue('star-rating', $company->profileId), $html);
                    $template->appendHtml('edit', $html);
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