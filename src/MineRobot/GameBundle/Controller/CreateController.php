<?php

namespace MineRobot\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CreateController extends Controller
{
    /**
     * @Route("/create", name="_create")
     * @Template()
     */
    public function indexAction()
    {

    }
}
