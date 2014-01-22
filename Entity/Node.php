<?php

namespace Ydle\NodesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Ydle\RoomBundle\Entity\Room;
use Ydle\NodesBundle\Entity\SensorType;

/**
 * Node
 *
 * @ORM\Table(name="node")
 * @ORM\Entity(repositoryClass="Ydle\NodesBundle\Repository\NodeRepository")
 * @ORM\NamedQueries({
 *     @ORM\NamedQuery(name="Node.countSensorsByRoom", query="SELECT count(s) FROM __CLASS__ n JOIN n.types s WHERE n.room = ?1 ")
 * })
 */
class Node
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;   

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true, unique=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 255,
     *      minMessage = "Minimum is 0",
     *      maxMessage = "Maximum code is 255"
     * )
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Ydle\RoomBundle\Entity\Room", inversedBy="nodes")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;

    /**
     * @var integer
     *
     * @ORM\ManyToMany(targetEntity="SensorType", cascade={"persist"})  
     * @ORM\JoinTable(name="node_sensor",
     *                joinColumns={@ORM\JoinColumn(name="node_id", referencedColumnName="id")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="sensortype_id", referencedColumnName="id")}
     *)
     */
    private $types;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;
    
    public function __construct()
    {
        // Si vous aviez déjà un constructeur, ajoutez juste cette ligne :
        $this->types = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Node
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Node
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Node
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Node
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
    /**
     * Add Type
     * 
     * @param \Ydle\NodesBundle\Entity\SensorType $type
     */
    public function addType(SensorType $type)
    {
        $this->types[] = $type;
    }
   
     /**
     * Get types
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTypes()
    {        
        return $this->types;
    } 
    
    /**
     * Remove remote
     */
    public function removeType($key){
        if($key!=null && array_key_exists($key, $this->types)){
            unset($this->types[$key]);
        }
        unset($this->types);

        return $this;
    }
   
     /**
     * Set types
     *
     */
    public function setTypes($types)
    {        
        $this->types = $types;
    
        return $this;
    } 

    /**
     * Set typeId
     *
     * @param integer $typeId
     * @return Room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    
        return $this;
    }

    /**
     * Get typeId
     *
     * @return Ydle\RoomBundle\Entity\Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }
}