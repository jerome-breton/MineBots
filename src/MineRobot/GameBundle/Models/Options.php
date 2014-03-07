<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:39
 */

namespace MineRobot\GameBundle\Models;


class Options
{

    protected $_options = array();

    public function __construct($data)
    {
        $this->_options = $data;
    }
} 