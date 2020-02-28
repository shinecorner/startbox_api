<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait SetsPropertyIfAvailable
{
    public function setIfAvailable(array $fields, array $data, bool $save = false)
    {
        foreach ($fields as $field) {
            if (Arr::has($data, $field)) {
                $this->{$field} = $data[$field];
            }
        }
        if ($save) {
            $this->save();
        }
    }
}