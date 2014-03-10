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
        $this->_forward(2);

        return parent::run();
    }

    /**
     * @param boolean $destroyed
     */
    public function setDestroyed($destroyed = true)
    {
        if($destroyed){
            $this->_createObject('explosion',1,-1);
            $this->_createObject('explosion',1,0);
            $this->_createObject('explosion',1,1);
            $this->_createObject('explosion',0,-1);
            $this->_createObject('explosion',0,0);
            $this->_createObject('explosion',0,1);
            $this->_createObject('explosion',-1,-1);
            $this->_createObject('explosion',-1,0);
            $this->_createObject('explosion',-1,1);
        }

        return parent::setDestroyed($destroyed);
    }
} 