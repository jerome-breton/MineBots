<?php
/**
 * Created by ECLIpse.
 * User: David
 * Date: 11/03/14
 * Time: 21:55
 */

namespace MineRobot\GameBundle\Pilots\AI\Dpo;


use MineRobot\GameBundle\Pilots\PilotAbstract;

class DpoV1 extends PilotAbstract
{

    protected $_DpoVar = 0;
    protected $_message = 'lllm';
    protected $_name = 'DPO V1 - AIe';

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

          $return = self::ORDER_STAY_SHIELD;
          $this->_message = 'ORDER_STAY_SHIELD';

      return $return;
    }
}
