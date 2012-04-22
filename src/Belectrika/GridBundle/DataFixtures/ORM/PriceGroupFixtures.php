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
        for ($i = 1; $i < 4; $i++) {
            $entity = new Group();
            $entity->setTitle("Group {$i}");
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }

}