<?php

namespace App\Providers;

use App\Http\Services\Api\V1\Admin\AcademicYear\AcademicYearService;
use App\Http\Services\Api\V1\Admin\AcademicYear\AcademicYearServiceImpl;
use App\Http\Services\Api\V1\Admin\Medium\MediumService;
use App\Http\Services\Api\V1\Admin\Medium\MediumServiceImpl;
use App\Http\Services\Api\V1\Admin\ParentGuardian\ParentGuardianService;
use App\Http\Services\Api\V1\Admin\ParentGuardian\ParentGuardianServiceImpl;
use App\Http\Services\Api\V1\Admin\Permission\PermissionService;
use App\Http\Services\Api\V1\Admin\Permission\PermissionServiceImpl;
use App\Http\Services\Api\V1\Admin\Role\RoleService;
use App\Http\Services\Api\V1\Admin\Role\RoleServiceImpl;
use App\Http\Services\Api\V1\Admin\School\SchoolService;
use App\Http\Services\Api\V1\Admin\School\SchoolServiceImpl;
use App\Http\Services\Api\V1\Admin\SchoolClass\SchoolClassService;
use App\Http\Services\Api\V1\Admin\SchoolClass\SchoolClassServiceImpl;
use App\Http\Services\Api\V1\Admin\Section\SectionService;
use App\Http\Services\Api\V1\Admin\Section\SectionServiceImpl;
use App\Http\Services\Api\V1\Admin\Student\StudentService;
use App\Http\Services\Api\V1\Admin\Student\StudentServiceImpl;
use App\Http\Services\Api\V1\Admin\StudentDropout\StudentDropoutService;
use App\Http\Services\Api\V1\Admin\StudentDropout\StudentDropoutServiceImpl;
use App\Http\Services\Api\V1\Admin\StudentEnrollment\StudentEnrollmentService;
use App\Http\Services\Api\V1\Admin\StudentEnrollment\StudentEnrollmentServiceImpl;
use App\Http\Services\Api\V1\Admin\StudentPromotion\StudentPromotionService;
use App\Http\Services\Api\V1\Admin\StudentPromotion\StudentPromotionServiceImpl;
use App\Http\Services\Api\V1\Admin\TransferCertificate\TransferCertificateService;
use App\Http\Services\Api\V1\Admin\TransferCertificate\TransferCertificateServiceImpl;
use App\Http\Services\Api\V1\Admin\User\UserService;
use App\Http\Services\Api\V1\Admin\User\UserServiceImpl;
use App\Http\Services\Api\V1\Auth\AuthService;
use App\Http\Services\Api\V1\Auth\AuthServiceImpl;
use Illuminate\Support\ServiceProvider;

class ServiceBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthService::class,
            AuthServiceImpl::class
        );

        $this->app->bind(
            PermissionService::class,
            PermissionServiceImpl::class
        );

        $this->app->bind(
            RoleService::class,
            RoleServiceImpl::class
        );

        $this->app->bind(
            UserService::class,
            UserServiceImpl::class
        );

        $this->app->bind(
            MediumService::class,
            MediumServiceImpl::class
        );

        $this->app->bind(
            SchoolClassService::class,
            SchoolClassServiceImpl::class
        );

        $this->app->bind(
            SectionService::class,
            SectionServiceImpl::class
        );

        $this->app->bind(
            AcademicYearService::class,
            AcademicYearServiceImpl::class
        );

        $this->app->bind(
            SchoolService::class,
            SchoolServiceImpl::class
        );

        $this->app->bind(
            StudentService::class,
            StudentServiceImpl::class
        );

        $this->app->bind(
            ParentGuardianService::class,
            ParentGuardianServiceImpl::class
        );

        $this->app->bind(
            StudentEnrollmentService::class,
            StudentEnrollmentServiceImpl::class
        );

        $this->app->bind(
            StudentPromotionService::class,
            StudentPromotionServiceImpl::class
        );

        $this->app->bind(
            TransferCertificateService::class,
            TransferCertificateServiceImpl::class
        );

        $this->app->bind(
            StudentDropoutService::class,
            StudentDropoutServiceImpl::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}