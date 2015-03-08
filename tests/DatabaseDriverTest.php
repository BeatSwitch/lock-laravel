<?php
namespace tests\BeatSwitch\Lock\Integrations\Laravel;

use BeatSwitch\Lock\Integrations\Laravel\DatabaseDriver;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use BeatSwitch\Lock\Tests\PersistentDriverTestCase;

class DatabaseDriverTest extends PersistentDriverTestCase
{
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $database;

    /**
     * The name of the table to store the permissions
     *
     * @var string
     */
    protected $table = 'lock_permissions';

    function setUp()
    {
        $this->setupDatabase();

        $this->driver = $this->bootstrapDriver();

        parent::setUp();
    }

    /**
     * Setup the database
     */
    protected function setupDatabase()
    {
        // Setup the database connection.
        $this->database = new Manager;
        $this->database->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:'
        ]);

        // Execute migrations.
        $this->database->getConnection()->getSchemaBuilder()->create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('caller_type')->nullable();
            $table->integer('caller_id')->nullable();
            $table->string('role')->nullable();
            $table->string('type');
            $table->string('action');
            $table->string('resource_type')->nullable();
            $table->integer('resource_id')->nullable();
        });
    }

    /**
     * Bootstrap the DatabaseDriver
     */
    protected function bootstrapDriver()
    {
        return new DatabaseDriver($this->database->getConnection(), $this->table);
    }
}
