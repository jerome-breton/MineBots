<?php
/**
 * Created by ECLIpse.
 * User: David
 * Date: 11/03/14
 * Time: 21:55
 */

namespace MineRobot\GameBundle\Pilots\AI\Dpo;


use MineRobot\GameBundle\Pilots\PilotAbstract;

class DpoV0 extends PilotAbstract
{

    protected $_DpoVar = 0;
    protected $_message = 'lllm';
    protected $_name = 'DPO V0 - AIe';

    public function getName()
    {
      return $this->_name;
    }
    public function getMessage()
    {
      return $this->_message;
    }

    public function serialize()
    {
        return serialize(array(
        		'message' => $this->_message,
            'DpoVar' => $this->_DpoVar
        ));
    }

    public function unserialize($string)
    {
        $data = unserialize($string);

        $this->_DpoVar = (isset($data['DpoVar']))?$data['DpoVar']:1;
        $this->_message = (isset($data['message']))?$data['message']:'';
    }

    public function getOrder($env)
    {

      $where = self::CONTEXT_SELF;

      $return = self::ORDER_STAY_REPAIR;

      switch ($this->_DpoVar) {
        case 1:
          $return = self::ORDER_MOVE_FORWARD;
          $this->_message = 'Move Forward';
          break;
        case 2:
          $return = self::ORDER_TURN_LEFT;
          $this->_message = 'ORDER_TURN_LEFT ';

          break;
        case 3:
          $return = self::ORDER_TURN_RIGHT;
          $this->_message = 'ORDER_TURN_RIGHT';

        case 4:
          $return = self::ORDER_ATTACK_ROCKET;
          $this->_message = 'ORDER_ATTACK_ROCKET';

          break;

        case 5:
          $return = self::ORDER_ATTACK_RAIL;
          $this->_message = 'ORDER_ATTACK_RAIL';

          break;

        case 6:
          $return = self::ORDER_ATTACK_GAUNTLET;
          $this->_message = 'ORDER_ATTACK_GAUNTLET';

          break;

        case 7:
          $return = self::ORDER_STAY_SHIELD;
          $this->_message = 'ORDER_STAY_SHIELD';

          break;

        case 8:
          $return = self::ORDER_STAY_REPAIR;
          $this->_message = 'ORDER_STAY_REPAIR';

          break;

        case 9:
          $return = self::ORDER_STAY_SCAN;
          $this->_message = 'ORDER_STAY_SCAN';

          break;

        default:
          if ($this->_DpoVar > 15) {
          $this->_DpoVar = 1;
        }
        $return = self::ORDER_ATTACK_ROCKET;
        $this->_message = 'HAHAHAHAHHAH FIRRRRERERRRERRERERE : ' . $this->_DpoVar;
        /*case 1:
        case 4:
        case 8:
        case 12:
          $return = self::ORDER_ATTACK_ROCKET;
          $this->_message = 'ORDER_ATTACK_ROCKET';
          break;

        default:
          if ($this->_DpoVar > 15) {
            $this->_DpoVar = 1;
          }*/
      }

      $this->_DpoVar++;
      return $return;
    }
}
