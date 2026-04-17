<?php

namespace App\Enums;

/**
 * Summary of NameOfCache
 */
enum NameOfCache: string {
    case Users = 'user_all';
    case Profiles = 'profile_all';
    case Category = 'category_all';
    case Course = "course_all";
    case Section = "section_all";
    case Lesson = 'lesson_all';

    case Enrollment = 'enrollment_all';

    case SubscriptionPlan = "subscription_plan_all";
    case Subscription = 'Subscription_all';

    case paymentMethod ='payment_methods';
    case  Invoice='Invoices';
}
