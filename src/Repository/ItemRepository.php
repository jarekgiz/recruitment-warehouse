<?php
// src/AppBundle/Repository/ItemRepository.php
namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param $amount
     * @return Item[]
     */
    public function findAllGreaterThanAmount($amount): array
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.amount > :amount')
            ->setParameter('amount', $amount)
            ->orderBy('i.amount', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    /**
     *
     * @return Item[]
     */
    public function findAllAvailable(): array
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.amount > 0')
            ->orderBy('i.id', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    /**
     *
     * @return Item[]
     */
    public function findAllNotAvailable(): array
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.amount = 0')
            ->orderBy('i.amount', 'ASC')
            ->getQuery();

        return $qb->execute();
    }
}