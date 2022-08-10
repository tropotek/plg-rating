<?php
namespace Rate\Db;


use Bs\Db\Traits\OrderByTrait;
use Bs\Db\Traits\TimestampTrait;
use Uni\Db\Traits\CourseTrait;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Question extends \Tk\Db\Map\Model
{
    use CourseTrait;
    use TimestampTrait;
    use OrderByTrait;
    
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $courseId = 0;

    /**
     * @var string
     */
    public $text = '';

    /**
     * @var boolean
     */
    public $total = true;

    /**
     * @var string
     */
    public $help = '';

    /**
     * @var int
     */
    public $orderBy = 0;

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->_TimestampTrait();
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Question
     */
    public function setText(string $text): Question
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTotal(): bool
    {
        return $this->total;
    }

    /**
     * @param bool $total
     * @return Question
     */
    public function setTotal(bool $total): Question
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return string
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * @param string $help
     * @return Question
     */
    public function setHelp(string $help): Question
    {
        $this->help = $help;
        return $this;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();
        $errors = $this->validateCourseId($errors);

        if (!$this->getText()) {
            $errors['text'] = 'Please enter a valid text';
        }

        return $errors;
    }
}