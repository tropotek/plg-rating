<?php
namespace Rate\Form;

use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * Example:
 * <code>
 *   $form = new Question::create();
 *   $form->setModel($obj);
 *   $formTemplate = $form->getRenderer()->show();
 *   $template->appendTemplate('form', $formTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2019-06-06
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Question extends \App\FormIface
{

    /**
     * @throws \Exception
     */
    public function init()
    {
        $this->appendField(new Field\Input('text'));
        $this->appendField(new Field\Input('help'));
        $this->appendField(new Field\Checkbox('total'))->setCheckboxLabel('Add this questions values to the ' .
            \App\Db\Phrase::findValue('company', $this->getQuestion()->courseId) . ' total rating calculations.');

        
        $this->appendField(new Event\Submit('update', array($this, 'doSubmit')));
        $this->appendField(new Event\Submit('save', array($this, 'doSubmit')));
        $this->appendField(new Event\Link('cancel', $this->getBackUrl()));
    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function execute($request = null)
    {
        $this->load(\Rate\Db\QuestionMap::create()->unmapForm($this->getQuestion()));
        parent::execute($request);
    }

    /**
     * @param Form $form
     * @param Event\Iface $event
     * @throws \Exception
     */
    public function doSubmit($form, $event)
    {
        // Load the object with form data
        \Rate\Db\QuestionMap::create()->mapForm($form->getValues(), $this->getQuestion());

        // Do Custom Validations

        $form->addFieldErrors($this->getQuestion()->validate());
        if ($form->hasErrors()) {
            return;
        }
        
        $isNew = (bool)$this->getQuestion()->getId();
        $this->getQuestion()->save();

        // Do Custom data saving

        \Tk\Alert::addSuccess('Record saved!');
        $event->setRedirect($this->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create()->set('questionId', $this->getQuestion()->getId()));
        }
    }

    /**
     * @return \Tk\Db\ModelInterface|\Rate\Db\Question
     */
    public function getQuestion()
    {
        return $this->getModel();
    }

    /**
     * @param \Rate\Db\Question $question
     * @return $this
     */
    public function setQuestion($question)
    {
        return $this->setModel($question);
    }
    
}