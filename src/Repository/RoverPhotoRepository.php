<?php

namespace App\Repository;

use App\Entity\RoverPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoverPhoto>
 *
 * @method RoverPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoverPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoverPhoto[]    findAll()
 * @method RoverPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoverPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoverPhoto::class);
    }

    public function save(RoverPhoto $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @param RoverPhoto[] $entities */
    public function saveMany(mixed $entities, bool $flush = false): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RoverPhoto $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeMany(mixed $entities, bool $flush = false): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function wipe(): void
    {
        $this->removeMany($this->findAll(), true);
    }

    /** @return RoverPhoto[] */
    public function getPhotosWithinRange(
        string $start  = NULL,
        string $end    = NULL,
        string $rover  = NULL,
        string $camera = NULL
    ): mixed
    {
        $query = $this->createQueryBuilder('r');

        if($start)
        {
            // subtract 1 day
            $start = (new \DateTime($start))->modify("-1 day")->format("Y-m-d");
            $query->andWhere('r.earthDate > :start')
                ->setParameter('start', $start);
        }
        if($end)
        {
            // add 1 day
            $end = (new \DateTime($end))->modify("+1 day")->format("Y-m-d");
            $query->andWhere('r.earthDate < :end')
                ->setParameter('end', $end);
        }
        if($rover)
        {
            $query->andWhere('r.roverName = :rover')
                ->setParameter('rover', $rover);
        }
        if($camera)
        {
            $query->andWhere('r.cameraName = :camera')
                ->setParameter('camera', $camera);
        }
        return $query->getQuery()->getResult();
    }

}
