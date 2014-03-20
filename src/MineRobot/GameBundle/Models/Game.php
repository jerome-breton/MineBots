<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:28
 */

namespace MineRobot\GameBundle\Models;

use MineRobot\GameBundle\Models\GridObject\Collector;
use MineRobot\GameBundle\Models\GridObject\Explosion;
use MineRobot\GameBundle\Models\GridObject\Gauntlet;
use MineRobot\GameBundle\Models\GridObject\GridObjectAbstract;
use MineRobot\GameBundle\Models\GridObject\Mineral;
use MineRobot\GameBundle\Models\GridObject\Rail;
use MineRobot\GameBundle\Models\GridObject\Robot;
use MineRobot\GameBundle\Models\GridObject\Rocket;

class Game
{
    const CONTEXT_OBJECTS = 'objects_in_sight';
    const CONTEXT_OPTIONS = 'options';
    const CONTEXT_SELF = 'self';

    const OBJECT_COLLECTOR = 'collector';
    const OBJECT_ROBOT = 'robot';
    const OBJECT_MINERAL = 'mineral';
    const OBJECT_ROCKET = 'rocket';
    const OBJECT_EXPLOSION = 'explosion';
    const OBJECT_SHIELD = 'shield';
    const OBJECT_GAUNTLET = 'gauntlet';
    const OBJECT_RAIL = 'rail';

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

    public $options = null;

    protected $_grid = array();
    public $gridModifications = array('add' => array(), 'move' => array(), 'del' => array(), 'rotate' => array(), 'robots' => array());

    protected $_objectsInGrid = [
        self::OBJECT_COLLECTOR,
        self::OBJECT_ROBOT,
        self::OBJECT_MINERAL,
        self::OBJECT_ROCKET,
        self::OBJECT_EXPLOSION,
        self::OBJECT_SHIELD,
        self::OBJECT_GAUNTLET,
        self::OBJECT_RAIL
    ];

    public function __construct($data)
    {
        if ($data['game']) {
            $this->_initGame($data['game']);
        }
        if ($data['options']) {
            $this->options = $data['options'];
        }
        foreach ($this->_objectsInGrid as $objectType) {
            if (isset($data[$objectType])) {
                $objectClassName = $this->getClassByType($objectType);
                foreach ($data[$objectType] as $objectData) {
                    $object = new $objectClassName($objectData, $this->options);
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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
        //Unset previous position in grid
        $hash = $gridObject->getHash();
        $ox = $gridObject->getOriginalX();
        $oy = $gridObject->getOriginalY();
        $oo = $gridObject->getOriginalOrientation();
        $new = true;
        if (isset($this->_grid[$ox][$oy][$hash])) {
            $new = false;
            unset($this->_grid[$ox][$oy][$hash]);
        }

        //Check if position is not out of bounds
        $x = $gridObject->getX();
        $y = $gridObject->getY();
        $jsonData = array('x' => $x, 'y' => $y, 'img' => $gridObject->getPicture());
        if ($x < 0 || $x >= $this->options['grid']['width']
            || $y < 0 || $y >= $this->options['grid']['height']
        ) {
            $gridObject->setDestroyed();
            $this->gridModifications['del'][$hash] = $jsonData;
        }

        //Init grid arrays if not done yet
        if (!isset($this->_grid[$x])) {
            $this->_grid[$x] = array();
        }
        if (!isset($this->_grid[$x][$y])) {
            $this->_grid[$x][$y] = array();
        }

        //Handle collisions
        if ($handleCollisions) {
            if ($ox == $x && $oy == $y) {
                //Test for not moving objects
                $this->_handleCollisions($x, $y, $gridObject);
            } elseif ($ox == $x) {
                //Test collisions for each cell along deplacement
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

        //Write objects in grid
        $this->_grid[$x][$y][$hash] = $gridObject;
        foreach ($this->_grid[$x][$y] as $hash => $object) {

            //Remove destroyed objects (mainly due to collisions)
            if ($object->isDestroyed()) {
                unset($this->_grid[$x][$y][$hash]);
                $this->gridModifications['del'][$hash] = $jsonData;
            }

            //Adds subobjects to grid using recursive call
            $objectsToCreate = $object->getObjectsToCreate();
            $object->resetObjectsToCreate();
            if (!empty($objectsToCreate)) {
                foreach ($objectsToCreate as $objectToCreate) {
                    $class = $this->getClassByType($objectToCreate['type']);
                    $this->_writeGrid(new $class($objectToCreate, $this->options), $handleCollisions);
                }
            }

        }

        if ($handleCollisions && !$gridObject->isDestroyed()) {
            if ($new) {
                $this->gridModifications['add'][$hash] = $jsonData;
            } else if ($ox != $x || $oy != $y) {
                $this->gridModifications['move'][$hash] = $jsonData;
            }
            if ($oo != $gridObject->getOrientation()) {
                $this->gridModifications['rotate'][$hash] = $jsonData;
            }
        }
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
     * @param Explosion $explosion
     * @param Robot     $robot
     */
    protected function _whenExplosionReachesRobot($explosion, $robot)
    {
        if (!$robot->hasShield()) {
            $robot->damage($this->options['weapons'][self::OBJECT_ROCKET]);
        }
    }

    /**
     * @param Explosion $explosion
     * @param Rocket    $rocket
     */
    protected function _whenExplosionReachesRocket($explosion, $rocket)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param Gauntlet $gauntlet
     * @param Rocket   $rocket
     */
    protected function _whenGauntletReachesRocket($gauntlet, $rocket)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param Rocket $rocketA
     * @param Rocket $rocketB
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
     * @param Rocket    $rocket
     * @param Collector $collector
     */
    protected function _whenRocketReachesCollector($rocket, $collector)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param Rocket $rocket
     * @param Robot  $robot
     */
    protected function _whenRocketReachesRobot($rocket, $robot)
    {
        //Robot will take damage from explosion, not the rocket itself
        $rocket->setDestroyed();
    }

    /**
     * @param Gauntlet $gauntlet
     * @param Robot    $robot
     */
    protected function _whenGauntletReachesRobot($gauntlet, $robot)
    {
        if (!$robot->hasShield()) {
            $robot->damage($this->options['weapons'][self::OBJECT_GAUNTLET]);
        }
    }

    /**
     * @param Rail  $rail
     * @param Robot $robot
     */
    protected function _whenRailReachesRobot($rail, $robot)
    {
        if (!$robot->hasShield()) {
            $robot->damage($this->options['weapons'][self::OBJECT_RAIL]);
        }
    }

    /**
     * @param Rail   $rail
     * @param Rocket $rocket
     */
    protected function _whenRailReachesRocket($rail, $rocket)
    {
        $rocket->setDestroyed();
    }

    /**
     * @param Robot     $robot
     * @param Collector $collector
     */
    protected function _whenRobotReachesCollector($robot, $collector)
    {
        $robot->atCollector();
    }

    /**
     * @param Robot   $robot
     * @param Mineral $mineral
     */
    protected function _whenRobotReachesMineral($robot, $mineral)
    {
        $robot->collect($mineral);
    }

    /**
     * @param Robot $robotIncoming
     * @param Robot $robotInCell
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
        foreach ($this->getGrid() as $column) {
            foreach ($column as $objects) {
                /** @var GridObjectAbstract $object */
                foreach ($objects as $object) {
                    $context = $object->getNeedContext() ? $this->_getContext($object) : null;
                    $timeStart = microtime(true);
//                    try {
                    $object->run($context);
//                    } catch (Exception $e) {
//                        $object->setDestroyed();
//                    }
                    $timeStop = microtime(true);
                    $celerity = (double)round(($timeStop - $timeStart) * pow(10, 15));

                    $inGridObjects[] = array('object' => $object, 'celerity' => $celerity);
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

    protected function _getContext(GridObjectAbstract $object)
    {
        if ($object instanceof Robot && $object->isScanning()) {
            $scanDistance = $this->options['sight']['scan'];
        } else {
            $scanDistance = $this->options['sight']['base'];
        }
        $context = array(
            self::CONTEXT_OBJECTS => $this->_getObjectsInSight(
                    $object->getX(), $object->getY(), $scanDistance, spl_object_hash($object)
                ),
            self::CONTEXT_OPTIONS => $this->options,
            self::CONTEXT_SELF => array(
                'x' => $object->getX(),
                'y' => $object->getY(),
                'orientation' => $object->getOrientation(),
            )
        );
        if ($object instanceof Robot) {
            $context[self::CONTEXT_SELF]['life'] = $object->getLife();
            $context[self::CONTEXT_SELF]['minerals'] = $object->getMinerals();
            $context[self::CONTEXT_SELF]['score'] = $object->getScore();
            $context[self::CONTEXT_SELF]['healingTurns'] = $object->getHealingTurns();
            $this->gridModifications['robots'][$object->getHash()] = array(
            	'name' => $object->getHash(),
            	'life' => $context[self::CONTEXT_SELF]['life'],
            	'minerals' => $context[self::CONTEXT_SELF]['minerals'],
            	'score' => $context[self::CONTEXT_SELF]['score'],
            	'healingTurns' => $context[self::CONTEXT_SELF]['healingTurns']
            );
        }

        return $context;
    }

    protected function _getObjectsInSight($cx, $cy, $d, $h)
    {
        $objectsInSight = array();
        for ($x = $cx - $d; $x <= $cx + $d; $x++) {
            for ($y = $cy - $d; $y <= $cy + $d; $y++) {
                if (isset($this->_grid[$x]) && isset($this->_grid[$x][$y]) && !empty($this->_grid[$x][$y])) {
                    /** @var GridObjectAbstract $object */
                    foreach ($this->_grid[$x][$y] as $hash => $object) {
                        if ($hash == $h) {
                            continue;
                        }
                        $objectsInSight[$hash] = array(
                            'type' => $object->getType(),
                            'x' => $object->getX(),
                            'y' => $object->getY(),
                            'orientation' => $object->getOrientation()
                        );
                    }
                    $objectsInSight = array_merge($objectsInSight, $this->_grid[$x][$y]);
                }
            }
        }
        return $objectsInSight;
    }
}
