<?php

namespace App\Repositories;

use App\Contracts\Repositories\SessionRepositoryInterface;
use App\Models\Session;

class SessionRepository extends AbstractRepository implements SessionRepositoryInterface
{
    public function __construct(Session $model)
    {
        $this->model = $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @param array $fields
     * @throws RelationNotFoundException
     */
    public function updateAfter($object, $fields)
    {
        parent::updateAfter($object, $fields);

        $this->updateRelatedElements($object, $fields, 'themes_id');

        if (array_key_exists('questionnaires', $fields)) {
            $this->updateCoursesAndQuestionnaires($object, $fields, 'questionnaires');
        }
        if (array_key_exists('courses', $fields)) {
            $this->updateCoursesAndQuestionnaires($object, $fields, 'courses');
        }
    }

    /**
     * sync the $object model many to many relation specified by $relatedFieldName
     * @param Model $object
     * @param array $fields
     * @param string $relatedFieldName
     * @throws RelationNotFoundException
     */
    private function updateCoursesAndQuestionnaires(Model $object, array $fields, $relatedFieldName)
    {
        $relatedElements = $fields[$relatedFieldName];

        $relatedElementsWithPosition = [];
        foreach ($relatedElements as $relatedElement) {
            $relatedElementsWithPosition[$relatedElement['id']] =
                ['position_in_session' => $relatedElement['position']];
        }

        $relatedObjectRelation = $object->$relatedFieldName();
        try {
            $relatedObjectRelation->sync($relatedElementsWithPosition);
        } catch (\Exception $e) {
            throw new RelationNotFoundException(
                'No query result for model [' . get_class($object->$relatedFieldName()->getRelated()) .
                '] ' . implode(' or ', array_keys($relatedElementsWithPosition))
            );
        }
    }
}
