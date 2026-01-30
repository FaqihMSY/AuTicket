<?php

if (!function_exists('project_status_label')) {
    /**
     * Get Indonesian label for project status
     */
    function project_status_label(string $status): string
    {
        return match ($status) {
            'DRAFT' => 'Draf',
            'PUBLISHED' => 'Dipublikasikan',
            'ON_PROGRESS' => 'Sedang Berjalan',
            'WAITING' => 'Menunggu Review',
            'CLOSED' => 'Selesai',
            default => $status,
        };
    }
}

if (!function_exists('project_status_badge_class')) {
    /**
     * Get Bootstrap badge class for project status
     */
    function project_status_badge_class(string $status): string
    {
        return match ($status) {
            'DRAFT' => 'bg-secondary',
            'PUBLISHED' => 'bg-info',
            'ON_PROGRESS' => 'bg-primary',
            'WAITING' => 'bg-warning',
            'CLOSED' => 'bg-success',
            default => 'bg-secondary',
        };
    }
}

if (!function_exists('project_priority_label')) {
    /**
     * Get Indonesian label for project priority
     */
    function project_priority_label(string $priority): string
    {
        return match ($priority) {
            'LOW' => 'Rendah',
            'MEDIUM' => 'Sedang',
            'HIGH' => 'Tinggi',
            default => $priority,
        };
    }
}

if (!function_exists('project_priority_badge_class')) {
    /**
     * Get Bootstrap badge class for project priority
     */
    function project_priority_badge_class(string $priority): string
    {
        return match ($priority) {
            'LOW' => 'bg-success',
            'MEDIUM' => 'bg-warning',
            'HIGH' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}

if (!function_exists('user_role_label')) {
    /**
     * Get Indonesian label for user role
     */
    function user_role_label(string $role): string
    {
        return match ($role) {
            'admin' => 'Administrator',
            'pengawas' => 'Pengawas',
            'reviewer' => 'Reviewer',
            'staff' => 'Staff Auditor',
            default => $role,
        };
    }
}
