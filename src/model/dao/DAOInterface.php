<?php
namespace src\model\dao;

/**
 * Interface DAOInterface : Methodes CRUD
 */
interface DAOInterface
{
    /**
     * @return mixed
     */
    public function create($object);

    /**
     * @return mixed
     */
    public function update($object);

    /**
     * @return mixed
     */
    public function delete($object);

    /**
     * @param $pk : primary key
     * @return mixed
     */
    public function find($pk);

    /**
     * @return mixed
     */
    public function findAll();

    /**
     * @param $searchKey
     * @return mixed
     */
    public function search($searchKey);

}
?>