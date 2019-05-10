<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 18.09.17
 * Time: 12:33
 */

namespace cronfy\import\readers;

use yii\db\Connection;
use yii\db\Query;

/**
 * @property Query $query
 */
class YiiQuery extends Generic
{
    /** @var Connection */
    public $connection;
    public $command;

    public $skipper;

    public function iterateItems()
    {
        $idsToQuery = [];
        foreach ($this->iterateIds() as $id) {
            if ($this->skipper) {
                if ($this->skipper->skip()) {
                    continue;
                }

                if (!$this->skipper->go()) {
                    return;
                }
            }

            $idsToQuery[] = $id;
        }

        $query = clone ($this->query);
        return $query->andWhere(['id' => $idsToQuery])->all($this->connection);
    }

    protected $_query;
    public function getQuery()
    {
        if ($this->_query === null) {
            $this->_query = new Query();
        }

        return $this->_query;
    }

    protected $_ids;
    protected function iterateIds()
    {
        if ($this->_ids === null) {
            $idsQuery = clone ($this->query);
            $this->_ids = $idsQuery->select('id')->column($this->connection);
        }

        return $this->_ids;
    }

    public function initColumns($lineData)
    {
        $columns = array_map(function ($item) {
            static $counter = 0;
            $counter++;
            return $item ?: 'noname_column_' . $counter;
        }, $lineData);

        $this->columns = $columns;
    }
}
