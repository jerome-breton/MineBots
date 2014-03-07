<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:53
 */

namespace MineRobot\GameBundle\Models\GridObject;


class Rail extends GridObjectAbstract{
    protected $_useOrientation = true;
    protected $_useVariation = true;

    protected $_variations = ['suite','source'];

    protected $_base_picture = 'rail';
} 