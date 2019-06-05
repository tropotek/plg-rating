<?php
namespace Rate\Listener;

use Tk\Event\Subscriber;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class StatusMailHandler implements Subscriber
{

    /**
     * @param \App\Event\StatusEvent $event
     * @throws \Exception
     */
    public function onSendStatusMessage(\App\Event\StatusEvent $event)
    {
        // do not send messages
        if (!$event->getStatus()->notify || !$event->getStatus()->getProfile()->notifications) {
            \Tk\Log::warning('onSendStatusMessage: Status Notification Disabled');
            return;
        }

        /** @var \Tk\Mail\CurlyMessage $message */
        foreach ($event->getMessageList() as $message) {
            if (!\Rate\Plugin::getInstance()->isProfileActive($message->get('profile::id'))) return;

            /** @var \App\Db\Company $company */
            $company = \App\Db\CompanyMap::create()->find($message->get('company::id'));
            if(!$company) continue;

            $value = (float)\Rate\Db\ValueMap::create()->findAverage(array('companyId' => $company->getId()));
            $message->set('company::starRating', $value);

            $questionRatings = '';
            $questionList = \Rate\Db\QuestionMap::create()->findFiltered(array('profileId' => $message->get('profile::id')));
            foreach ($questionList as $q) {
                $ratingsList = \Rate\Db\ValueMap::create()->findFiltered(array('companyId' => $company->id, 'questionId' => $q->id));
                $cnt = $ratingsList->count();
                $tot = 0;
                $rating = '';

                if ($cnt) {
                    foreach ($ratingsList as $v) {
                        $tot += (int)$v->value;
                    }
                    $r = round($tot / $cnt, 2);
                    $rating = sprintf('%.2f', $r);
                }

                $questionRatings .= sprintf('<li>%s [Rating: %s / 5.00]</li>', $q->text, $rating);
            }
            if ($questionRatings) {
                $questionRatings = '<ol>' . $questionRatings . '</ol>';
            }
            $message->set('company::questionRatings', $questionRatings);
        }

    }

    /**
     * @param \App\Event\PlacementReportEvent $event
     * @throws \Exception
     */
    public function onCommentReport(\App\Event\PlacementReportEvent $event)
    {
        $report = $event->getPlacementReport();
        if (!$report->getPlacement() || !\Rate\Plugin::getInstance()->isProfileActive($report->getPlacement()->getSubject()->getProfileId())) return;

        $val = \Rate\Db\Value::getCompanyRating($report->getPlacement()->companyId, $report->placementId);
        if ($val !== null) {
            $html = sprintf('<p><small>Rating: %.2f / 5.00</small></p>', $val);
            $event->set('postHtml', $html);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \App\StatusEvents::STATUS_CHANGE => array('onSendStatusMessage', 0),
            \App\AppEvents::COMPANY_COMMENT_REPORT => array('onCommentReport', 0)
        );
    }

}


