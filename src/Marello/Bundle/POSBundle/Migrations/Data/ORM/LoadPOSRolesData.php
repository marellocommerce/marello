<?php

namespace Marello\Bundle\POSBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\Bundle\UserBundle\Entity\Role;
use Oro\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;

class LoadPOSRolesData extends AbstractFixture implements
    VersionedFixtureInterface
{
    public const ROLE_USER = 'ROLE_POS_USER';
    public const ROLE_ADMIN = 'ROLE_POS_ADMIN';

    public function load(ObjectManager $manager): void
    {
        $userRole = $manager
            ->getRepository(Role::class)
            ->findOneBy(['role' => self::ROLE_USER]);
        if (!$userRole) {
            $roleUser = new Role(self::ROLE_USER);
            $roleUser->setLabel('POS User');
            $manager->persist($roleUser);
        }

        $this->updateDescriptionForUser($userRole, $manager, $roleUser);

        $userRoleAdmin = $manager
            ->getRepository(Role::class)
            ->findOneBy(['role' => self::ROLE_ADMIN]);
        if (!$userRoleAdmin) {
            $roleAdmin = new Role(self::ROLE_ADMIN);
            $roleAdmin->setLabel('POS Administrator');
            $manager->persist($roleAdmin);
        }
        $this->updateDescriptionForAdmin($userRoleAdmin, $manager, $roleAdmin);
        $manager->flush();
    }

    private function updateDescriptionForAdmin($userRoleAdmin, $manager, $roleAdmin = null): void
    {
        $description = 'This role is a default user role for a POS Administrator and
         needs to be assigned to the user when a Marello POS+ administrator user is created.';

        $userRoleAdmin->setExtendDescription($description);
        if ($roleAdmin) {
            $roleAdmin->setExtendDescription($description);
            $manager->persist($roleAdmin);
        }

        $manager->persist($userRoleAdmin);
    }

    private function updateDescriptionForUser($userRole, $manager, $roleUser = null): void
    {
        $description = 'This role is a default user role for a POS User and
         needs to be assigned to the user when a Marello POS+ user is created.';

        $userRole->setExtendDescription($description);
        if ($roleUser) {
            $roleUser->setExtendDescription($description);
            $manager->persist($roleUser);
        }

        $manager->persist($userRole);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return '1.1';
    }
}
