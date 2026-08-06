<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrationField;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\ProfileWizardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ProfileSearchController;
use App\Http\Controllers\User\PhotoEditorController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\MediaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SuccessStoryController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\ProfileApprovalController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BulkEmailController;
use App\Http\Controllers\Admin\BulkWhatsAppController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\RegistrationFieldController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\AccountApprovalController;

// Utility & Maintenance Routes

Route::get('/dbcheck', function() {
    $field = RegistrationField::where('field_key', 'subcast')->first();
    if (!$field) {
        $field = RegistrationField::create([
            'field_group' => 'Personal Details',
            'field_key' => 'subcast',
            'field_label' => 'Sub-Cast (उपजाति)',
            'field_type' => 'dropdown',
            'field_options' => 'Khandelwal,Agrawal,Oswal,Porwal,Golalare,Humad,Bagherwal,Chaturth,Pancham,Other (अन्य)',
            'is_custom' => false,
            'is_visible' => true,
            'is_required' => true,
            'is_core' => true,
            'sort_order' => 1,
        ]);
        return 'Created: ' . $field->id;
    }
    return $field;
});

Route::get('/patch-birthtime-column', function() {
    try {
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `birth_time` VARCHAR(50) NULL DEFAULT NULL");
        return "Successfully updated birth_time column to VARCHAR(50) in users table.";
    } catch (\Throwable $e) {
        return "Patch execution message: " . $e->getMessage();
    }
});

Route::get('/', [HomeController::class, 'index'])->name('home');

// Candidate Auth Routes
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,5');
    Route::get('/register/verify-otp', [RegisterController::class, 'showOtpForm'])->name('register.otp');
    Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->middleware('throttle:5,5');
    Route::post('/register/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.resend-otp')->middleware('throttle:5,5');
});

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Admin Auth Routes
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login-form');
    Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login');
});

// Logout Routes
Route::post('/logout', [LoginController::class, 'logoutUser'])->name('logout');            // User logout → /login
Route::post('/user/logout', [LoginController::class, 'logoutUser'])->name('user.logout');  // Explicit user logout
Route::post('/admin/logout', [LoginController::class, 'logoutAdmin'])->name('admin.logout'); // Admin logout → /admin/login


Route::get('/image', [ImageController::class, 'serve'])->name('image.serve');
Route::get('/gallery', [MediaController::class, 'gallery'])->name('gallery');
Route::get('/success-stories', [MediaController::class, 'successStories'])->name('stories');

Route::middleware(['auth:web', 'profile.completed'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'myProfile'])->name('profile.my');
    Route::get('/profile/edit', [ProfileController::class, 'showEditForm'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/resubmit', [ProfileController::class, 'resubmit'])->name('profile.resubmit');
    Route::post('/profile/payment', [ProfileController::class, 'uploadPayment'])->name('profile.payment.upload');
    Route::delete('/profile', [ProfileController::class, 'deleteProfile'])->name('profile.delete');

    
    Route::get('/success-stories/add', [MediaController::class, 'addSuccessStory'])->name('success-stories.add');
    Route::post('/success-stories/add', [MediaController::class, 'storeSuccessStory'])->name('success-stories.store');
    
    Route::get('/change-password', [ChangePasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.change.update');
    
    Route::get('/profiles', [ProfileSearchController::class, 'index'])->name('profiles');
    Route::get('/profiles/{profile}', [ProfileSearchController::class, 'showDetail'])->name('profiles.detail');
    Route::get('/profiles/{profile}/pdf', [ProfileSearchController::class, 'downloadPdf'])->name('profiles.pdf');
    Route::post('/profiles/{profile}/like', [ProfileSearchController::class, 'toggleLike'])->name('profiles.like');
    Route::post('/user/profile/photo/rotate', [PhotoEditorController::class, 'rotate'])->name('user.photo.rotate');

    // Candidate Wizard & Dashboard
    Route::get('/registration-wizard', [ProfileWizardController::class, 'showWizard'])->name('registration.wizard');
    Route::post('/registration-wizard/basic', [ProfileWizardController::class, 'saveBasic'])->name('registration.save.basic');
    Route::post('/registration-wizard/personal', [ProfileWizardController::class, 'savePersonal'])->name('registration.save.personal');
    Route::post('/registration-wizard/family', [ProfileWizardController::class, 'saveFamily'])->name('registration.save.family');
    Route::post('/registration-wizard/final', [ProfileWizardController::class, 'saveFinal'])->name('registration.save.final');
    Route::post('/registration-wizard/check-mobile', [ProfileWizardController::class, 'checkMobile'])->name('registration.check-mobile');

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Stage 1 Account Approvals
    Route::get('/admin/account-approvals', [AccountApprovalController::class, 'index'])->name('admin.account-approvals.index');
    Route::post('/admin/account-approvals/{id}/approve', [AccountApprovalController::class, 'approve'])->name('admin.account-approvals.approve');
    Route::post('/admin/account-approvals/{id}/reject', [AccountApprovalController::class, 'reject'])->name('admin.account-approvals.reject');
    
    // Contact Messages
    Route::get('/admin/contacts', [ContactMessageController::class, 'index'])->name('admin.contacts.index');
    Route::delete('/admin/contacts/{id}', [ContactMessageController::class, 'destroy'])->name('admin.contacts.destroy');
    
    // Bulk Email
    Route::get('/admin/bulk-email', [BulkEmailController::class, 'index'])->name('admin.bulk-email.index');
    Route::post('/admin/bulk-email', [BulkEmailController::class, 'send'])->name('admin.bulk-email.send');

    // Bulk WhatsApp
    Route::get('/admin/bulk-whatsapp', [BulkWhatsAppController::class, 'index'])->name('admin.bulk-whatsapp.index');

    // Membership Plans
    Route::get('/admin/membership-plans', [MembershipController::class, 'index'])->name('admin.membership-plans.index');
    Route::post('/admin/membership-plans', [MembershipController::class, 'store'])->name('admin.membership-plans.store');
    Route::put('/admin/membership-plans/{plan}', [MembershipController::class, 'update'])->name('admin.membership-plans.update');
    Route::delete('/admin/membership-plans/{plan}', [MembershipController::class, 'destroy'])->name('admin.membership-plans.destroy');

    // Registration Fields Manager
    Route::get('/admin/registration-fields', [RegistrationFieldController::class, 'index'])->name('admin.registration-fields.index');
    Route::post('/admin/registration-fields', [RegistrationFieldController::class, 'store'])->name('admin.registration-fields.store');
    Route::post('/admin/registration-fields/visibility', [RegistrationFieldController::class, 'saveVisibility'])->name('admin.registration-fields.visibility');
    Route::post('/admin/registration-fields/{id}/options', [RegistrationFieldController::class, 'updateOptions'])->name('admin.registration-fields.options');
    Route::delete('/admin/registration-fields/{id}', [RegistrationFieldController::class, 'destroy'])->name('admin.registration-fields.destroy');
    
    // Settings
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    
    // Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
    
    // Approvals queue
    Route::get('/admin/approvals', [ProfileApprovalController::class, 'index'])->name('admin.approvals.index');
    Route::post('/admin/approvals/{member}/approve', [ProfileApprovalController::class, 'approve'])->name('admin.approvals.approve');
    Route::post('/admin/approvals/{member}/reject', [ProfileApprovalController::class, 'reject'])->name('admin.approvals.reject');
    
    // Members management
    Route::get('/admin/members', [MemberController::class, 'index'])->name('admin.members.index');
    Route::get('/admin/members-incomplete', [MemberController::class, 'incomplete'])->name('admin.members.incomplete');
    Route::get('/admin/members-requests', [MemberController::class, 'requests'])->name('admin.members.requests');
    Route::post('/admin/members-requests/{id}/process', [MemberController::class, 'processRequest'])->name('admin.members.requests.process');
    Route::get('/admin/members/{member}', [MemberController::class, 'show'])->name('admin.members.show');
    Route::get('/admin/members/{member}/edit', [MemberController::class, 'edit'])->name('admin.members.edit');
    Route::put('/admin/members/{member}', [MemberController::class, 'update'])->name('admin.members.update');
    Route::post('/admin/members/{member}/status', [MemberController::class, 'updateStatus'])->name('admin.members.status');
    Route::delete('/admin/members/{member}', [MemberController::class, 'destroy'])->name('admin.members.destroy');

    // Payments management
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/admin/payments/manual', [PaymentController::class, 'storeManual'])->name('admin.payments.manual');
    Route::post('/admin/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('admin.payments.verify');

    // CMS Announcements & Marquees
    Route::get('/admin/cms/news', [NewsController::class, 'index'])->name('admin.cms.news.index');
    Route::post('/admin/cms/news', [NewsController::class, 'storeNews'])->name('admin.cms.news.store');
    Route::put('/admin/cms/news/{news}', [NewsController::class, 'updateNews'])->name('admin.cms.news.update');
    Route::post('/admin/cms/news/{news}/toggle', [NewsController::class, 'toggleNews'])->name('admin.cms.news.toggle');
    Route::delete('/admin/cms/news/{news}', [NewsController::class, 'destroyNews'])->name('admin.cms.news.destroy');

    Route::post('/admin/cms/marquee', [NewsController::class, 'storeMarquee'])->name('admin.cms.marquee.store');
    Route::put('/admin/cms/marquee/{marquee}', [NewsController::class, 'updateMarquee'])->name('admin.cms.marquee.update');
    Route::post('/admin/cms/marquee/{marquee}/toggle', [NewsController::class, 'toggleMarquee'])->name('admin.cms.marquee.toggle');
    Route::delete('/admin/cms/marquee/{marquee}', [NewsController::class, 'destroyMarquee'])->name('admin.cms.marquee.destroy');

    // CMS Photo & Video Galleries
    Route::get('/admin/cms/gallery', [GalleryController::class, 'index'])->name('admin.cms.gallery.index');
    Route::post('/admin/cms/gallery/photo', [GalleryController::class, 'storePhoto'])->name('admin.cms.gallery.photo.store');
    Route::post('/admin/cms/gallery/photo/{photo}/toggle', [GalleryController::class, 'togglePhoto'])->name('admin.cms.gallery.photo.toggle');
    Route::delete('/admin/cms/gallery/photo/{photo}', [GalleryController::class, 'destroyPhoto'])->name('admin.cms.gallery.photo.destroy');

    Route::post('/admin/cms/gallery/banner', [GalleryController::class, 'updateBanner'])->name('admin.cms.gallery.banner.update');
    Route::post('/admin/cms/gallery/video', [GalleryController::class, 'storeVideo'])->name('admin.cms.gallery.video.store');
    Route::post('/admin/cms/gallery/video/{video}/toggle', [GalleryController::class, 'toggleVideo'])->name('admin.cms.gallery.video.toggle');
    Route::delete('/admin/cms/gallery/video/{video}', [GalleryController::class, 'destroyVideo'])->name('admin.cms.gallery.video.destroy');

    // CMS Success Stories
    Route::get('/admin/cms/stories', [SuccessStoryController::class, 'index'])->name('admin.cms.stories.index');
    Route::post('/admin/cms/stories/{story}/status', [SuccessStoryController::class, 'updateStatus'])->name('admin.cms.stories.status');
    Route::delete('/admin/cms/stories/{story}', [SuccessStoryController::class, 'destroy'])->name('admin.cms.stories.destroy');

    // CMS Advertisements
    Route::get('/admin/cms/ads', [AdvertisementController::class, 'index'])->name('admin.cms.ads.index');
    Route::post('/admin/cms/ads', [AdvertisementController::class, 'store'])->name('admin.cms.ads.store');
    Route::put('/admin/cms/ads/{ad}', [AdvertisementController::class, 'update'])->name('admin.cms.ads.update');
    Route::post('/admin/cms/ads/{ad}/toggle', [AdvertisementController::class, 'toggle'])->name('admin.cms.ads.toggle');
    Route::delete('/admin/cms/ads/{ad}', [AdvertisementController::class, 'destroy'])->name('admin.cms.ads.destroy');

    // Latest Profiles Bottom Advertisement
    Route::match(['get', 'post'], '/admin/cms/ads/latest-profiles-bottom/toggle', [AdvertisementController::class, 'toggleLatestProfilesBottomSection'])->name('admin.cms.ads.latest-profiles-bottom.toggle-section');
    Route::post('/admin/cms/ads/latest-profiles-bottom', [AdvertisementController::class, 'updateLatestProfilesBottomAd'])->name('admin.cms.ads.latest-profiles-bottom.update');
    Route::delete('/admin/cms/ads/latest-profiles-bottom/image', [AdvertisementController::class, 'removeLatestProfilesBottomImage'])->name('admin.cms.ads.latest-profiles-bottom.remove-image');
});

// CMS Pages
Route::get('/about', [CmsController::class, 'about'])->name('about');
Route::get('/contact', [CmsController::class, 'contact'])->name('contact.show');
Route::post('/contact', [CmsController::class, 'submitContact'])->name('contact.submit');
Route::get('/privacy', [CmsController::class, 'privacy'])->name('privacy');
Route::get('/terms', [CmsController::class, 'terms'])->name('terms');
Route::get('/committee', [CmsController::class, 'community'])->name('community');
Route::redirect('/community', '/committee', 301);
Route::get('/news', [CmsController::class, 'news'])->name('news');

Route::get('/waiting-approval', function () {
    $user = Auth::guard('web')->user();
    if ($user) {
        if ($user->status === 'account_approved') {
            return redirect()->route('registration.wizard');
        } elseif ($user->status === 'approved') {
            return redirect()->route('profile.my');
        }
    }
    return view('auth.waiting-approval');
})->name('waiting.approval');


