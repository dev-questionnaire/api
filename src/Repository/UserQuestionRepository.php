<?php

namespace App\Repository;

use App\Entity\UserQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserQuestion[]    findAll()
 * @method UserQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserQuestion::class);
    }

    public function findOneByQuestionAndUser(string $questionSlug, int $userId): ?UserQuestion
    {
        $userQuestionList = $this->createQueryBuilder('uq')
            ->where('uq.questionSlug = :questionSlug')
            ->andWhere('uq.user = :userId')
            ->setParameter('questionSlug', $questionSlug)
            ->setParameter('userId', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        foreach ($userQuestionList as $userQuestion) {
            return $userQuestion;
        }

        return null;
    }

    /**
     * @return UserQuestion[]
     */
    public function findByExamAndUser(string $examSlug, int $userId): array
    {
        return $this->createQueryBuilder('uq')
            ->where('uq.examSlug = :examSlug')
            ->andWhere('uq.user = :userId')
            ->setParameter('examSlug', $examSlug)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}
