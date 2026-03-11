<?php

namespace Modules\CourseManagement\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\CourseManagement\Events\CreateCourseEvent;
use Modules\CourseManagement\Events\UpdateCourseEvent;
use Modules\CourseManagement\Events\UpdateLessonFilesEvent;
use Modules\CourseManagement\Events\UploadAttachementEvent;
use Modules\CourseManagement\Listeners\CreateCourseListener;
use Modules\CourseManagement\Listeners\UpdateCourseListener;
use Modules\CourseManagement\Listeners\UpdateLessonFilesListener;
use Modules\CourseManagement\Listeners\UpdateLessonFilesListner;
use Modules\CourseManagement\Listeners\UploadAttachementListener;

class EventServiceProvider extends ServiceProvider {

    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        CreateCourseEvent::class => [
            CreateCourseListener::class
        ],
        UpdateCourseEvent::class => [
            UpdateCourseListener::class
        ],
        UploadAttachementEvent::class=>[
            UploadAttachementListener::class
        ],
        UpdateLessonFilesEvent::class=>[
            UpdateLessonFilesListner::class
        ]
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {
    }
}
