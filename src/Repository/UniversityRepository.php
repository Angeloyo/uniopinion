<?php

namespace App\Repository;

use App\Entity\University;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;


/**
 * @extends ServiceEntityRepository<University>
 *
 * @method University|null find($id, $lockMode = null, $lockVersion = null)
 * @method University|null findOneBy(array $criteria, array $orderBy = null)
 * @method University[]    findAll()
 * @method University[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UniversityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, University::class);
    }

    public function findOneBySlug(string $slug): ?University
    {
        $university = $this->findOneBy(['slug' => $slug]);

        if (!$university) {
            throw new EntityNotFoundException('La universidad '.$slug.' no existe');
        }

        return $university;
    }
}
