<?php

namespace App\Http\Controllers\App;

use App\Models\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

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
        $validateData = $request->validate([
            'name'=>'required|string|max:255',
            'domain_name'=>'required|string |max:255|unique:domains,domain',
            'email'=>'required|email|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

        ]);
        $tenant=Tenant::create($validateData);
        $tenant->domains()->create([
            'domain' =>$validateData['domain_name'].'.'.config('app.domain')
        ]);
       
        session()->flash('success', 'Tenant created successfully!');
        return redirect()->route('tenants.index');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        //
    }
}
