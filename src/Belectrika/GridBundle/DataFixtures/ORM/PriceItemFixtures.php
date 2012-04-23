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
        $item = new Item();
        $item->setTitle('Item-with-changes');
        $item->setPrice(100);
        $item->setAmount(100);
        $item->setGroup($this->getReference('group-1'));
        $this->setReference('item-with-changes', $item);
        $manager->persist($item);

        for ($i=1; $i<10; $i++) {
            $item = new Item();
            $item->setTitle("Item {$i}");
            $item->setPrice($i / 10);
            $item->setAmount($i * 2 + 1);
            $groupId = rand(1,4);
            $item->setGroup($this->getReference("group-{$groupId}"));
            $manager->persist($item);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}