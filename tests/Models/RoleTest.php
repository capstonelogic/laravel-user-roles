<?php namespace CapstoneLogic\Auth\Tests\Models;

use CapstoneLogic\Auth\Model\Permission;
use CapstoneLogic\Auth\Model\Role;
use CapstoneLogic\Auth\Model\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleTest extends ModelsTest
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Role */
    protected $roleModel;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->roleModel = new Role;
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->roleModel);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function itCanBeInstantiated()
    {
        $expectations = [
            \Illuminate\Database\Eloquent\Model::class,
            \CapstoneLogic\Auth\Model\Role::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->roleModel);
        }
    }

    /** @test */
    public function itHasRelationships()
    {
        $usersRelationship       = $this->roleModel->users();
        $permissionsRelationship = $this->roleModel->permissions();

        $this->assertInstanceOf(BelongsToMany::class, $usersRelationship);
        $this->assertInstanceOf(BelongsToMany::class, $permissionsRelationship);
    
        /**
         * @var  User        $user
         * @var  Permission  $permission
         */
        $user       = $usersRelationship->getRelated();
        $permission = $permissionsRelationship->getRelated();
    
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Permission::class, $permission);
    }
    
    /** @test */
    public function itCanCreate()
    {
        $attributes = [
            'name'        => 'Custom role',
            'slug'        => $this->str_slug('Custom role', config('laravel-auth.slug-separator')),
            'description' => 'Custom role description.',
        ];
    
        $role = $this->createRole($attributes);
    
        $this->assertEquals($attributes['name'], $role->name);
        $this->assertEquals($attributes['slug'], $role->slug);
        $this->assertEquals($attributes['description'], $role->description);
    
        $this->assertDatabaseHas('roles', $attributes);
    }
    
    /** @test */
    public function itCanUpdate()
    {
        $attributes = $this->getAdminRoleAttributes();
    
        $role = $this->createRole($attributes);
    
        $this->assertDatabaseHas('roles', $attributes);
        $this->assertDatabaseHas('roles', $role->toArray());
    
        $updatedAttributes = [
            'name'        => 'Custom role',
            'description' => 'Custom role description.',
        ];
    
        $role->update($updatedAttributes);
    
        $this->assertDatabaseMissing('roles', $attributes);
        $this->assertDatabaseHas('roles', $updatedAttributes);
        $this->assertDatabaseHas('roles', $role->toArray());
    }
    
    /** @test */
    public function itCanDelete()
    {
        $role = $this->createRole();
    
        $this->assertDatabaseHas('roles', $role->toArray());
    
        $role->delete();
    
        $this->assertDatabaseMissing('roles', $role->toArray());
    }
    
    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create role model.
     *
     * @param  array  $attributes
     *
     * @return Role
     */
    private function createRole(array $attributes = [])
    {
        if (empty($attributes)) {
            $attributes = $this->getAdminRoleAttributes();
        }
    
        /** @var Role $role */
        $role = $this->roleModel->create($attributes);
    
        return $this->roleModel->find($role->id);
    }
    
    /**
     * Get a dummy user attributes.
     *
     * @return array
     */
    private function getAdminRoleAttributes()
    {
        return [
            'name'        => 'Admin',
            'slug'        => $this->str_slug('Admin role', config('laravel-auth.slug-separator')),
            'description' => 'Admin role descriptions.',
        ];
    }
}
