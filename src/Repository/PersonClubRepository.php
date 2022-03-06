<?php

namespace App\Repository;


use App\Entity\PersonClub;
use App\Model\PersonClubRepositoryCriteria;
use App\Service\PersonClub\ClubMemberManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonClub|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonClub|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonClub[]    findAll()
 * @method PersonClub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonClub::class);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getTotalSalary($id)
    {
        $qb = $this->createQueryBuilder("c");
        return $qb
            ->select("SUM(c.salary) as sumOfSalaries")
            ->andWhere('c.club  = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()['sumOfSalaries'];
    }

    /**
     * @param $id
     * @return int|mixed|string|null
     */
    public function getCoach($id)
    {
        try {
            return $this->createQueryBuilder("c")
                ->andWhere('c.club  = :val')
                ->andWhere('c.type  =  :type')
                ->setParameter('val', $id)
                ->setParameter('type', clubMemberManager::TYPE_COACH)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException | NonUniqueResultException $e) {
        }
        return null;

    }

    /**
     * @return int|mixed|string
     */
    public function getFreePlayers()
    {
        return $this->createQueryBuilder('clubUser')
            ->join('player.user', 'user')
            ->innerJoin('user.clubUser', 'club')
            ->getQuery()
            ->getResult();

    }

    /**
     * @param $id
     * @return int|mixed|string
     */
    public function getTotalPlayers($id)
    {
        return $this->createQueryBuilder("c")
            ->andWhere('c.club  = :val')
            ->andWhere('c.type  = :type')
            ->setParameter('val', $id)
            ->setParameter('type', clubMemberManager::TYPE_PLAYER)
            ->select("Count(c.id)")
            ->getQuery()->getResult();
    }

    /**
     * @param PersonClub|null $club
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(?PersonClub $club)
    {
        $this->getEntityManager()->remove($club);
        $this->getEntityManager()->flush();
    }


    /**
     * @throws \Exception
     */
    public function findByCriteria(PersonClubRepositoryCriteria $criteria, $id): array
    {
        // Main queryBuilder - return the whole results
        $qb = $this->createQueryBuilder('usrs')
            ->where('usrs.club = :clubId')
            ->andWhere('usrs.type = :playerType')
            ->setParameter('clubId', $id)
            ->setParameter('playerType', ClubMemberManager::TYPE_PLAYER);

        // Check our Criteria object in order to apply filter
        if ($criteria->playerName !== null) {
            $qb->join('usrs.person', 'pers')
                ->andWhere('pers.name = :playerName')
                ->orWhere('pers.surname = :playerName')
                ->setParameter('playerName', trim($criteria->playerName));
        }

        $qb->setMaxResults($criteria->itemsPerPage);
        $qb->setFirstResult(($criteria->page - 1) * $criteria->itemsPerPage);

        $paginator = new Paginator($qb->getQuery());
        return [
            'total' => count($paginator),
            'itemsPerPage' => $criteria->itemsPerPage,
            'page' => $criteria->page,
            'data' => iterator_to_array($paginator->getIterator())
        ];
    }
}
