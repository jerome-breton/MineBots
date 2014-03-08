<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 06/03/14
 * Time: 23:24
 */

namespace MineRobot\GameBundle\Helpers;

use MineRobot\GameBundle\Models\Game;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class GameManager
{

    static protected $_jsonEncoder = null;
    static protected $_serializer = null;

    static public function getGamesList($rootDir)
    {

        $finder = new Finder();

        $finder
            ->files()
            ->name('*.json')
            ->depth(0)
            ->in($rootDir . DIRECTORY_SEPARATOR . 'games');

        $games = array();

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $games[$file->getBaseName('.json')] = self::decodeJsonFile($file);;
        }

        return $games;
    }

    static public function loadGame($rootDir, $gameFileName, $deleteFile = true)
    {
        $finder = new Finder();

        $finder
            ->files()
            ->name($gameFileName . '.json')
            ->depth(0)
            ->in($rootDir . DIRECTORY_SEPARATOR . 'games');

        $files = array();


        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
        }
        $game = new Game(self::decodeJsonFile($file));

        if ($deleteFile) {

            $fs = new Filesystem();
            $fs->remove($file->getRealPath());
        }

        return $game;
    }

    static public function saveGame($rootDir, Game $game, $gameFileName)
    {
        $gameData = array(
            'game' => array(
                'name' => $game->getName(),
                'iteration' => $game->getIteration(),
                'status' => $game->getStatus(),
            ),
            'options' => array(
                'weapons' => $game->getOptions()->getWeapons(),
                'robots' => $game->getOptions()->getRobots(),
                'minerals' => $game->getOptions()->getMinerals(),
                'grid' => $game->getOptions()->getGrid(),
            )
        );
        foreach ($game->getGrid() as $x => $column) {
            foreach ($column as $y => $objects) {
                foreach ($objects as $object) {
                    $type = self::getTypeByClass($object);
                    if (!isset($gameData[$type])) {
                        $gameData[$type] = array();
                    }
                    $gameData[$type][] = $object->getSleepArray();
                }
            }
        }
        $json = self::getSerializer()->serialize($gameData, 'json');

        file_put_contents($rootDir . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . $gameFileName . '.json',
            $json);

        return $game;
    }

    static public function getTypeByClass($object)
    {
        return strtolower(str_replace('MineRobot\\GameBundle\\Models\\GridObject\\', '', get_class($object)));
    }

    static public function getClassByType($type)
    {
        return 'MineRobot\\GameBundle\\Models\\GridObject\\' . ucwords($type);
    }

    /**
     * @return JsonEncoder
     */
    public static function getJsonEncoder()
    {
        if (is_null(self::$_jsonEncoder)) {
            self::$_jsonEncoder = new JsonEncoder();
        }
        return self::$_jsonEncoder;
    }

    /**
     * @return Serializer
     */
    public static function getSerializer()
    {
        if (is_null(self::$_serializer)) {
            self::$_serializer = new Serializer(array(), array(self::getJsonEncoder()));
        }
        return self::$_serializer;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return array
     */
    private static function decodeJsonFile($file)
    {
        return self::getSerializer()->decode(
            $file->getContents(), 'json', array('json_decode_associative' => true)
        );
    }
} 