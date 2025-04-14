<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants=Tenant::with('domains')->get();
     //   dd($tenants);
        return view('tenants.index',['tenants'=>$tenants]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateData = $request->validate([
                'name'=>'required|string|max:255',
                'domain_name'=>'required|string|max:255|unique:domains,domain',
                'email'=>'required|email|max:255',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $tenant = Tenant::create($validateData);
            $tenant->domains()->create([
                'domain' => $validateData['domain_name'].'.'.config('app.domain')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating tenant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        try {
            $validateData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'domain_name' => 'required|string|max:255|unique:domains,domain,' . $tenant->domains->first()->id . ',id',
                'password' => 'nullable|confirmed|min:8',
            ]);

            $tenant->name = $validateData['name'];
            $tenant->email = $validateData['email'];
            
            if (!empty($validateData['password'])) {
                $tenant->password = Hash::make($validateData['password']);
            }
            
            $tenant->save();

            $domain = $tenant->domains->first();
            $domain->domain = $validateData['domain_name'];
            $domain->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating tenant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting tenant: ' . $e->getMessage()
            ], 500);
        }
    }
}
