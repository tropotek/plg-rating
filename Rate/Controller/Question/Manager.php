<?php
namespace Rate\Controller\Question;

use Dom\Template;
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
     * @var \App\Db\Course
     */
    private $course = null;


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
        $this->course = $this->getConfig()->getCourseMapper()->find($request->get('courseId'));
        $this->subject = $this->getConfig()->getSubject();
        if (!$this->course && $this->subject)
            $this->course = $this->subject->getCourse();

        $this->setTable(\Rate\Table\Question::create());
        $this->getTable()->setEditUrl(\Uni\Uri::createHomeUrl('/ratingQuestionEdit.html'));
        $this->getTable()->init();

        $filter = array(
            'courseId' => $this->course->getId()
        );
        $this->getTable()->setList($this->getTable()->findList($filter));

    }

    /**
     *
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('New Question',
            $this->getTable()->getEditUrl()->set('courseId', $this->course->getId()), 'fa fa-star fa-add-action'));
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

