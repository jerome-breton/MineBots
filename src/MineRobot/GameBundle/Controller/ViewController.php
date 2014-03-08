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

        return array('games' => GameManager::getGamesList($rootDir));
    }

    /**
     * @Route("/view/{gameFileName}", name="_view")
     * @Template()
     */
    public function rungameAction($gameFileName)
    {
        $rootDir = $this->get('kernel')->getRootDir();

        $game = GameManager::loadGame($rootDir, $gameFileName);

        $game->run();

        GameManager::saveGame($rootDir, $game, $gameFileName);

        return array('game' => $game);
    }
}
