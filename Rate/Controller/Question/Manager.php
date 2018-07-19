<?php
namespace Rate\Controller\Question;

use Dom\Template;
use Tk\Form\Field;
use Tk\Request;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Manager extends \App\Controller\AdminManagerIface
{

    /**
     * @var \App\Db\Subject
     */
    private $subject = null;

    /**
     * @var \App\Db\Profile
     */
    private $profile = null;


    /**
     * @var null|\Tk\Uri
     */
    private $editUrl = null;



    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Rating Question Manager');
    }

    /**
     * @param Request $request
     * @throws \Exception
     * @throws \Tk\Db\Exception
     * @throws \Tk\Form\Exception
     */
    public function doDefault(Request $request)
    {
        $this->profile = \App\Db\ProfileMap::create()->find($request->get('profileId'));
        $this->subject = $this->getConfig()->getSubject();
        if (!$this->profile && $this->subject)
            $this->profile = $this->subject->getProfile();

        $this->editUrl = \App\Uri::createHomeUrl('/ratingQuestionEdit.html');

        $u = clone $this->editUrl;
        $this->getActionPanel()->add(\Tk\Ui\Button::create('New Question',
            $u->set('profileId', $this->profile->getId()), 'fa fa-star fa-add-action'));

        $this->table = \App\Config::getInstance()->createTable(\App\Config::getInstance()->getUrlName());
        $this->table->setRenderer(\App\Config::getInstance()->createTableRenderer($this->table));

        $this->table->addCell(new \Tk\Table\Cell\Checkbox('id'));
        $this->table->addCell(new \Tk\Table\Cell\Text('text'))->addCss('key')->setUrl(clone $this->editUrl);
        $this->table->addCell(new \Tk\Table\Cell\Date('modified'));

        // Filters
        $this->table->addFilter(new Field\Input('keywords'))->setAttr('placeholder', 'Keywords');

        // Actions
        $this->table->addAction(\Tk\Table\Action\ColumnSelect::create()->setDisabled(array('id', 'name')));
        $this->table->addAction(\Tk\Table\Action\Csv::create());
        $this->table->addAction(\Tk\Table\Action\Delete::create());

        $this->table->setList($this->getList());

    }

    /**
     * @return \Rate\Db\Question[]|\Tk\Db\Map\ArrayObject
     * @throws \Exception
     */
    protected function getList()
    {
        $filter = $this->table->getFilterValues();
        $filter['profileId'] = $this->profile->getId();
        return \Rate\Db\QuestionMap::create()->findFiltered($filter, $this->table->getTool());
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $template->replaceTemplate('table', $this->table->getRenderer()->show());

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
      <h4 class="panel-title"><i class="fa fa-star"></i> Rating Questions</h4>
    </div>
    <div class="panel-body">
      <div var="table"></div>
    </div>
  </div>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}

