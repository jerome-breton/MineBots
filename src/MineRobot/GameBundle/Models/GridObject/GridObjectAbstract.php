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

    protected $_destroyed = false;
    protected $_objectsToCreate = array();

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

    public function run(){
        return $this;
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

    /**
     * @return boolean
     */
    public function isDestroyed()
    {
        return (boolean)$this->_destroyed;
    }

    /**
     * @param boolean $destroyed
     */
    public function setDestroyed($destroyed = true)
    {
        $this->_destroyed = $destroyed;

        return $this;
    }

    /**
     * @return array
     */
    public function getObjectsToCreate()
    {
        return $this->_objectsToCreate;
    }

    /**
     * @param array $objects
     */
    public function setObjectsToCreate($objects)
    {
        $this->_objectsToCreate = $objects;
    }

    /**
     * @param array $createdObjects
     */
    public function resetObjectsToCreate()
    {
        $this->setObjectsToCreate(array());
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

    protected function _forward($distance = 1){
        switch ($this->_orientation) {
            case self::ORIENTATION_NORTH:
                $this->_y = $this->getY() - $distance;
                break;
            case self::ORIENTATION_EAST:
                $this->_x = $this->getX() + $distance;
                break;
            case self::ORIENTATION_SOUTH:
                $this->_y = $this->getY() + $distance;
                break;
            case self::ORIENTATION_WEST:
                $this->_x = $this->getX() - $distance;
                break;
            default:
        }
    }

    protected function _destroy(){
        $this->setDestroyed();
    }

    protected function _createObject($type, $distance = 1, $side = 0, $rotation = 0, $variation = null){

        switch ($this->_orientation) {
            case self::ORIENTATION_NORTH:
                $y = $this->getY() - $distance;
                $x = $this->getX() + $side;
                break;
            case self::ORIENTATION_EAST:
                $x = $this->getX() + $distance;
                $y = $this->getY() - $side;
                break;
            case self::ORIENTATION_SOUTH:
                $y = $this->getY() + $distance;
                $x = $this->getX() - $side;
                break;
            case self::ORIENTATION_WEST:
                $x = $this->getX() - $distance;
                $y = $this->getY() + $side;
                break;
            default:
                $x = $this->getX();
                $y = $this->getY();
        }

        $data = array(
            'type' => $type,
            'x' => $x,
            'y' => $y,
            'orientation' => $this->getOrientation()
        );
        if(!is_null($variation)){
            $data['variation'] = $variation;
        }
        $this->_objectsToCreate[] = $data;

        return $this;
    }

    public function getType()
    {
        return strtolower(str_replace('MineRobot\\GameBundle\\Models\\GridObject\\', '', get_class($this)));
    }
} 