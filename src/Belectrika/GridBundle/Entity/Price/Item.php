<?php

namespace Belectrika\GridBundle\Entity\Price;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Belectrika\GridBundle\Entity\Price\Item
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Belectrika\GridBundle\Entity\Price\ItemRepository")
 */
class Item
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
     * @ORM\Column(name="title", type="string", length=1024)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var float $price
     *
     * @ORM\Column(name="price", type="float")
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var integer $amount
     *
     * @ORM\Column(name="amount", type="integer")
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @ORM\OneToMany(targetEntity="\Belectrika\GridBundle\Entity\Price\Item\Changelog", mappedBy="item")
     */
    private $changelogs;


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

    /**
     * Set price
     *
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }
    public function __construct()
    {
        $this->changelogs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add changelogs
     *
     * @param Belectrika\GridBundle\Entity\Price\Item\Changelog $changelogs
     */
    public function addChangelog(\Belectrika\GridBundle\Entity\Price\Item\Changelog $changelogs)
    {
        $this->changelogs[] = $changelogs;
    }

    /**
     * Get changelogs
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChangelogs()
    {
        return $this->changelogs;
    }
}