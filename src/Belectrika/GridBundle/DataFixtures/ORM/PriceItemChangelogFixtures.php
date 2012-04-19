<?php
namespace LunchTime\DeliveryBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Belectrika\GridBundle\Entity\Price\Item\Changelog;

class PriceItemChangelogFixtures extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $item = $this->getReference('item-with-changes');

        $log = new Changelog();
        $log->setType(Changelog::TYPE_UPDATE);
        $log->setItem($item);
        $manager->persist($log);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}