<?php

namespace App\Services;

use App\Models\User;
use App\Models\Internship;
use Illuminate\Auth\Access\AuthorizationException;

class AuthorizationService
{
    /**
     * Authorize that the user is the student owner of the internship
     *
     * @param Internship $internship
     * @param User $user
     * @throws AuthorizationException
     */
    public static function authorizeStudentOwnsInternship(Internship $internship, User $user): void
    {
        if (!$user->student || $internship->student_id !== $user->student->id) {
            throw new AuthorizationException('Unauthorized to access this internship.');
        }
    }

    /**
     * Check if user can manage internships (admin or garant)
     *
     * @param User $user
     * @return bool
     */
    public static function canManageInternships(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'garant']);
    }

    /**
     * Check if user can manage announcements (admin or garant)
     *
     * @param User $user
     * @return bool
     */
    public static function canManageAnnouncements(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'garant']);
    }

    /**
     * Check if user can manage garants (admin only)
     *
     * @param User $user
     * @return bool
     */
    public static function canManageGarants(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Authorize that user can manage comments (admin or garant)
     *
     * @param User $user
     * @throws AuthorizationException
     */
    public static function authorizeCanManageComments(User $user): void
    {
        if (!$user->hasAnyRole(['admin', 'garant'])) {
            throw new AuthorizationException('Only admins and garants can manage comments.');
        }
    }

    /**
     * Authorize that user is a student
     *
     * @param User $user
     * @throws AuthorizationException
     */
    public static function authorizeIsStudent(User $user): void
    {
        if (!$user->isStudent() || !$user->student) {
            throw new AuthorizationException('User must be a student.');
        }
    }

    /**
     * Authorize that user is admin
     *
     * @param User $user
     * @throws AuthorizationException
     */
    public static function authorizeIsAdmin(User $user): void
    {
        if (!$user->isAdmin()) {
            throw new AuthorizationException('Only administrators can perform this action.');
        }
    }
}
