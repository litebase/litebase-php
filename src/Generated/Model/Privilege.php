<?php

namespace Litebase\Generated\Model;
use \Litebase\Generated\ObjectSerializer;

class Privilege
{
    /**
     * Possible values of this enum
     */
    public const ACCESS_KEY_CREATE = 'access-key:create';

    public const ACCESS_KEY_DELETE = 'access-key:delete';

    public const ACCESS_KEY_LIST = 'access-key:list';

    public const ACCESS_KEY_READ = 'access-key:read';

    public const ACCESS_KEY_UPDATE = 'access-key:update';

    public const DATABASE_CREATE = 'database:create';

    public const DATABASE_LIST = 'database:list';

    public const DATABASE_SHOW = 'database:show';

    public const DATABASE_MANAGE = 'database:manage';

    public const DATABASE_BRANCH_CREATE = 'database:branch:create';

    public const DATABASE_BRANCH_LIST = 'database:branch:list';

    public const DATABASE_BRANCH_SHOW = 'database:branch:show';

    public const DATABASE_BRANCH_MANAGE = 'database:branch:manage';

    public const DATABASE_BACKUP = 'database:backup';

    public const DATABASE_RESTORE = 'database:restore';

    public const DATABASE_QUERY = 'database:query';

    public const DATABASE_ANALYZE = 'database:analyze';

    public const DATABASE_ATTACH = 'database:attach';

    public const DATABASE_ALTER_TABLE = 'database:alter_table';

    public const DATABASE_CREATE_INDEX = 'database:create_index';

    public const DATABASE_CREATE_TABLE = 'database:create_table';

    public const DATABASE_CREATE_TEMP_TABLE = 'database:create_temp_table';

    public const DATABASE_CREATE_TEMP_TRIGGER = 'database:create_temp_trigger';

    public const DATABASE_CREATE_TEMP_VIEW = 'database:create_temp_view';

    public const DATABASE_CREATE_TRIGGER = 'database:create_trigger';

    public const DATABASE_CREATE_VIEW = 'database:create_view';

    public const DATABASE_CREATE_VTABLE = 'database:create_vtable';

    public const DATABASE_DELETE = 'database:delete';

    public const DATABASE_DETACH = 'database:detach';

    public const DATABASE_DROP_INDEX = 'database:drop_index';

    public const DATABASE_DROP_TABLE = 'database:drop_table';

    public const DATABASE_DROP_TRIGGER = 'database:drop_trigger';

    public const DATABASE_DROP_VIEW = 'database:drop_view';

    public const DATABASE_FUNCTION = 'database:function';

    public const DATABASE_INSERT = 'database:insert';

    public const DATABASE_PRAGMA = 'database:pragma';

    public const DATABASE_READ = 'database:read';

    public const DATABASE_RECURSIVE = 'database:recursive';

    public const DATABASE_REINDEX = 'database:reindex';

    public const DATABASE_SAVEPOINT = 'database:savepoint';

    public const DATABASE_SELECT = 'database:select';

    public const DATABASE_TRANSACTION = 'database:transaction';

    public const DATABASE_UPDATE = 'database:update';

    public const TOKEN_CREATE = 'token:create';

    public const TOKEN_DELETE = 'token:delete';

    public const TOKEN_LIST = 'token:list';

    public const TOKEN_READ = 'token:read';

    public const TOKEN_UPDATE = 'token:update';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::ACCESS_KEY_CREATE,
            self::ACCESS_KEY_DELETE,
            self::ACCESS_KEY_LIST,
            self::ACCESS_KEY_READ,
            self::ACCESS_KEY_UPDATE,
            self::DATABASE_CREATE,
            self::DATABASE_LIST,
            self::DATABASE_SHOW,
            self::DATABASE_MANAGE,
            self::DATABASE_BRANCH_CREATE,
            self::DATABASE_BRANCH_LIST,
            self::DATABASE_BRANCH_SHOW,
            self::DATABASE_BRANCH_MANAGE,
            self::DATABASE_BACKUP,
            self::DATABASE_RESTORE,
            self::DATABASE_QUERY,
            self::DATABASE_ANALYZE,
            self::DATABASE_ATTACH,
            self::DATABASE_ALTER_TABLE,
            self::DATABASE_CREATE_INDEX,
            self::DATABASE_CREATE_TABLE,
            self::DATABASE_CREATE_TEMP_TABLE,
            self::DATABASE_CREATE_TEMP_TRIGGER,
            self::DATABASE_CREATE_TEMP_VIEW,
            self::DATABASE_CREATE_TRIGGER,
            self::DATABASE_CREATE_VIEW,
            self::DATABASE_CREATE_VTABLE,
            self::DATABASE_DELETE,
            self::DATABASE_DETACH,
            self::DATABASE_DROP_INDEX,
            self::DATABASE_DROP_TABLE,
            self::DATABASE_DROP_TRIGGER,
            self::DATABASE_DROP_VIEW,
            self::DATABASE_FUNCTION,
            self::DATABASE_INSERT,
            self::DATABASE_PRAGMA,
            self::DATABASE_READ,
            self::DATABASE_RECURSIVE,
            self::DATABASE_REINDEX,
            self::DATABASE_SAVEPOINT,
            self::DATABASE_SELECT,
            self::DATABASE_TRANSACTION,
            self::DATABASE_UPDATE,
            self::TOKEN_CREATE,
            self::TOKEN_DELETE,
            self::TOKEN_LIST,
            self::TOKEN_READ,
            self::TOKEN_UPDATE
        ];
    }
}


