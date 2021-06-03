<?php

namespace App\Modules\DispatchSystem\Models;

/**
 * Class Record
 * This is used by any models that will have entries in storage.
 * It is a basic demonstration of saving a model, loading a model and loading multiple models.
 * It is not complete as it is outside the scope of this exercise.
 * @package App\Modules\DispatchSystem\Models
 */
class Record
{
    protected Storage $storage;
    protected ?int $id;

    /**
     * Record constructor.
     */
    public function __construct()
    {
        global $storage;
        $this->storage = $storage;
    }

    /**
     * Saves a model to storage.
     * @return bool|null
     */
    public function save(): ?bool
    {
        // TODO: Create query string from the model's properties once storage has been implemented
        if (isset($this->id)) {
            $result = $this->storage->query('update'); // Update row in storage if it has an id
        } else {
            $result = $this->storage->query('insert'); // Otherwise insert row in storage
        }

        // TODO: Assuming no result means no error for the moment until storage is implmented
        return (bool)$result;
    }

    /**
     * Finding a model's entry in storage (based on criteria provided), populates the models properties and returns it.
     * @param array $criteria
     * @return static|null
     */
    public static function load(array $criteria): ?self
    {
        global $storage;

        // TODO: Create query string from criteria once storage has been implemented
        $query = $criteria;

        $result = $storage->query($query);

        // TODO: Create new Model and set properties based on $result or return null if not found
        $model = null;

        return $model;
    }

    /**
     * Finding multiple models' entries in storage (based on criteria provided), populates the models' properties and returns them in an array.
     * @param array $criteria
     * @return array
     */
    public static function all(array $criteria): array
    {
        global $storage;

        // TODO: Create query string from criteria once storage has been implemented
        $query = $criteria;

        $result = $storage->query($query);

        // TODO: Create new Models and set their properties based on $result or return array if not found
        $models = array();

        return $models;
    }
}