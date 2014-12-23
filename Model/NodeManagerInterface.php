<?php

/*
* This file is part of the Sonata project.
*
* (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ydle\NodesBundle\Model;

use Ydle\CoreBundle\Model\ManagerInterface;
use Ydle\CoreBundle\Model\PageableManagerInterface;

interface NodeManagerInterface extends ManagerInterface, PageableManagerInterface
{
}