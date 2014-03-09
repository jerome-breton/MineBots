<?php
/**
 * Created by PhpStorm.
 * User: Jerome
 * Date: 06/03/14
 * Time: 23:24
 */

namespace MineRobot\GameBundle\Services;

use MineRobot\GameBundle\Models\Game;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class Manager
{

    protected $_jsonEncoder = null;
    protected $_serializer = null;

    public function getGamesList($rootDir)
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
            $games[$file->getBaseName('.json')] = $this->decodeJsonFile($file);;
        }

        return $games;
    }

    public function loadGame($rootDir, $gameFileName, $deleteFile = true)
    {
        $finder = new Finder();

        $finder
            ->files()
            ->name($gameFileName . '.json')
            ->depth(0)
            ->in($rootDir . DIRECTORY_SEPARATOR . 'games');

        $files = array();


        /** @var SplFileInfo $file */
        foreach ($finder as $file) {}

        if(!isset($file)){
            throw new FileNotFoundException('Unable to find '.$gameFileName.'.json file');
        }

        $game = new Game($this->decodeJsonFile($file));

        if ($deleteFile) {

            $fs = new Filesystem();
            $fs->remove($file->getRealPath());
        }

        return $game;
    }

    public function saveGame($rootDir, Game $game, $gameFileName)
    {
        $gameData = array(
            'game' => array(
                'name' => $game->getName(),
                'iteration' => $game->getIteration(),
                'status' => $game->getStatus(),
            ),
            'options' => $game->options
        );
        foreach ($game->getGrid() as $x => $column) {
            foreach ($column as $y => $objects) {
                foreach ($objects as $object) {
                    $type = $object->getType();
                    if (!isset($gameData[$type])) {
                        $gameData[$type] = array();
                    }
                    $gameData[$type][] = $object->getSleepArray();
                }
            }
        }
        $json = $this->getSerializer()->serialize($gameData, 'json');

        file_put_contents($rootDir . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . $gameFileName . '.json',
            $json);

        return $game;
    }

    /**
     * @return JsonEncoder
     */
    public function getJsonEncoder()
    {
        if (is_null($this->_jsonEncoder)) {
            $this->_jsonEncoder = new JsonEncoder();
        }
        return $this->_jsonEncoder;
    }

    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        if (is_null($this->_serializer)) {
            $this->_serializer = new Serializer(array(), array($this->getJsonEncoder()));
        }
        return $this->_serializer;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return array
     */
    private function decodeJsonFile($file)
    {
        return $this->getSerializer()->decode(
            $file->getContents(), 'json', array('json_decode_associative' => true)
        );
    }
} 