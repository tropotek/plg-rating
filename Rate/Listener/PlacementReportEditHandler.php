<?php
namespace Rate\Listener;

use Tk\Event\Subscriber;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
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
     * @throws \Exception
     */
    public function onFormPreInit(\Tk\Event\FormEvent $event)
    {
        /** @var \App\Controller\Placement\ReportEdit $controller */
        $controller = $event->getForm()->get('controller');
        if ($controller instanceof \App\Controller\Placement\ReportEdit) {
            if (!\Rate\Plugin::getInstance()->isCourseActive($controller->getCourse()->getId())) return;
            if ($controller->getSubject() && $controller->getPlacement()) {
                $this->controller = $controller;
                $this->form = $controller->getForm();
                $this->questionList = \Rate\Db\QuestionMap::create()->findFiltered(array('courseId' => $this->controller->getCourse()->getId()));
            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     * @throws \Tk\Form\Exception
     */
    public function onFormInit(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $reportLabel = \App\Db\Phrase::findValue('report', $this->controller->getCourse()->getId());
            $companyStr = \App\Db\Phrase::findValue('company', $this->controller->getCourse()->getId());
            $this->form->appendField(new \Tk\Form\Field\Html($companyStr . '-rRating', 'Please rate your experience with this ' . strtolower($companyStr)))
                ->removeCss('form-control form-control-static form-control-plaintext')
                ->addCss('text-italic')
                ->setLabel('')
                ->setFieldset($companyStr . ' ' . $reportLabel, 'tks-star-rating');
            foreach ($this->questionList as $question) {
                $name = 'sr-' . $question->id;
                $this->form->appendField(new \Rate\Form\Field\StarRating($name))->setFieldset($companyStr . ' ' . $reportLabel)
                    ->setLabel($question->text)->setNotes($question->help);
            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     * @throws \Tk\Exception
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
     * @throws \Tk\Db\Exception
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