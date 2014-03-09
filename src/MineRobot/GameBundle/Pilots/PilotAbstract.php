<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 09/03/14
 * Time: 16:13
 */

namespace MineRobot\GameBundle\Pilots;


abstract class PilotAbstract implements \Serializable
{

    const ORDER_MOVE_FORWARD = 'forward';
    const ORDER_TURN_LEFT = 'left';
    const ORDER_TURN_RIGHT = 'right';
    const ORDER_ATTACK_ROCKET = 'rocket';
    const ORDER_ATTACK_RAIL = 'rail';
    const ORDER_ATTACK_GAUNTLET = 'gauntlet';
    const ORDER_DEFEND_SHIELD = 'shield';
    const ORDER_REPAIR = 'repair';

    abstract function getOrder($env);
}