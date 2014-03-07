<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:28
 */

namespace MineRobot\GameBundle\Models;

use MineRobot\GameBundle\Models\GridObject\GridObjectAbstract;

class Game
{

    /**
     * Name of the Game
     * @var string
     */
    protected $_name = null;

    /**
     * Current iteration
     * @var int
     */
    protected $_iteration = null;

    /**
     * Current status
     * @var string
     */
    protected $_status = null;
    const STATUS_NEW = 'new';
    const STATUS_RUNNING = 'running';
    const STATUS_FINISHED = 'finished';

    /**
     * Game options
     * @var Options
     */
    protected $_options = null;

    protected $_grid = array();

    protected $_objectsInGrid = ['collector','robot','mineral','rocket','explosion','gauntlet','rail'];

    public function __construct($data)
    {
        if ($data['game']) {
            $this->_initGame($data['game']);
        }
        if ($data['options']) {
            $this->_options = new Options($data['options']);
        }
        foreach($this->_objectsInGrid as $objectType){
            if($data[$objectType]){
                $objectClassName = 'MineRobot\\GameBundle\\Models\\GridObject\\'.ucwords($objectType);
                foreach($data[$objectType] as $objectData){
                    $object = new $objectClassName($objectData);
                    $this->_writeGrid($object);
                }
            }
        }
    }

    protected function _initGame($gameData)
    {
        if ($gameData['name']) {
            $this->_name = $gameData['name'];
        }
        if ($gameData['iteration']) {
            $this->_iteration = $gameData['iteration'];
        }
        if ($gameData['status']) {
            $this->_status = $gameData['status'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return \MineRobot\GameBundle\Models\Options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    public function readGrid($x, $y){
        if(!isset($this->_grid[$x])){
            return null;
        }
        if(!isset($this->_grid[$x][$y])){
            return null;
        }
        return $this->_grid[$x][$y];
    }

    protected function _writeGrid(GridObjectAbstract $gridObject){
        $x = $gridObject->getX();
        $y = $gridObject->getY();
        if(!isset($this->_grid[$x])){
            $this->_grid[$x] = array();
        }
        if(!isset($this->_grid[$x][$y])){
            $this->_grid[$x][$y] = array();
        }
        $this->_grid[$x][$y][] = $gridObject;
        return $this;
    }

    public function getGrid(){
        return $this->_grid;
    }
} 