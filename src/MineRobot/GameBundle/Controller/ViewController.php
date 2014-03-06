<?php

namespace MineRobot\GameBundle\Controller;

use MineRobot\GameBundle\Helpers\GameManager;
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
        $rootDir = $this->get('kernel')->getRootDir();

        $gameManager = new GameManager();

        return array('games' => $gameManager->getGamesList($rootDir));
    }

    /**
     * @Route("/view/{game}", name="_view")
     * @Template()
     */
    public function rungameAction($game)
    {
        return array('game' => $game);
    }
}
