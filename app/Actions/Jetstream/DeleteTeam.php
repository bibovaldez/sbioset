<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Illuminate\Support\Facades\Log;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete(Team $team): void
    {
        $team->purge();
        $this->log_activity($team);
    }

    protected function log_activity(Team $team)
    {
        Log::info('Team deleted', ['team' => $team->name]);
    }
}
