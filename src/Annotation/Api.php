<?php

namespace App\Annotation;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Annotation class for @Api().
 *
 * @Annotation
 * Target({"PROPERTY", "METHOD"})
 */
class Api extends Groups
{
    /**
     * Property can be write from API request.
     */
    public bool $write;

    public function __construct(array $data)
    {
        parent::__construct(isset($data['value']) ? $data : ['value' => $data['groups'] ?? '']);
        $this->write = (bool) ($data['write'] ?? false);
    }
}
