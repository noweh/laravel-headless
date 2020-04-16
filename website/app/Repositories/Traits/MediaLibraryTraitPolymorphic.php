<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\RelationNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\MediaLibrary;
use App\Models\Mediable;

trait MediaLibraryTraitPolymorphic
{
    /**
     * @param $object
     * @param $fields
     * @throws RelationNotFoundException
     * @throws ValidationException
     */
    public function updateAfterMediaLibraryTraitPolymorphic($object, $fields)
    {
        $this->updateMedia($object, $fields);
    }

    /**
     * @param $object
     * @param $fields
     * @return bool
     * @throws ValidationException
     * @throws RelationNotFoundException
     */
    private function updateMedia($object, $fields)
    {
        if (array_key_exists('medias', $fields)) {
            // Detach all medias from the entity
            $object->medias()->sync([]);

            if (isset($fields['medias'])) {
                foreach ($fields['medias'] as $mediaLibraryId => $data) {
                    if (!is_numeric($mediaLibraryId)) {
                        throw new ValidationException(
                            null,
                            'media_library_id integer format is expected, but another format is received'
                        );
                    }
                    if (!MediaLibrary::find($mediaLibraryId)) {
                        throw new RelationNotFoundException(
                            'No query result for model [' . MediaLibrary::class .
                            '] ' . $mediaLibraryId
                        );
                    }
                    foreach ($data['formats'] as $role => $ratioData) {
                        if (array_key_exists($role, MediaLibrary::formatDefinition())) {
                            $mediaData = $this->fillData($ratioData);
                            $mediaData['ratio'] = $role;
                            if (isset($data['position'])) {
                                $mediaData['position'] = $data['position'];
                            }

                            $object->medias()->attach($mediaLibraryId, $mediaData);
                        } else {
                            throw new ValidationException(
                                'Format ' . $role . ' does not exists for model [' . MediaLibrary::class . ']'
                            );
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Workaround to the Laravel sync that doesn't check the mass asignements constraints
     * @param $data
     * @return array
     */
    private function fillData($data)
    {
        $instance = new Mediable;
        return $instance->fill($data)->toArray();
    }
}
