<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 07:03
 */

namespace MineRobot\GameBundle\Models\GridObject;


class Robot extends GridObjectAbstract{
    protected $_useOrientation = true;
    protected $_useVariation = true;

    protected $_variations = ['black','blue','cyan','gray','green','pink','purple','red','white','yellow'];

    protected $_base_picture = 'robot';

    protected $_pilot = null;

    protected $_life = 1;
    protected $_score = 0;
    protected $_minerals = 0;

    public function __construct($data){
        parent::__construct($data);

        if(isset($data['life'])){
            $this->_life = $data['life'];
        }
        if(isset($data['score'])){
            $this->_score = $data['score'];
        }
        if(isset($data['minerals'])){
            $this->_minerals = $data['minerals'];
        }
        if(isset($data['pilot'])){
            $class = $data['pilot'];
            $this->_pilot = new $class();

            if(isset($data['sleepString'])){
                $this->_pilot->sleepString = $data['sleepString'];
            }
        }
    }

    public function getSleepArray(){
        $array = parent::getSleepArray();

        $array['life'] = $this->_life;
        $array['score'] = $this->_score;
        $array['minerals'] = $this->_minerals;
        $array['pilot'] = get_class($this->_pilot);
        $array['sleepString'] = $this->_pilot->sleepString;

        return $array;
    }
}