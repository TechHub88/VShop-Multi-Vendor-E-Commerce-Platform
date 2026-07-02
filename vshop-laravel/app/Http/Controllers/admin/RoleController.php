<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\Permission;
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('created_at', 'desc')->get();
        return view('admin.roles.index', compact('roles'));
    }

   public function create()
    {
        // Group permissions category wise
        $permissions = Permission::all()->groupBy('group_name');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $request->validate(['name' => 'required|unique:roles,name,'.$role->id]);
        $role->update(['name' => $request->name]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated!');
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted!');
    }
}
