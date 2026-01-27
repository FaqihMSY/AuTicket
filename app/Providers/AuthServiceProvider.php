<?php

namespace App\Providers;

use App\Models\Auditor;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Policies\AuditorPolicy;
use App\Policies\ProjectAttachmentPolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Project::class => ProjectPolicy::class,
        ProjectAttachment::class => ProjectAttachmentPolicy::class,
        Auditor::class => AuditorPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
