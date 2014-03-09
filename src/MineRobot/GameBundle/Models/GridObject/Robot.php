<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 07:03
 */

namespace MineRobot\GameBundle\Models\GridObject;

use MineRobot\GameBundle\Pilots\PilotAbstract;

class Robot extends GridObjectAbstract
{
    protected $_useOrientation = true;
    protected $_useVariation = true;

    protected $_variations = ['black', 'blue', 'cyan', 'gray', 'green', 'pink', 'purple', 'red', 'white', 'yellow'];

    protected $_base_picture = 'robot';

    protected $_pilot = null;

    protected $_life = 1;
    protected $_score = 0;
    protected $_minerals = 0;

    protected $_healingTurns = 0;

    protected $_shieldEnabled = false;

    public function __construct($data, $options)
    {
        parent::__construct($data, $options);

        if (isset($data['life'])) {
            $this->_life = $data['life'];
        }
        if (isset($data['healingTurns'])) {
            $this->_healingTurns = $data['healingTurns'];
        }
        if (isset($data['score'])) {
            $this->_score = $data['score'];
        }
        if (isset($data['minerals'])) {
            $this->_minerals = $data['minerals'];
        }
        if (isset($data['pilot'])) {
            $this->_pilot = $data['pilot'];
        }
    }

    public function getSleepArray()
    {
        $array = parent::getSleepArray();

        $array['life'] = $this->_life;
        $array['healingTurns'] = $this->_healingTurns;
        $array['score'] = $this->_score;
        $array['minerals'] = $this->_minerals;
        $array['pilot'] = $this->_pilot;

        return $array;
    }

    public function damage($amount)
    {
        $this->_life -= $amount;
        if ($this->_life <= 0) {
            $this->setDestroyed();
        }
    }

    public function _heal()
    {
        $this->_life += $this->_options['robots']['heal']
            * (1 + $this->_healingTurns * $this->_options['robots']['healTurnBonus']);

        if ($this->_life > $this->_options['robots']['life']) {
            $this->_life = $this->_options['robots']['life'];
        }
    }

    public function run()
    {
        $pilot = unserialize($this->_pilot);

        $order = $pilot->getOrder('@todo');

        $this->_pilot = serialize($pilot);

        if ($order != PilotAbstract::ORDER_STAY_REPAIR) {
            $this->_healingTurns = 0;
        }

        switch ($order) {
            case PilotAbstract::ORDER_MOVE_FORWARD:
                $this->_forward();
                break;
            case PilotAbstract::ORDER_ATTACK_ROCKET:
                $this->_rocket();
                break;
            case PilotAbstract::ORDER_ATTACK_GAUNTLET:
                $this->_gauntlet();
                break;
            case PilotAbstract::ORDER_ATTACK_RAIL:
                $this->_rail();
                break;
            case PilotAbstract::ORDER_STAY_SHIELD:
                $this->_shield();
                break;
            case PilotAbstract::ORDER_STAY_REPAIR:
                $this->_heal();
                break;
            case PilotAbstract::ORDER_STAY_SCAN:
                //@TODO
                break;
            case PilotAbstract::ORDER_TURN_LEFT:
                $this->_rotateLeft();
                break;
            case PilotAbstract::ORDER_TURN_RIGHT:
                $this->_rotateRight();
                break;
            default:
                $this->setDestroyed();
        }

        return parent::run();
    }

    protected function _shield()
    {
        $this->_shieldEnabled = true;
        $this->_createObject('shield', 0);
    }

    protected function _gauntlet()
    {
        $this->_createObject('gauntlet');
    }

    protected function _rocket()
    {
        $this->_createObject('rocket');
    }

    protected function _rail()
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->_createObject('rail', $i, 0, 0, ($i == 1) ? 'source' : 'suite');
        }
    }

    public function setDestroyed($destroyed = true)
    {
        if ($destroyed) {
            $this->_createObject('explosion', 0);
            for ($x = $this->_minerals; $x > 0; $x--) {
                $this->_createObject('mineral', rand(-3, 3), rand(-3, 3));
            }
        }
        return parent::setDestroyed($destroyed);
    }

    public function atCollector()
    {
        $this->_score += $this->_minerals * $this->_options['minerals']['points'];
        $this->_minerals = 0;
    }

    /**
     * @param Mineral $mineral
     */
    public function collect($mineral)
    {
        $this->_minerals++;
        $mineral->setDestroyed();
    }

    /**
     * @param $robotInCell
     */
    public function cellTaken($robotInCell)
    {
        if ($this->getX() == $robotInCell->getX() && $this->getY() == $robotInCell->getY()) {
            $this->_resetPosition();
        }
    }

    public function hasShield()
    {
        return $this->_shieldEnabled;
    }
}
