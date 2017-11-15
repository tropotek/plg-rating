<?php
namespace Rate\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class StarRating extends \Tk\Form\Field\Input
{
    /**
     * @var int
     */
    protected $min = 0;

    /**
     * @var int
     */
    protected $max = 5;

    /**
     * @var float
     */
    protected $step = 1.0;

    /**
     * @var bool
     */
    protected $showClear = false;

    /**
     * @var bool
     */
    protected $showCaption = true;

    /**
     * @var null|array
     */
    protected $starCaptions = array('Not Rated', 'Strongly Disagree', 'Disagree', 'Acceptable', 'Agree', 'Strongly Agree');



    /**
     * @param string $name
     * @throws \Tk\Exception
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     * @return static
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     * @return static
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return float
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param float $step
     * @return static
     */
    public function setStep($step)
    {
        $this->step = $step;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowClear()
    {
        return $this->showClear;
    }

    /**
     * @param bool $showClear
     * @return static
     */
    public function setShowClear($showClear)
    {
        $this->showClear = $showClear;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowCaption()
    {
        return $this->showCaption;
    }

    /**
     * @param bool $showCaption
     * @return static
     */
    public function setShowCaption($showCaption)
    {
        $this->showCaption = $showCaption;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getStarCaptions()
    {
        return $this->starCaptions;
    }

    /**
     * @param array|null $starCaptions
     * @return static
     */
    public function setStarCaptions($starCaptions)
    {
        $this->starCaptions = $starCaptions;
        return $this;
    }



    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        $template->appendCssUrl(\Tk\Uri::create('/vendor/kartik-v/bootstrap-star-rating/css/star-rating.min.css'));
        $template->appendJsUrl(\Tk\Uri::create('/vendor/kartik-v/bootstrap-star-rating/js/star-rating.min.js'));

        $this->setType('number');
        // Options: http://plugins.krajee.com/star-rating#usage
        $template->setAttr('element', 'min', $this->getMin());
        $template->setAttr('element', 'max', $this->getMax());
        $template->setAttr('element', 'step', $this->getStep());
        $template->setAttr('element', 'data-size', 'xs');
        $template->setAttr('element', 'data-show-clear', 'true');   // show the clear button

        if (is_array($this->starCaptions) && count($this->starCaptions)) {
            $template->setAttr('element', 'data-show-caption', 'true');
            $template->setAttr('element', 'data-star-captions', json_encode($this->starCaptions));
        } else {
            $template->setAttr('element', 'data-show-caption', 'false');
            $template->setAttr('element', 'data-star-captions', '');
        }

        $js = <<<JS
jQuery(function($) {
  
  $('.tk-star-rating input').rating();
  
});
JS;
        $template->appendJs($js);

        parent::show();

        return $template;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {

        $xhtml = <<<HTML
<div class="tk-star-rating">
  <input type="text" var="element" class="form-control" />
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}
