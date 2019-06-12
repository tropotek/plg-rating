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
     * Manager constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Rating Question Manager');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->profile = \App\Db\ProfileMap::create()->find($request->get('profileId'));
        $this->subject = $this->getConfig()->getSubject();
        if (!$this->profile && $this->subject)
            $this->profile = $this->subject->getProfile();


        $this->setTable(\Rate\Table\Question::create());
        $this->getTable()->setEditUrl(\App\Uri::createHomeUrl('/ratingQuestionEdit.html'));
        $this->getTable()->init();

        $filter = array(
            'profileId' => $this->profile->getId()
        );
        $this->getTable()->setList($this->getTable()->findList($filter));

    }

    /**
     *
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('New Question',
            $this->getTable()->getEditUrl()->set('profileId', $this->profile->getId()), 'fa fa-star fa-add-action'));
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->initActionPanel();
        $template = parent::show();

        $template->appendTemplate('panel', $this->getTable()->show());

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
<div class="tk-panel" data-panel-title="Rating Questions" data-panel-icon="fa fa-star" var="panel"></div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}

