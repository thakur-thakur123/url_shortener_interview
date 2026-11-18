<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    /** Form + list of invitations sent by the logged-in user. */
    public function create(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isSuperAdmin() || $user->isAdmin(), 403);

        $invitations = Invitation::with('company')
            ->where('invited_by', $user->id)
            ->latest()
            ->get();

        return view('invitations.create', compact('user', 'invitations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            // SuperAdmin can ONLY invite an Admin into a brand new company.
            $data = $request->validate([
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'company_name' => ['required', 'string', 'max:255'],
            ]);

            $company = Company::create(['name' => $data['company_name']]);
            $role = Role::Admin;
        } elseif ($user->isAdmin()) {
            // Admin can invite an Admin or a Member, always into their OWN company.
            $data = $request->validate([
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'role' => ['required', Rule::in([Role::Admin->value, Role::Member->value])],
            ]);

            $company = $user->company;
            $role = Role::from($data['role']);
        } else {
            abort(403);
        }

        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $user->id,
            'email' => $data['email'],
            'role' => $role,
        ]);

        return redirect()
            ->route('invitations.create')
            ->with('status', 'Invitation created. Share this link with the user: '.$invitation->link());
    }

    /** Public page opened from the invitation link. */
    public function show(string $token)
    {
        $invitation = $this->findPending($token);

        return view('invitations.accept', compact('invitation'));
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->findPending($token);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        abort_if(User::where('email', $invitation->email)->exists(), 422, 'This email is already registered.');

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => $data['password'],
            'role' => $invitation->role,
            'company_id' => $invitation->company_id,
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/');
    }

    private function findPending(string $token): Invitation
    {
        return Invitation::with('company')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();
    }
}
