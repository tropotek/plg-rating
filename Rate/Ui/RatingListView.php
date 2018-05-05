<?php
namespace Rate\Ui;

use Dom\Template;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class RatingListView extends \Dom\Renderer\Renderer
{
    /**
     * @var \Rate\Db\Value[]
     */
    protected $list = null;


    /**
     * @param \Rate\Db\Value[] $list
     */
    public function __construct($list)
    {
        $this->list = $list;
    }

    /**
     * @param \Rate\Db\Value[] $list
     * @return RatingListView
     */
    public static function create($list)
    {
        $obj = new static($list);
        return $obj;
    }

    /**
     * @return \Dom\Template
     * @throws \Dom\Exception
     * @throws \Tk\Db\Exception
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (!$this->list || !count($this->list)) return $template;

        foreach ($this->list as $val) {
            $question = $val->getQuestion();

            $row = $template->getRepeat('question-row');
            $row->insertText('text', $question->text);
            $row->insertHtml('rating', \Rate\Ui\Stars::create($val->value, true));
            $row->appendRepeat();
            $template->setChoice('table');
        }

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
<div class="rating-view">   
  
  <ol class="star-rating-list">
    <li class="rating-question-value" repeat="question-row" var="question-row">
      <div class="pull-right" var="rating">%s</div> <span var="text">%s</span>
    </li>
  </ol>
  
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}
