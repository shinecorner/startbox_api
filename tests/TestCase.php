<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function spew()
    {
        $this->withoutExceptionhandling();
    }

    public function assertHasPagination($response, $values = [])
    {
        $response->assertJsonStructure([
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ]
        ]);

        $response->assertJsonStructure([
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ]
        ]);

        if(count($values) == 0) return;

        foreach($values as $key => $value) {
            $response->assertJsonPath("meta.$key", $value);
        }
    }
}
