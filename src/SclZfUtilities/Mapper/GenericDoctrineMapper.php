<?php

namespace SclZfUtilities\Mapper;

use Doctrine\ORM\EntityManager;
use SclZfUtilities\Doctrine\FlushLock;

/**
 * Basic mapper class for doctrine storage.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class GenericDoctrineMapper
{
    /**
     * The Doctrine EntityManager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The FlushLock class.
     *
     * @var FlushLock
     */
    protected $flushLock;

    /**
     * The name of the entity that this mapper is working with.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Inject required objects.
     *
     * @param  EntityManager $entityManager
     * @param  FlushLock     $flushLock
     * @param  string        $entityName
     */
    public function __construct(
        EntityManager $entityManager,
        FlushLock $flushLock,
        $entityName = null
    ) {
        $this->entityManager = $entityManager;
        $this->flushLock     = $flushLock;
        $this->entityName    = (string) $entityName;
    }

    /**
     * Set the name of the entity that this class deals with.
     *
     * @param  string $entityName
     * @return self
     */
    public function setEntityName($entityName)
    {
        $this->entityName = (string) $entityName;

        return $this;
    }

    /**
     * Creates a new instance of the entity.
     *
     * @return object
     */
    public function create()
    {
        return new $this->entityName;
    }

    /**
     * Persists to the Order to storage.
     *
     * @param  object $order
     * @return boolean
     */
    public function save($order)
    {
        $this->flushLock->lock();

        $this->entityManager->persist($order);

        return $this->flushLock->unlock();
    }

    /**
     * Loads a given order from the database.
     *
     * @param  mixed $id
     * @return object|null
     */
    public function findById($id)
    {
        return $this->entityManager->find($this->entityName, $id);
    }

    /**
     * Returns all orders from the database.
     *
     * @return object[]|null
     */
    public function fetchAll()
    {
        return $this->entityManager->getRepository($this->entityName)->findAll();
    }

    /**
     * Does a search by criteria.
     *
     * @param  array $criteria
     * @return object[]|null
     */
    public function fetchBy(array $criteria)
    {
        return $this->entityManager->getRepository($this->entityName)->findBy($criteria);
    }

    /**
     * Deletes the order from the storage.
     *
     * @param  object $entity
     * @return boolean
     */
    public function delete($entity)
    {
        $this->flushLock->lock();

        $this->entityManager->remove($entity);

        return $this->flushLock->unlock();
    }
}
