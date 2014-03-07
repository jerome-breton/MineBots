<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:56
 */

namespace MineRobot\GameBundle\Models\GridObject;


abstract class GridObjectAbstract
{
    protected $_x = null;
    protected $_y = null;

    protected $_useOrientation = true;
    protected $_picture = '?';

    protected $_orientation = null;

    const ORIENTATION_NORTH = 'north';
    const ORIENTATION_SOUTH = 'south';
    const ORIENTATION_EAST = 'east';
    const ORIENTATION_WEST = 'west';

    public function __construct($data)
    {
        $this->_x = $data['x'];
        $this->_y = $data['y'];
        if ($this->_useOrientation) {
            $this->_orientation = $data['orientation'];
        }
    }

    /**
     * @return null
     */
    public function getX()
    {
        return $this->_x;
    }

    /**
     * @return null
     */
    public function getY()
    {
        return $this->_y;
    }

    /**
     * @return null
     */
    public function getOrientation()
    {
        return $this->_orientation;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->_picture;
    }

    protected function _rotateLeft()
    {
        switch ($this->_orientation) {
            case self::ORIENTATION_NORTH:
                $this->_orientation = self::ORIENTATION_WEST;
                break;
            case self::ORIENTATION_WEST:
                $this->_orientation = self::ORIENTATION_SOUTH;
                break;
            case self::ORIENTATION_SOUTH:
                $this->_orientation = self::ORIENTATION_EAST;
                break;
            default:
            case self::ORIENTATION_EAST:
                $this->_orientation = self::ORIENTATION_NORTH;
                break;
        }
    }

    protected function _rotateRight()
    {
        switch ($this->_orientation) {
            case self::ORIENTATION_NORTH:
                $this->_orientation = self::ORIENTATION_EAST;
                break;
            case self::ORIENTATION_EAST:
                $this->_orientation = self::ORIENTATION_SOUTH;
                break;
            case self::ORIENTATION_SOUTH:
                $this->_orientation = self::ORIENTATION_WEST;
                break;
            default:
            case self::ORIENTATION_WEST:
                $this->_orientation = self::ORIENTATION_NORTH;
                break;
        }
    }
} 