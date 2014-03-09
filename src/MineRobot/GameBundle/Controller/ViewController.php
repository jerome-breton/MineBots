<?php

namespace MineRobot\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// these import the "@Route" and "@Template" annotations

class ViewController extends Controller
{
    /**
     * @Route("/view/selectgame", name="_view_selectgame")
     * @Template()
     */
    public function selectgameAction()
    {
        $rootDir = $this->get('kernel')->getRootDir();

        return array('games' => $this->get('minerobot_game.manager')->getGamesList($rootDir));
    }

    /**
     * @Route("/view/{gameFileName}", name="_view")
     * @Template()
     */
    public function viewgameAction($gameFileName)
    {
        $rootDir = $this->get('kernel')->getRootDir();

        $game = $this->get('minerobot_game.manager')->loadGame($rootDir, $gameFileName, false);

        return array('game' => $game, 'filename' => $gameFileName);
    }

    /**
     * @Route("/view/run/{gameFileName}", name="_view_run")
     * @Template()
     */
    public function rungameAction($gameFileName)
    {
        $rootDir = $this->get('kernel')->getRootDir();

        $game = $this->get('minerobot_game.manager')->loadGame($rootDir, $gameFileName, false);

        $game->run();

        $this->get('minerobot_game.manager')->saveGame($rootDir, $game, $gameFileName);

        return array('game' => $game);
    }
}
