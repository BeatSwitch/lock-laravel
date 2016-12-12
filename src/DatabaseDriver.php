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
     * The caller permissions cache at runtime
     *
     * @var array
     */
    protected $callerPermissions = [];

    /**
     * The role permissions cache at runtime
     *
     * @var array
     */
    protected $rolePermissions = [];

    /**
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
     * @return \BeatSwitch\Lock\Permissions\Permission[]
     */
    public function getCallerPermissions(Caller $caller)
    {
        $key = $this->getCallerKey($caller);

        // If we've saved the caller permissions we don't need to fetch them again.
        if (array_key_exists($key, $this->callerPermissions)) {
            return $this->callerPermissions[$key];
        }

        $results = $this->getTable()
            ->where('caller_type', $caller->getCallerType())
            ->where('caller_id', $caller->getCallerId())
            ->get()
            ->toArray();

        return $this->callerPermissions[$key] = PermissionFactory::createFromData($results);
    }

    /**
     * Stores a new permission for a caller
     *
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

        $this->resetPermissionsCacheForCaller($caller);
    }

    /**
     * Removes a permission for a caller
     *
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

        $this->resetPermissionsCacheForCaller($caller);
    }

    /**
     * Checks if a permission is stored for a caller
     *
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
        $key = $this->getRoleKey($role);

        // If we've saved the caller permissions we don't need to fetch them again.
        if (array_key_exists($key, $this->rolePermissions)) {
            return $this->rolePermissions[$key];
        }

        $results = $this->getTable()->where('role', $role->getRoleName())->get()->toArray();

        return $this->rolePermissions[$key] = PermissionFactory::createFromData($results);
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

        $this->resetPermissionsCacheForRole($role);
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

        $this->resetPermissionsCacheForRole($role);
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

    /**
     * Creates a key to store the caller's permissions
     *
     * @param \BeatSwitch\Lock\Callers\Caller $caller
     * @return string
     */
    private function getCallerKey(Caller $caller)
    {
        return 'caller_' . $caller->getCallerType() . '_' . $caller->getCallerId();
    }

    /**
     * Creates a key to store the role's permissions
     *
     * @param \BeatSwitch\Lock\Roles\Role $role
     * @return string
     */
    private function getRoleKey(Role $role)
    {
        return 'role_' . $role->getRoleName();
    }

    /**
     * @param \BeatSwitch\Lock\Callers\Caller $caller
     */
    protected function resetPermissionsCacheForCaller(Caller $caller)
    {
        unset($this->callerPermissions[$this->getCallerKey($caller)]);
    }

    /**
     * @param \BeatSwitch\Lock\Roles\Role $role
     */
    protected function resetPermissionsCacheForRole(Role $role)
    {
        unset($this->rolePermissions[$this->getRoleKey($role)]);
    }
}
