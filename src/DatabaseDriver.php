<?php
namespace BeatSwitch\Lock\Integrations\Laravel;

use BeatSwitch\Lock\Callers\Caller;
use BeatSwitch\Lock\Drivers\Driver;
use BeatSwitch\Lock\Permissions\Permission;
use BeatSwitch\Lock\Permissions\PermissionFactory;
use BeatSwitch\Lock\Roles\Role;
use Illuminate\Database\ConnectionInterface;

class DatabaseDriver implements Driver
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The name of the database table for storing permissions
     *
     * @var string
     */
    protected $table;

    /**
     * @param \Illuminate\Database\ConnectionInterface $connection
     * @param string $table
     */
    public function __construct(ConnectionInterface $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Returns all the permissions for a caller
     *
     * @param \BeatSwitch\Lock\Callers\Caller $caller
     * @return \BeatSwitch\Lock\Permissions\Permission[]
     */
    public function getCallerPermissions(Caller $caller)
    {
        $results = $this->getTable()
            ->where('caller_type', $caller->getCallerType())
            ->where('caller_id', $caller->getCallerId())
            ->get();

        return PermissionFactory::createFromArray($results);
    }

    /**
     * Stores a new permission for a caller
     *
     * @param \BeatSwitch\Lock\Callers\Caller $caller
     * @param \BeatSwitch\Lock\Permissions\Permission
     * @return void
     */
    public function storeCallerPermission(Caller $caller, Permission $permission)
    {
        $this->getTable()->insert([
            'caller_type' => $caller->getCallerType(),
            'caller_id' => $caller->getCallerId(),
            'type' => $permission->getType(),
            'action' => $permission->getAction(),
            'resource_type' => $permission->getResourceType(),
            'resource_id' => $permission->getResourceId(),
        ]);
    }

    /**
     * Removes a permission for a caller
     *
     * @param \BeatSwitch\Lock\Callers\Caller $caller
     * @param \BeatSwitch\Lock\Permissions\Permission
     * @return void
     */
    public function removeCallerPermission(Caller $caller, Permission $permission)
    {
        $query = $this->getTable()
            ->where('caller_type', $caller->getCallerType())
            ->where('caller_id', $caller->getCallerId())
            ->where('type', $permission->getType())
            ->where('action', $permission->getAction());

        if (is_null($permission->getResourceType())) {
            $query->whereNull('resource_type');
        } else {
            $query->where('resource_type', $permission->getResourceType());
        }

        if (is_null($permission->getResourceId())) {
            $query->whereNull('resource_id');
        } else {
            $query->where('resource_id', $permission->getResourceId());
        }

        $query->delete();
    }

    /**
     * Checks if a permission is stored for a caller
     *
     * @param \BeatSwitch\Lock\Callers\Caller $caller
     * @param \BeatSwitch\Lock\Permissions\Permission
     * @return bool
     */
    public function hasCallerPermission(Caller $caller, Permission $permission)
    {
        $query = $this->getTable()
            ->where('caller_type', $caller->getCallerType())
            ->where('caller_id', $caller->getCallerId())
            ->where('type', $permission->getType())
            ->where('action', $permission->getAction());

        if (is_null($permission->getResourceType())) {
            $query->whereNull('resource_type');
        } else {
            $query->where('resource_type', $permission->getResourceType());
        }

        if (is_null($permission->getResourceId())) {
            $query->whereNull('resource_id');
        } else {
            $query->where('resource_id', $permission->getResourceId());
        }

        return (bool) $query->first();
    }

    /**
     * Returns all the permissions for a role
     *
     * @param \BeatSwitch\Lock\Roles\Role $role
     * @return \BeatSwitch\Lock\Permissions\Permission[]
     */
    public function getRolePermissions(Role $role)
    {
        $results = $this->getTable()->where('role', $role->getRoleName())->get();

        return PermissionFactory::createFromArray($results);
    }

    /**
     * Stores a new permission for a role
     *
     * @param \BeatSwitch\Lock\Roles\Role $role
     * @param \BeatSwitch\Lock\Permissions\Permission
     * @return void
     */
    public function storeRolePermission(Role $role, Permission $permission)
    {
        $this->getTable()->insert([
            'role' => $role->getRoleName(),
            'type' => $permission->getType(),
            'action' => $permission->getAction(),
            'resource_type' => $permission->getResourceType(),
            'resource_id' => $permission->getResourceId(),
        ]);
    }

    /**
     * Removes a permission for a role
     *
     * @param \BeatSwitch\Lock\Roles\Role $role
     * @param \BeatSwitch\Lock\Permissions\Permission
     * @return void
     */
    public function removeRolePermission(Role $role, Permission $permission)
    {
        $query = $this->getTable()
            ->where('role', $role->getRoleName())
            ->where('type', $permission->getType())
            ->where('action', $permission->getAction());

        if (is_null($permission->getResourceType())) {
            $query->whereNull('resource_type');
        } else {
            $query->where('resource_type', $permission->getResourceType());
        }

        if (is_null($permission->getResourceId())) {
            $query->whereNull('resource_id');
        } else {
            $query->where('resource_id', $permission->getResourceId());
        }

        $query->delete();
    }

    /**
     * Checks if a permission is stored for a role
     *
     * @param \BeatSwitch\Lock\Roles\Role $role
     * @param \BeatSwitch\Lock\Permissions\Permission
     * @return bool
     */
    public function hasRolePermission(Role $role, Permission $permission)
    {
        $query = $this->getTable()
            ->where('role', $role->getRoleName())
            ->where('type', $permission->getType())
            ->where('action', $permission->getAction());

        if (is_null($permission->getResourceType())) {
            $query->whereNull('resource_type');
        } else {
            $query->where('resource_type', $permission->getResourceType());
        }

        if (is_null($permission->getResourceId())) {
            $query->whereNull('resource_id');
        } else {
            $query->where('resource_id', $permission->getResourceId());
        }

        return (bool) $query->first();
    }

    /**
     * Returns the table from the connection
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }
}
