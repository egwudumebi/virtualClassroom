<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    public function view(User $user, Classroom $classroom): bool
    {
        return $user->id === $classroom->teacher_id || 
               $classroom->students()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->id === $classroom->teacher_id;
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->id === $classroom->teacher_id;
    }
}
