<?php namespace Skovachev\Lacore;

use App;

abstract class Repository {

    protected $paginate = false;
    protected $per_page = 50;

    public function enablePagination($per_page = 50)
    {
        $this->paginate = true;
        $this->per_page = $per_page;
    }

    abstract protected function getEntityClass();
    abstract protected function getValidationClass();

    public function all($query = null)
    {
        if (!is_null($query))
        {
            return $this->paginate ? $query->paginate($this->per_page) : $query->get();
        }
        else
        {
            $class = $this->getEntityClass();
            return $this->paginate ? $class::paginate($this->per_page) : $class::all();
        }
    }

    public function find($id)
    {
        $class = $this->getEntityClass();
        return $class::find($id);
    }

    public function add($data)
    {
        $class = $this->getEntityClass();
        $validation = $this->getValidationClass();

        if (!is_null($validation))
        {
            $validator = App::make($validation);
            $validator->validate($data);
        }

        return $class::create($data);
    }

    public function delete($id)
    {
        $entity = $this->find($id);
        if (!is_null($entity))
        {
            $entity->delete();
        }
    }

    public function edit($id, $data)
    {
        $entity = $this->find($id);
        if (!is_null($entity))
        {
            $validation = $this->getValidationClass();

            if (!is_null($validation))
            {
                $validator = App::make($validation);
                $validator->validateForUpdate($data);
            }

            $entity->fill($data);

            $entity->save();

            return true;
        }
        return false;
    }

}