<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 09/03/14
 * Time: 16:13
 */

namespace MineRobot\GameBundle\Pilots;


use MineRobot\GameBundle\Models\Game;
use MineRobot\GameBundle\Models\GridObject\GridObjectAbstract;

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

    const ORIENTATION_NORTH = GridObjectAbstract::ORIENTATION_NORTH;
    const ORIENTATION_SOUTH = GridObjectAbstract::ORIENTATION_SOUTH;
    const ORIENTATION_EAST = GridObjectAbstract::ORIENTATION_EAST;
    const ORIENTATION_WEST = GridObjectAbstract::ORIENTATION_WEST;

    const CONTEXT_OBJECTS = Game::CONTEXT_OBJECTS;
    const CONTEXT_OPTIONS = Game::CONTEXT_OPTIONS;
    const CONTEXT_SELF = Game::CONTEXT_SELF;

    const OBJECT_COLLECTOR = Game::OBJECT_COLLECTOR;
    const OBJECT_ROBOT = Game::OBJECT_ROBOT;
    const OBJECT_MINERAL = Game::OBJECT_MINERAL;
    const OBJECT_ROCKET = Game::OBJECT_ROCKET;
    const OBJECT_EXPLOSION = Game::OBJECT_EXPLOSION;
    const OBJECT_SHIELD = Game::OBJECT_SHIELD;
    const OBJECT_GAUNTLET = Game::OBJECT_GAUNTLET;
    const OBJECT_RAIL = Game::OBJECT_RAIL;

    protected $_objectTypes = [
        self::OBJECT_COLLECTOR,
        self::OBJECT_ROBOT,
        self::OBJECT_MINERAL,
        self::OBJECT_ROCKET,
        self::OBJECT_EXPLOSION,
        self::OBJECT_SHIELD,
        self::OBJECT_GAUNTLET,
        self::OBJECT_RAIL
    ];

    abstract function getOrder($env);

    public function serialize()
    {
        return '';
    }

    public function unserialize($string)
    {
    }
}