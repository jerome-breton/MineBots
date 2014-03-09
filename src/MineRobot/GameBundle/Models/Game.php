<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:28
 */

namespace MineRobot\GameBundle\Models;

use MineRobot\GameBundle\Models\GridObject\GridObjectAbstract;
use MineRobot\GameBundle\Models\GridObject\Mineral;
use MineRobot\GameBundle\Models\GridObject\Robot;

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

    protected $_objectsInGrid = ['collector', 'robot', 'mineral', 'rocket', 'explosion', 'shield', 'gauntlet', 'rail'];

    public function __construct($data)
    {
        if ($data['game']) {
            $this->_initGame($data['game']);
        }
        if ($data['options']) {
            $this->_options = new Options($data['options']);
        }
        foreach ($this->_objectsInGrid as $objectType) {
            if (isset($data[$objectType])) {
                $objectClassName = $this->getClassByType($objectType);
                foreach ($data[$objectType] as $objectData) {
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

    public function readGrid($x, $y)
    {
        if (!isset($this->_grid[$x])) {
            return null;
        }
        if (!isset($this->_grid[$x][$y])) {
            return null;
        }
        return $this->_grid[$x][$y];
    }

    protected function _writeGrid(GridObjectAbstract $gridObject, $handleCollisions = false)
    {
        $hash = spl_object_hash($gridObject);
        $ox = $gridObject->getOriginalX();
        $oy = $gridObject->getOriginalY();
        if (isset($this->_grid[$ox][$oy][$hash])) {
            unset($this->_grid[$ox][$oy][$hash]);
        }
        $x = $gridObject->getX();
        $y = $gridObject->getY();
        $gridSize = $this->getOptions()->getGrid();
        if ($x < 0 || $x >= $gridSize['width'] || $y < 0 || $y >= $gridSize['height']) {
            $gridObject->setDestroyed();
            return $this;
        }
        if (!isset($this->_grid[$x])) {
            $this->_grid[$x] = array();
        }
        if (!isset($this->_grid[$x][$y])) {
            $this->_grid[$x][$y] = array();
        }
        if ($handleCollisions) {
            //Test collisions for each cell along deplacement
            if ($ox == $x) {
                $sy = ($oy < $y) ? 1 : -1;
                for ($dy = $oy + $sy; $sy * $dy <= $sy * $y; $dy += $sy) {
                    $this->_handleCollisions($x, $dy, $gridObject);
                }
            } elseif ($oy == $y) {
                $sx = ($ox < $x) ? 1 : -1;
                for ($dx = $ox + $sx; $sx * $dx <= $sx * $x; $dx += $sx) {
                    $this->_handleCollisions($dx, $y, $gridObject);
                }
            } else {
                //Diagonal move not allowed for now.
                $gridObject->setDestroyed();
            }
        }

        $this->_grid[$x][$y][$hash] = $gridObject;
        foreach ($this->_grid[$x][$y] as $hash => $object) {
            if ($object->isDestroyed()) {
                unset($this->_grid[$x][$y][$hash]);
            }

            $objectsToCreate = $object->getObjectsToCreate();
            if (!empty($objectsToCreate)) {
                foreach ($objectsToCreate as $objectToCreate) {
                    $class = $this->getClassByType($objectToCreate['type']);
                    $this->_writeGrid(new $class($objectToCreate), $handleCollisions);
                }
                $object->resetObjectsToCreate();
            }

        }
        gc_collect_cycles();
        return $this;
    }

    /**
     * @param array $inCellObjects
     * @param GridObjectAbstract $incomingObject
     */
    protected function _handleCollisions($x, $y, $incomingObject)
    {
        if (!isset($this->_grid[$x][$y])) {
            return;
        }
        $incomingObject->setCreatePosition($x, $y);

        /** @var GridObjectAbstract $inCellObject */
        foreach ($this->_grid[$x][$y] as $inCellObject) {
            if (!$inCellObject->isDestroyed() && !$incomingObject->isDestroyed()) {
                $this->_handleCollision($inCellObject, $incomingObject);
            }
        }

        $incomingObject->resetCreatePosition();
    }

    protected function _handleCollision($inCellObject, $incomingObject)
    {
        $inCellObjectType = $inCellObject->getType();
        $incomingObjectType = $incomingObject->getType();
        $functionReach = '_when' . ucwords($incomingObjectType) . 'Reaches' . ucwords($inCellObjectType);
        $functionReverse = '_when' . ucwords($inCellObjectType) . 'Reaches' . ucwords($incomingObjectType);

        if (is_callable(array($this, $functionReach))) {
            $this->$functionReach($incomingObject, $inCellObject);
        } elseif (is_callable(array($this, $functionReverse))) {
            $this->$functionReverse($inCellObject, $incomingObject);
        }
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $shield
     */
    protected function _whenExplosionReachesRobot($explosion, $robot)
    {
        $robot->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $shield
     */
    protected function _whenExplosionReachesRocket($explosion, $rocket)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $shield
     */
    protected function _whenRocketReachesShield($rocket, $shield)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenGauntletReachesRocket($gauntlet, $rocket)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocketA
     * @param GridObjectAbstract $rocketB
     */
    protected function _whenRocketReachesRocket($rocketA, $rocketB)
    {
        //Two rockets in the same orientation can't collide
        //Solves celerity conflicts when two rockets are following each other
        if ($rocketA->getOrientation() != $rocketB->getOrientation()) {
            $rocketA->setDestroyed();
            $rocketB->setDestroyed();
        }
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenRocketReachesCollector($rocket, $collector)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenRocketReachesRobot($rocket, $robot)
    {
        $rocket->setDestroyed();
        $robot->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenGauntletReachesRobot($gauntlet, $robot)
    {
        $robot->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenGauntletReachesShield($gauntlet, $shield)
    {
        $gauntlet->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenRailReachesShield($rail, $shield)
    {
        $rail->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenRailReachesRobot($rail, $robot)
    {
        $robot->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param GridObjectAbstract $robot
     */
    protected function _whenRailReachesRocket($rail, $rocket)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param GridObjectAbstract $rocket
     * @param Robot              $robot
     */
    protected function _whenRobotReachesCollector($robot, $collector)
    {
        $robot->atCollector();
    }

    /**
     * @param Mineral $mineral
     * @param Robot   $robot
     */
    protected function _whenRobotReachesMineral($robot, $mineral)
    {
        $robot->collect($mineral);
    }

    /**
     * @param Mineral $mineral
     * @param Robot   $robot
     */
    protected function _whenRobotReachesRobot($robotIncoming, $robotInCell)
    {
        $robotIncoming->cellTaken($robotInCell);
    }

    public function getGrid()
    {
        return $this->_grid;
    }

    public function run()
    {
        $inGridObjects = array();
        foreach ($this->getGrid() as $x => $column) {
            foreach ($column as $y => $objects) {

                /** @var GridObjectAbstract $object */
                foreach ($objects as $o => $object) {
                    $timeStart = microtime(true);
                    try {
                        $object->run();
                    } catch (Exception $e) {
                        $object->setDestroyed();
                    }
                    $timeStop = microtime(true);
                    $celerity = (double)round(($timeStop - $timeStart) * pow(10, 15));

                    $inGridObjects[] = array('object' => $object, 'celerity' => $celerity++);
                    /*$objectsToCreate = $object->getObjectsToCreate();
                    if (!empty($objectsToCreate)) {
                        foreach ($objectsToCreate as $objectToCreate) {
                            $class = $this->getClassByType($objectToCreate['type']);
                            $inGridObjects[] = array('object' => new $class($objectToCreate), 'celerity' => $celerity++);
                        }
                        $object->resetObjectsToCreate();
                    }*/
                }
            }
        }
        //$this->_grid = array();

        usort($inGridObjects, function ($a, $b) {
            if ($b['celerity'] == $a['celerity']) {
                return 0;
            }
            return ($b['celerity'] < $a['celerity']) ? 1 : -1;
        });
        foreach ($inGridObjects as $inGridObject) {
            $this->_writeGrid($inGridObject['object'], true);
        }

        $this->_iteration++;
    }

    /**
     * @return int
     */
    public function getIteration()
    {
        return $this->_iteration;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }


    public function getTypeByClass($object)
    {
        return strtolower(str_replace('MineRobot\\GameBundle\\Models\\GridObject\\', '', get_class($object)));
    }

    public function getClassByType($type)
    {
        return 'MineRobot\\GameBundle\\Models\\GridObject\\' . ucwords($type);
    }
} 