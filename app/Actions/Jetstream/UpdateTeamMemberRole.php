<?php

namespace App\Actions\Jetstream;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Events\TeamMemberUpdated;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class UpdateTeamMemberRole
{
    /**
     * Update the role for the given team member.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @param  int  $teamMemberId
     * @param  string  $role
     * @return void
     */
    public function update($user, $team, $teamMemberId, string $role)
    {
        Gate::forUser($user)->authorize('updateTeamMember', $team);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $team->users()->updateExistingPivot($teamMemberId, [
            'role' => $role,
        ]);

        TeamMemberUpdated::dispatch($team->fresh(), Jetstream::findUserByIdOrFail($teamMemberId));

        $this->log_activity($user, $team, $teamMemberId, $role);
    }

    protected function log_activity($user, $team, $teamMemberId, $role)
    {
        Log::info('Team member role updated', ['user' => $user->name, 'team' => $team->name, 'team_member_id' => $teamMemberId, 'role' => $role]);
    }
}
