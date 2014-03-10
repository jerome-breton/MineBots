<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 09/03/14
 * Time: 16:24
 */

namespace MineRobot\GameBundle\Pilots\AI\Dumb;


use MineRobot\GameBundle\Pilots\PilotAbstract;

class Shield extends PilotAbstract
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
        return self::ORDER_STAY_SHIELD;
    }
}
