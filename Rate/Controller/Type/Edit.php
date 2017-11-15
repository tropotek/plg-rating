<?php
namespace Rate\Controller\Type;

use App\Controller\AdminEditIface;
use Dom\Template;
use Tk\Form\Event;
use Tk\Form\Field;
use Tk\Request;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Edit extends AdminEditIface
{

    /**
     * @var \Rate\Db\Type
     */
    protected $type = null;



    /**
     * Iface constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setPageTitle('Animal Type Edit');
    }

    /**
     *
     * @param Request $request
     */
    public function doDefault(Request $request)
    {
        $this->type = new \Rate\Db\Type();
        $this->type->profileId = (int)$request->get('profileId');
        if ($request->get('typeId')) {
            $this->type = \Rate\Db\TypeMap::create()->find($request->get('typeId'));
        }

        $this->buildForm();

        $this->form->load(\Rate\Db\TypeMap::create()->unmapForm($this->type));
        $this->form->execute($request);
    }


    protected function buildForm() 
    {
        $this->form = \App\Factory::createForm('animalTypeEdit');
        $this->form->setRenderer(\App\Factory::createFormRenderer($this->form));

        $this->form->addField(new Field\Input('name'));
        $this->form->addField(new \App\Form\Field\MinMax('min', 'max'));
        $this->form->addField(new Field\Textarea('description'));
        $this->form->addField(new Field\Textarea('notes'));

        $this->form->addField(new Event\Button('update', array($this, 'doSubmit')));
        $this->form->addField(new Event\Button('save', array($this, 'doSubmit')));
        $this->form->addField(new Event\Link('cancel', \App\Factory::getCrumbs()->getBackUrl()));

    }

    /**
     * @param \Tk\Form $form
     */
    public function doSubmit($form)
    {
        // Load the object with data from the form using a helper object
        \Rate\Db\TypeMap::create()->mapForm($form->getValues(), $this->type);

        $form->addFieldErrors($this->type->validate());

        if ($form->hasErrors()) {
            return;
        }
        $this->type->save();

        \Tk\Alert::addSuccess('Record saved!');
        if ($form->getTriggeredEvent()->getName() == 'update') {
            \App\Factory::getCrumbs()->getBackUrl()->redirect();
        }
        \Tk\Uri::create()->set('typeId', $this->type->getId())->redirect();
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        // Render the form
        $template->insertTemplate('form', $this->form->getRenderer()->show()->getTemplate());

        return $template;
    }

    /**
     * DomTemplate magic method
     *
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>
    
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><i class="fa fa-paw"></i> <span var="panel-title">Animal Type Edit</span></h4>
    </div>
    <div class="panel-body">
      <div var="form"></div>
    </div>
  </div>
  
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}