<?php
namespace Rate\Db;


use App\Db\Traits\PlacementTrait;
use Bs\Db\Traits\TimestampTrait;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Value extends \Tk\Db\Map\Model
{
    use TimestampTrait;
    use PlacementTrait;



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
    private $_question = null;


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->_TimestampTrait();
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
        $obj->placementId = $placement->getId();
        $obj->questionId = $question->getId();
        $obj->value = (int)$value;
        return $obj;
    }

    /**
     * @return null|Question|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     * @throws \Tk\Db\Exception
     */
    public function getQuestion()
    {
        if (!$this->_question) {
            $this->_question = QuestionMap::create()->find($this->questionId);
        }
        return $this->_question;
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
            $cnt++;
            $tot += (int)$r->value;
        }
        return round(($tot/$cnt), 2);
    }

    /**
     * @return int
     */
    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    /**
     * @param int $questionId
     * @return Value
     */
    public function setQuestionId(int $questionId): Value
    {
        $this->questionId = $questionId;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Value
     */
    public function setValue(string $value): Value
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();
        $errors = $this->validatePlacementId($errors);

        if ((int)$this->questionId <= 0) {
            $errors['typeId'] = 'Invalid Type ID';
        }

        return $errors;
    }
}