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
    //Move a case further according to direction
    const ORDER_MOVE_FORWARD = 'forward';
    //Turn in cell
    const ORDER_TURN_LEFT = 'left';
    const ORDER_TURN_RIGHT = 'right';
    //Fire a rocket moving 2cell/turn until collision
    const ORDER_ATTACK_ROCKET = 'rocket';
    //Fire an instant hitting rail on 10 cells ahead
    const ORDER_ATTACK_RAIL = 'rail';
    //Fire a lethal attack on front cell
    const ORDER_ATTACK_GAUNTLET = 'gauntlet';
    //Prevent any form off damage this turn
    const ORDER_STAY_SHIELD = 'shield';
    //Repair instead of moving
    const ORDER_STAY_REPAIR = 'repair';
    //Sacrifice a move to get better sight at next turn
    const ORDER_STAY_SCAN = 'scan';

    abstract function getOrder($env);
}