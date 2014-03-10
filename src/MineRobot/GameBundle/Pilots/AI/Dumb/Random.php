<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 09/03/14
 * Time: 16:24
 */

namespace MineRobot\GameBundle\Pilots\AI\Dumb;


use MineRobot\GameBundle\Pilots\PilotAbstract;

class Random extends PilotAbstract
{

    protected $_dummyVar = 'something';

    public function serialize()
    {
        return serialize(array(
            'dummyVar' => $this->_dummyVar
        ));
    }

    public function unserialize($string)
    {
        $data = unserialize($string);

        $this->_dummyVar = $data['dummyVar'];
    }

    public function getOrder($env)
    {
        switch (rand(1, 10)) {
            case 1:
                return self::ORDER_TURN_LEFT;
            case 2:
                return self::ORDER_TURN_RIGHT;
            case 3:
                return self::ORDER_ATTACK_GAUNTLET;
            case 4:
                return self::ORDER_ATTACK_RAIL;
            case 5:
                return self::ORDER_ATTACK_ROCKET;
            case 6:
                return self::ORDER_STAY_SHIELD;
            case 7:
                return self::ORDER_STAY_REPAIR;
            default:
                return self::ORDER_MOVE_FORWARD;
        }
    }
}
