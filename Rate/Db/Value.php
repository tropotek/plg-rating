<?php
namespace Rate\Db;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Value extends \Tk\Db\Map\Model
{
    
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $questionId = 0;

    /**
     * @var int
     */
    public $placementId = 0;

    /**
     * @var string
     */
    public $value = '';

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;


    /**
     * @var Question
     */
    private $question = null;

    /**
     * @var \App\Db\Placement
     */
    private $placement = null;



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->modified = \Tk\Date::create();
        $this->created = \Tk\Date::create();
    }

    /**
     * @param \App\Db\Placement $placement
     * @param \Rate\Db\Question $question
     * @param int $value
     * @return Value
     */
    public static function create($placement, $question, $value)
    {
        $obj = new static();
        $obj->placementId = $placement->id;
        $obj->questionId = $question->id;
        $obj->value = (int)$value;
        return $obj;
    }

    /**
     *
     */
    public function save()
    {
        parent::save();
    }

    /**
     * @return null|Question|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     * @throws \Tk\Db\Exception
     */
    public function getQuestion()
    {
        if (!$this->question) {
            $this->question = QuestionMap::create()->find($this->questionId);
        }
        return $this->question;
    }

    /**
     * @return \App\Db\Placement|null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     * @throws \Tk\Db\Exception
     */
    public function getPlacement()
    {
        if (!$this->placement) {
            $this->placement = \App\Db\PlacementMap::create()->find($this->placementId);
        }
        return $this->placement;
    }


    /**
     * If a $placementId is supplied then total rating will be for that placement only
     *
     * @param int $companyId
     * @param int $placementId
     * @return int|null Null is returned when there have been no rating values logged
     * @throws \Tk\Db\Exception
     */
    public static function getCompanyRating($companyId, $placementId = 0)
    {
        $filter = array(
            'companyId' => $companyId
        );
        if ($placementId) {
            $filter['placementId'] = $placementId;
        }
        $list = ValueMap::create()->findFiltered($filter);
        if (!count($list)) return null;

        $cnt = 0;
        $tot = 0;
        foreach($list as $i => $r) {
            //if (!$r->getQuestion()->total) continue;
            $cnt++;
            $tot += (int)$r->value;
        }
        return round(($tot/$cnt), 2);
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if ((int)$this->questionId <= 0) {
            $errors['typeId'] = 'Invalid Type ID';
        }
        if ((int)$this->placementId <= 0) {
            $errors['placementId'] = 'Invalid Placement ID';
        }

        return $errors;
    }
}