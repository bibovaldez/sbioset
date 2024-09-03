<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $user = Auth::user();
        // get the user team
        $selectedTeamId = $this->request->input('team_id', $user->current_team_id);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input, $selectedTeamId) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'current_team_id' => $selectedTeamId,
            ]), function (User $user) use ($selectedTeamId) {
                $this->team_user($user, $selectedTeamId);
                $this->Log_activity($user);
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function team_user(User $user, $selectedTeamId)
    {
        // add data in table using db transaction
        DB::transaction(function () use ($user, $selectedTeamId) {
            DB::table('team_user')->insert([
                'team_id' => $selectedTeamId,
                'user_id' => $user->id,
                'role' => 'worker',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    protected function Log_activity(User $user)
    {
        Log::info('User Created', [
            'user_id' => $user->id,
            'username' => $user->name,
            'user_email' => $user->email,
        ]);
    }
}
