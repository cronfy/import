<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 18.09.17
 * Time: 12:33
 */

namespace cronfy\import\readers;

use yii\base\BaseObject;

abstract class Generic extends BaseObject
{
    public function iterateItems()
    {
        return [];
    }

    public function getAll() {
        $items = [];

        foreach ($this->iterateItems() as $item) {
            $items[] = $item;
        }

        return $items;
    }
}
