<?php

namespace Litebase\Generated\Model;
use \Litebase\Generated\ObjectSerializer;

class StatementEffect
{
    /**
     * Possible values of this enum
     */
    public const ALLOW = 'allow';

    public const DENY = 'deny';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::ALLOW,
            self::DENY
        ];
    }
}


