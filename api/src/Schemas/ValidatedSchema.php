<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

trait ValidatedSchema
{
    public static function from(mixed $data): self|false
    {
        $rc = new ReflectionClass(self);
        $obj = new self();

        try {
            for ($rc->getProperties() as $rp) {
                $name = $rp->getName();

                // Let PHP do the type checking for us, including null, union types, everyhting :)
                $obj->$name = isset($data[$name]) ? $data[$name] : null;
            }
    
            return $obj;
        } catch(Exception) {
            return false;
        }
    }
}
