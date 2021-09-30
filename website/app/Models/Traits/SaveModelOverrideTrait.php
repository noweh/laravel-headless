<?php


namespace App\Models\Traits;


trait SaveModelOverrideTrait
{

    /**
     * Save the model to the database converting Dates to DB datetime.
     *
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        foreach ($this->getAttributes() as $key => $value) {
            if (!empty($value)) {
                if (preg_match('/_at$/i', $key)) {
                    if (false === ($ts = strtotime($value))) {
                        $this->setAttribute($key, null);
                    }
                    $this->setAttribute($key, date('Y-m-d H:i:s', $ts));
                }
            }
        }

        return parent::save($options);
    }
}