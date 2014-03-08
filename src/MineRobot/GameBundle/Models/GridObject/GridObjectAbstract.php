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
    protected $_useVariation = false;

    protected $_picture = 'nothing';
    protected $_base_picture = 'nothing';

    protected $_orientation = null;
    protected $_variation = null;

    protected $_variations = array();

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
        if ($this->_useVariation){
            if(isset($data['variation'])){
                $this->setVariation($data['variation']);
            }else{
                $this->setVariation(0);
            }
        }
    }

    public function getSleepArray(){
        $array = array(
            'x' => $this->getX(),
            'y' => $this->getY()
        );
        if ($this->_useOrientation) {
            $array['orientation'] = $this->_orientation;
        }
        if ($this->_useVariation){
            $array['variation'] = $this->_variation;
        }

        return $array;
    }

    public function setVariation($variation){
        if(is_numeric($variation)){
            $this->_variation = $this->_variations[$variation % count($this->_variations)];
        }elseif(in_array($variation, $this->_variations)){
            $this->_variation = $variation;
        }else{
            throw new \OutOfBoundsException('Invalid variation');
        }

        $this->_picture = $this->_base_picture . '/'.$this->_variation;

        return $this;
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
        if ($this->_useOrientation) {
            return $this->_picture . '/' . $this->getOrientation() . '.gif';
        }else{
            return $this->_picture . '.gif';
        }
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