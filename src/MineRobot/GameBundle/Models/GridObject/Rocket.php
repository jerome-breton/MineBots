<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 07:02
 */

namespace MineRobot\GameBundle\Models\GridObject;


class Rocket extends GridObjectAbstract{
    protected $_useOrientation = true;

    protected $_picture = 'rocket';

    public function run(){
        $this->_forward();
        $this->_forward();

        return parent::run();
    }
} 