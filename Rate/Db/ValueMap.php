<?php
namespace Rate\Db;

use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ValueMap extends \App\Db\Mapper
{
    /**
     * Mapper constructor.
     *
     * @param \Tk\Db\Pdo|null $db
     * @throws \Exception
     * @throws \Tk\Db\Exception
     */
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->setMarkDeleted('');
    }

    /**
     * @return \Tk\DataMap\DataMap
     * @throws \Tk\Db\Exception
     */
    public function getDbMap()
    {
        if (!$this->dbMap) {
            $this->setTable('rating_value');
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('questionId', 'question_id'));
            $this->dbMap->addPropertyMap(new Db\Integer('placementId', 'placement_id'));
            $this->dbMap->addPropertyMap(new Db\Text('value'));
            $this->dbMap->addPropertyMap(new Db\Date('modified'));
            $this->dbMap->addPropertyMap(new Db\Date('created'));
        }
        return $this->dbMap;
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getFormMap()
    {
        if (!$this->formMap) {
            $this->formMap = new \Tk\DataMap\DataMap();
            $this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
            $this->formMap->addPropertyMap(new Form\Integer('questionId'));
            $this->formMap->addPropertyMap(new Form\Integer('placementId'));
            $this->formMap->addPropertyMap(new Form\Text('value'));
        }
        return $this->formMap;
    }


    /**
     * Remove all values from the DB by placementId
     *
     * @param $placementId
     * @return $this
     * @throws \Tk\Db\Exception
     */
    public function removeAllByPlacementId($placementId)
    {
        $list = $this->findFiltered(array('placementId' => $placementId));
        foreach ($list as $v) {
            $v->delete();
        }
        return $this;
    }

    /**
     * @param $questionId
     * @param $placementId
     * @return Value
     * @throws \Tk\Db\Exception
     */
    public function findValue($questionId, $placementId)
    {
        return $this->findFiltered(array('questionId' => $questionId, 'placementId' => $placementId))->current();
    }

    /**
     * Find filtered records
     *
     * @param array $filter
     * @param Tool $tool
     * @return ArrayObject|Value[]
     * @throws \Tk\Db\Exception
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        list($from, $where) = $this->processFilter($filter);
        $r = $this->selectFrom($from, $where, $tool);
        return $r;
    }

    /**
     * @param $filter
     * @return array
     */
    protected function processFilter($filter)
    {
        $from = sprintf('%s a ', $this->quoteTable($this->getTable()));
        $where = '';

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.value LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) {
                $where .= '(' . substr($w, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['questionId'])) {
            $where .= sprintf('a.question_id = %s AND ', (int)$filter['questionId']);
        }

        if (!empty($filter['placementId'])) {
            $where .= sprintf('a.placement_id = %s AND ', (int)$filter['placementId']);
        }

        if (!empty($filter['profileId'])) {
            $from .= sprintf(', %s b', $this->quoteTable('rating_question'));
            $where .= sprintf('a.question_id = b.id AND ');
            $where .= sprintf('b.profile_id = %s AND ', (int)$filter['profileId']);
        }


        if (!empty($filter['subjectId']) || !empty($filter['companyId'])) {
            $from .= sprintf(', %s c', $this->quoteTable('placement'));
            $where .= sprintf('a.placement_id = c.id AND ');
            if (!empty($filter['subjectId'])) {
                $where .= sprintf('c.subject_id = %s AND ', (int)$filter['subjectId']);
            }
            if (!empty($filter['companyId'])) {
                $where .= sprintf('c.company_id = %s AND ', (int)$filter['companyId']);
            }
        }

        if (!empty($filter['value'])) {
            $where .= sprintf('a.value = %s AND ', $this->quote($filter['value']));
        }

        if (!empty($filter['dateFrom'])) {
            /** @var \DateTime $dtef */
            $dtef = \Tk\Date::floor($filter['dateFrom']);
            $where .= sprintf('a.created >= %s AND ', $this->quote($dtef->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }
        if (!empty($filter['dateTo'])) {
            /** @var \DateTime $dtet */
            $dtet = \Tk\Date::ceil($filter['dateTo']);
            $where .= sprintf('a.created <= %s AND ', $this->quote($dtet->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) {
                $where .= '('. $w . ') AND ';
            }
        }

        if ($where) {
            $where = substr($where, 0, -4);
        }

        $r = array($from, $where);
        return $r;
    }


    /**
     * @param array $filter
     * @param Tool $tool
     * @return float
     */
    public function findAverage($filter, $tool = null)
    {
        list($from, $where) = $this->processFilter($filter);
        if (!$where) $where = '1';

        $sql = sprintf('SELECT AVG(a.value) as avg
FROM %s
WHERE %s', $from, $where);
        $stm = $this->getDb()->prepare($sql);
        $stm->execute();
        $r = $stm->fetch();
        
        return $r->avg;
    }

}