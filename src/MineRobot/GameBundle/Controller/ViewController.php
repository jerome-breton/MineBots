<?php

namespace MineRobot\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ViewController extends Controller
{
    /**
     * @Route("/view/selectgame", name="_view_selectgame")
     * @Template()
     */
    public function selectgameAction()
    {

    }
}
