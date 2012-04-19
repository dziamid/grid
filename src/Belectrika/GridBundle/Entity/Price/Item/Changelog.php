<?php

namespace Belectrika\GridBundle\Entity\Price\Item;

use Doctrine\ORM\Mapping as ORM;

/**
 * Belectrika\GridBundle\Entity\Price\Item\Changelog
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Belectrika\GridBundle\Entity\Price\Item\ChangelogRepository")
 */
class Changelog
{
    const TYPE_CREATE = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $type
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="\Belectrika\GridBundle\Entity\Price\Item", inversedBy="changelogs")
     */
    private $item;


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
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set item
     *
     * @param Belectrika\GridBundle\Entity\Price\Item $item
     */
    public function setItem(\Belectrika\GridBundle\Entity\Price\Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get item
     *
     * @return Belectrika\GridBundle\Entity\Price\Item
     */
    public function getItem()
    {
        return $this->item;
    }
}