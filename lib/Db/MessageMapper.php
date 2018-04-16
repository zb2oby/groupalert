<?php

namespace OCA\GroupAlert\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\Mapper;


class MessageMapper extends Mapper {


    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'group_message');
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Message[]
     */
    public function findAll($limit = null, $offset = null) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' ORDER BY dt_message DESC';
        return $this->findEntities($sql);
    }

    /**
     * @param int $limit
     * @param null $offset
     * @return \OCP\AppFramework\Db\Entity
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    public function findLast($limit = 1, $offset = null) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' ORDER BY id DESC';
        return $this->findEntity($sql, $limit, $offset);
    }

    /**
     * @param int $id
     * @return \OCP\AppFramework\Db\Entity
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    public function findById($id) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?';
        return $this->findEntity($sql, [$id]);
    }

    /**
     * @param $folder
     * @return array
     */
    public function findByFolder($folder) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE folder = ?';
        return $this->findEntities($sql, [$folder]);
    }

    /**
     * @param int $id
     */
    public function deleteById($id) {
        $sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE id = ?';
        $this->execute($sql, [$id]);
    }
}
