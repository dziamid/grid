<?php
namespace LunchTime\DeliveryBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Belectrika\GridBundle\Entity\Price\Group;

class PriceGroupFixtures extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $parent1 = new Group();
        $parent1->setTitle("Parent Group 1");
        $manager->persist($parent1);

        $child1 = new Group();
        $child1->setTitle("Child Group 1");
        $child1->setParent($parent1);
        $manager->persist($child1);

        for ($i = 1; $i <= 4; $i++) {
            $entity = new Group();
            $entity->setTitle("Group {$i}");
            $manager->persist($entity);
            $this->setReference("group-{$i}", $entity);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}