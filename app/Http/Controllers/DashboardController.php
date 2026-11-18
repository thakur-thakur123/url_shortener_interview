<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // SuperAdmin also gets the list of companies.
        $companies = $user->isSuperAdmin()
            ? Company::withCount('users')->latest()->get()
            : collect();

        return view('dashboard', compact('user', 'companies'));
    }
}
