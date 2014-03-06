<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 06/03/14
 * Time: 23:24
 */

namespace MineRobot\GameBundle\Helpers;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class GameManager {

    public function getGamesList($rootDir){

        $finder = new Finder();

        $iterator = $finder
            ->files()
            ->name('*.json')
            ->depth(0)
            ->in($rootDir . DIRECTORY_SEPARATOR . 'games');

        $files = array();

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $files[$file->getRealpath()] = $file->getBaseName('.json');
        }

        return $files;
    }
} 