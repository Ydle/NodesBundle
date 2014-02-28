<?php
/*
  This file is part of Ydle.

    Ydle is free software: you can redistribute it and/or modify
    it under the terms of the GNU  Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ydle is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU  Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Ydle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Ydle\NodesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ydle\NodesBundle\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

class NodesController extends Controller
{
    public function indexAction(Request $request)
    {
        $nodes = $this->get("ydle.nodes.manager")->findAllByName();
        
        return $this->render('YdleNodesBundle:Nodes:index.html.twig', array(
            'mainpage' => 'nodes',
            'nodes' => $nodes
            )
        );
    }
    
    /**
     * Display a form to create or edit a node.
     * 
     * @param Request $request
     */
    public function formAction(Request $request)
    {
        $node = new Node();
        // Manage edition mode
        $this->currentNode = $request->get('node');
        if($this->currentNode){
            $node = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'));
        }      

        $form = $this->createForm("node_form", $node);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($node);
            $em->flush();
            $message = 'Node added successfully';
            if($node->getId()){
                $message = 'Node modify successfully';
            }
            $this->get('session')->getFlashBag()->add('notice', $message);
            $this->get('ydle.logger')->log('info', $message, 'hub');
            return $this->redirect($this->generateUrl('nodes'));
        }
        
        return $this->render('YdleNodesBundle:Nodes:form.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    
    /**
     * Delete a node
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function deleteAction(Request $request)
    {
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'));
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->remove($object);
        $em->flush();
        $this->get('ydle.logger')->log('info', 'node removed', 'hub');
        $this->get('session')->getFlashBag()->add('notice', 'Node removed');
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    
    /**
     * Reset a node, sending an http request to the master 
     * and a 433 mhz request then
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function resetAction(Request $request)
    {
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'));
        
        // Check if the required options are set in the parameters.yml file
        $masterAddr = $this->container->getParameter('master_address');
        $masterCode = $this->container->getParameter('master_id');        
        if(empty($masterAddr) || empty($masterAddr)){
            $this->get('session')->getFlashBag()->add('error', 'Node removed');
            $this->redirect($this->generateUrl('nodes'));
            die;
        }
        
        $address = $masterAddr;
        $address .= ':8888/node/reset?target='.$object->getCode().'&sender=';
        $address .= $masterCode;
        
        $ch = curl_init($address);
        curl_exec($ch);
        curl_close($ch);
        
        $this->get('ydle.logger')->log('info', 'Initialization signal sent to node #'.$object->getCode());
        $this->get('session')->getFlashBag()->add('notice', 'Reset envoyé');
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    
    /**
     * Create a link with a node, sending an http request to the master 
     * and a 433 mhz request then
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function linkAction(Request $request)
    {
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'));
        
        $address = $this->container->getParameter('master_address');
        $address .= ':8888/node/link?target='.$object->getCode().'&sender=';
        $address .= $this->container->getParameter('master_id');
        
        $ch = curl_init($address);
        curl_exec($ch);
        curl_close($ch);
        
        $this->get('ydle.logger')->log('info', 'Initialization signal sent to node #'.$object->getCode());
        $this->get('session')->getFlashBag()->add('notice', 'Link action envoyée');
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    /**
    * Manage activation of a node
    * 
    * @param Request $request
    */
    public function activationAction(Request $request)
    {
        $isActive = $request->get('active');
        $message = $isActive?'Node activated':'Node deactivated';
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'))->setIsActive($isActive);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->persist($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', $message);
        
        if($isActive){
           $this->get('ydle.logger')->log('info', 'Node #'.$object->getCode().' activated', 'hub');
        } else {
           $this->get('ydle.logger')->log('info', 'Node #'.$object->getCode().' deactivated', 'hub');
        }
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    /**
    * Manage initialization of a node
    * 
    * @param Request $request
    */
    public function initializeAction(Request $request)
    {
        $isActive = $request->get('active');
        $message = $isActive?'Node activated':'Node deactivated';
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'))->setIsActive($isActive);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->persist($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', $message);
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    public function sensorsAction(Request $request)
    {
        $msg = 'ok';
        $sensors = array();
        if(!$node = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'))){
            $msg = 'ko';
        } else {
            foreach($node->getTypes() as $type)
            {
                $sensors[] = $type->getId();
            }
        }
        return new JsonResponse(array('msgReturn' => $msg, 'data' => $sensors, 'nodeId' => $node->getId(), 'roomId' => $node->getRoom()->getId()));
    }
}
