<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 07/03/14
 * Time: 06:28
 */

namespace MineRobot\GameBundle\Models;


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

    public function __construct($data)
    {
        if ($data['game']) {
            $this->_initGame($data['game']);
        }
        if ($data['options']) {
            $this->_options = new Options($data['options']);
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
     * @return null
     */
    public function getName()
    {
        return $this->_name;
    }

} 