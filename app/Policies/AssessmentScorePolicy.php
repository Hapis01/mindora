<?php

namespace App\Policies;

use App\Models\AssessmentScore;
use App\Models\User;

class AssessmentScorePolicy
{
    /**
     * Determine whether the user can view the assessment score.
     */
    public function view(User $user, AssessmentScore $score): bool
    {
        // Admin and Super Admin can view any score; regular users can only view their own.
        return $user->isAdmin() || $user->id === $score->user_id;
    }
}
