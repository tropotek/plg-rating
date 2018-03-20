<?php
namespace Rate\Listener;

use Dom\Exception;
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
     * @throws \Tk\Db\Exception
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
            /** @var \App\Db\Company $company */
            $company = \App\Db\CompanyMap::create()->find($message->get('company::id'));
            if(!$company) continue;

            $value = (float)\Rate\Db\ValueMap::create()->findAverage(array('companyId' => $company->getId()));
            $message->set('company::starRating', $value);





        }

//
//
//        $mailTemplateList = \App\Db\MailTemplateMap::create()->findFiltered(
//            array('profileId' => $event->getStatus()->profileId, 'event' => $event->getStatus()->event));
//
//        /** @var \App\Db\MailTemplate $mailTemplate */
//        foreach ($mailTemplateList as $mailTemplate) {
//            $modelStrategy = $event->getStatus()->getModelStrategy();
//            if (!$modelStrategy) {
//                \Tk\Log::warning('onSendStatusMessage: Strategy Not Found For: ' . $event->getStatus()->fkey);
//                continue;
//            }
//
//            $message = $modelStrategy->makeStatusMessage($event->getStatus(), $mailTemplate);
//
//            // Save the message for sending
//            if ($message instanceof \Tk\Mail\Message) {
//                \App\Util\StatusMessage::setStatus($message, $event->getStatus());
//                \App\Util\StatusMessage::setProfile($message, $event->getStatus()->getProfile());
//                \App\Util\StatusMessage::setSubject($message, $event->getStatus()->getSubject());
//                \App\Util\StatusMessage::setRecipient($message, $mailTemplate->recipient);
//
//                if ($message->hasRecipient()) {
//                    $event->addMessage($message);
//                }
//            }
//        }
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \App\StatusEvents::STATUS_CHANGE => array('onSendStatusMessage', 0)
        );
    }

}


