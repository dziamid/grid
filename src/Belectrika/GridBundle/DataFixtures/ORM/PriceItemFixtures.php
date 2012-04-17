<?php
namespace LunchTime\DeliveryBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Belectrika\GridBundle\Entity\Price\Item;

class PriceItemFixtures extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i<10; $i++) {
            $item = new Item();
            $item->setTitle("Item {$i}");
            $item->setPrice($i / 10);
            $item->setAmount($i * 2 + 1);
            $manager->persist($item);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}