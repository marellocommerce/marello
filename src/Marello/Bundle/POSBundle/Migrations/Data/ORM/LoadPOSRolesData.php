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
            $userRole = new Role(self::ROLE_USER);
            $userRole->setLabel('POS User');
            $manager->persist($userRole);
        }

        $this->updateDescriptionForUser($userRole, $manager);

        $userRoleAdmin = $manager
            ->getRepository(Role::class)
            ->findOneBy(['role' => self::ROLE_ADMIN]);
        if (!$userRoleAdmin) {
            $userRoleAdmin = new Role(self::ROLE_ADMIN);
            $userRoleAdmin->setLabel('POS Administrator');
            $manager->persist($userRoleAdmin);
        }
        $this->updateDescriptionForAdmin($userRoleAdmin, $manager);

        $manager->flush();
    }

    private function updateDescriptionForAdmin($role, $manager): void
    {
        $description = 'This role is a default user role for a POS Administrator and
         needs to be assigned to the user when a Marello POS+ administrator user is created.';

        if ($role) {
            $role->setExtendDescription($description);
            $manager->persist($role);
        }
    }

    private function updateDescriptionForUser($role, $manager): void
    {
        $description = 'This role is a default user role for a POS User and
         needs to be assigned to the user when a Marello POS+ user is created.';

        if ($role) {
            $role->setExtendDescription($description);
            $manager->persist($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return '1.1';
    }
}
