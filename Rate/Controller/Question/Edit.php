<?php
namespace Rate\Controller\Question;

use Dom\Template;
use Tk\Request;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Edit extends \App\Controller\AdminEditIface
{

    /**
     * @var \Rate\Db\Question
     */
    protected $question = null;


    /**
     * Iface constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Rating Question Edit');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->question = new \Rate\Db\Question();
        $this->question->setCourseId((int)$request->get('courseId'));
        if ($request->get('questionId')) {
            $this->question = \Rate\Db\QuestionMap::create()->find($request->get('questionId'));
        }

        $this->setForm(\Rate\Form\Question::create()->setModel($this->question));
        $this->initForm($request);
        $this->getForm()->execute();
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        // Render the form
        $template->appendTemplate('panel', $this->getForm()->show());

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
<div class="tk-panel" data-panel-title="Rating Question Edit" data-panel-icon="fa fa-star" var="panel"></div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}