<?php

namespace Marello\Bundle\OrderBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Oro\Bundle\FormBundle\Form\DataTransformer\EntityToIdTransformer;

use Marello\Bundle\TaxBundle\Entity\TaxCode;

class TaxCodeToCodeTransformer extends EntityToIdTransformer
{
    /**
     * ProductToSkuTransformer constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct(
            $em,
            TaxCode::class,
            'code',
            function (EntityRepository $repository, $code) {
                $qb = $repository->createQueryBuilder('tc');

                return $qb
                    ->where(
                        $qb->expr()->like(
                            'tc.code',
                            $qb->expr()->literal($code)
                        )
                    );
            }
        );
    }
}
