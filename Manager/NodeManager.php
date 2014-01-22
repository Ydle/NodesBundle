<?php

namespace Ydle\NodesBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ydle\IhmBundle\Manager\BaseManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class NodeManager extends BaseManager
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findAllByName()
    {
        return $this->getRepository()->findAll();
    }

    public function getRepository()
    {
        return $this->em->getRepository('YdleNodesBundle:Node');
    }

    public function countSensorsByRoom($room)
    {
        return $this->getRepository()->createNamedQuery('Node.countSensorsByRoom')->setParameter(1, $room)->getSingleScalarResult();
    }

    public function findSensorsByRoom($room)
    {
        return $this->getRepository()->findBy(array('room' => $room));
    }

}
