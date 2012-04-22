<?php

namespace Belectrika\GridBundle\Entity\Price;

use Doctrine\ORM\Mapping as ORM;

/**
 * Belectrika\GridBundle\Entity\Price\Group
 *
 * @ORM\Table(name="PriceGroup")
 * @ORM\Entity(repositoryClass="Belectrika\GridBundle\Entity\Price\GroupRepository")
 */
class Group
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="Belectrika\GridBundle\Entity\Price\Item", mappedBy="group")
     */
    private $items;


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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add items
     *
     * @param Belectrika\GridBundle\Entity\Price\Item $items
     */
    public function addItem(\Belectrika\GridBundle\Entity\Price\Item $items)
    {
        $this->items[] = $items;
    }

    /**
     * Get items
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }
}