<?php

namespace Ydle\NodesBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ydle\IhmBundle\Manager\BaseManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class SensorTypeManager extends BaseManager
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findAllByName()
    {
        return $this->getRepository()->findAllOrderedByName();
    }

    public function getRepository()
    {
        return $this->em->getRepository('YdleNodesBundle:SensorType');
    }

}
