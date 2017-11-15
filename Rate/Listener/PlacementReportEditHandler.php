<?php
namespace Rate\Listener;

use Tk\Event\Subscriber;
use Rate\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class PlacementReportEditHandler implements Subscriber
{

    /**
     * @var \App\Controller\Placement\ReportEdit
     */
    private $controller = null;

    /**
     * @var \Tk\Form
     */
    private $form = null;

    /**
     * @var null|\Tk\Db\Map\ArrayObject|\Rate\Db\Question[]
     */
    private $questionList = null;


    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormPreInit(\Tk\Event\FormEvent $event)
    {
        /** @var \App\Controller\Placement\ReportEdit $controller */
        $controller = $event->getForm()->get('controller');
        if ($controller instanceof \App\Controller\Placement\ReportEdit) {
            if ($controller->getUser()->isStaff() && $controller->getCourse() && $controller->getPlacement()) {
                $this->controller = $controller;
                $this->form = $controller->getForm();
                $this->questionList = \Rate\Db\QuestionMap::create()->findFiltered(array('profileId' => $this->controller->getProfile()->getId()));
            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormInit(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            foreach ($this->questionList as $question) {
                $name = 'sr-' . $question->id;
                $this->form->addField(new \Rate\Form\Field\StarRating($name))->setFieldset('Company Report')->
                    setLabel($question->text)->setNotes($question->help);
            }

        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormLoad(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            foreach ($this->questionList as $question) {
                $name = 'sr-' . $question->id;
                $value = \Rate\Db\ValueMap::create()->findValue($question->getId(), $this->controller->getPlacement()->getId());
                if (!$value) continue;
                $this->form->setFieldValue($name, $value->value);
            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormSubmit(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $placement = $this->controller->getPlacement();

            // Validate star ratings
            foreach ($this->questionList as $question) {
                $name = 'sr-' . $question->id;
                if (!$this->form->getFieldValue($name)) {
                    $this->form->addFieldError($name, 'Please select a value for this question.');
                }
            }

            if ($this->form->hasErrors()) return;

            // Save the new question values
            foreach ($this->questionList as $question) {
                $name = 'sr-' . $question->id;
                $value = \Rate\Db\ValueMap::create()->findvalue($question->getId(), $placement->getId());
                if (!$value) {
                    $value = new \Rate\Db\Value();
                }
                $value->placementId = $placement->getId();
                $value->questionId = $question->getId();
                $value->value = $this->form->getFieldValue($name);
                $value->save();
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
            \Tk\Form\FormEvents::FORM_INIT => array(array('onFormPreInit', 0), array('onFormInit', 0)),
            \Tk\Form\FormEvents::FORM_LOAD => array('onFormLoad', 0),
            \Tk\Form\FormEvents::FORM_SUBMIT => array('onFormSubmit', 0),
            \Tk\PageEvents::CONTROLLER_SHOW => array('onControllerShow', 0)
        );
    }

}