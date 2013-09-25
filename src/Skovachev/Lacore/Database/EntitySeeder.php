<?php namespace Skovachev\Lacore\Database;

use Seeder;

abstract class EntitySeeder extends Seeder {

    abstract protected function getEntityClass();
    abstract protected function getEntityData();

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getEntityData() as $data) {
            $entity = $this->findEntity($data['id']);
            if (is_null($entity))
            {
                $entity = $this->create($data);
                $this->setupRelationships($entity);
            }
            else
            {
                $this->update($data, $entity);
            }
            $entity->save();
        }
    }

    protected function findEntity($id)
    {
        $class = $this->getEntityClass();
        return $class::find($id);
    }

    protected function create($data)
    {
        $class = $this->getEntityClass();
        return new $class($data);
    }

    protected function update($data, &$entity)
    {
        return $entity->fill($data);
    }

    protected function setupRelationships(&$entity)
    {

    }
}