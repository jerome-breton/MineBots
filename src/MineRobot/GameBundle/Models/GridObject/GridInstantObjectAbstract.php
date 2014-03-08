<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:53
 */

namespace MineRobot\GameBundle\Models\GridObject;


class GridInstantObjectAbstract extends GridObjectAbstract{

    public function run(){
        $this->_destroy();
        return parent::run();
    }
} 