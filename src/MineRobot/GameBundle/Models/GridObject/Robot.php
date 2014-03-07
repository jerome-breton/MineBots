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


} 