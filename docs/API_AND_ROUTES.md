# Digambar Jain Matrimony &mdash; Routes & Endpoints Reference

---

## 🌐 1. Public & Guest Routes

| Method | URI | Controller Action | Name | Description |
|---|---|---|---|---|
| `GET` | `/` | `HomeController@index` | `home` | Landing page with ad rotators & recent profiles |
| `GET` | `/login` | `Auth\LoginController@showLoginForm` | `login` | Candidate login page (Email/Mobile) |
| `POST` | `/login` | `Auth\LoginController@login` | - | Authenticates candidate |
| `GET` | `/register` | `Auth\RegisterController@showRegistrationForm` | `register` | Pre-registration form |
| `POST` | `/register` | `Auth\RegisterController@register` | - | Submits pre-registration & sends OTP |
| `GET` | `/register/verify-otp` | `Auth\RegisterController@showOtpForm` | `register.otp` | OTP entry form |
| `POST` | `/register/verify-otp` | `Auth\RegisterController@verifyOtp` | - | Verifies OTP & creates user session |
| `POST` | `/register/resend-otp` | `Auth\RegisterController@resendOtp` | `register.resend-otp` | Resends OTP email |
| `GET` | `/forgot-password` | `Auth\ForgotPasswordController@showLinkRequestForm` | `password.request` | Password reset request form |
| `POST` | `/forgot-password` | `Auth\ForgotPasswordController@sendResetLinkEmail` | `password.email` | Sends reset link email |
| `GET` | `/reset-password/{token}` | `Auth\ForgotPasswordController@showResetForm` | `password.reset` | Password update form |
| `POST` | `/reset-password` | `Auth\ForgotPasswordController@reset` | `password.update` | Sets new password |
| `GET` | `/about` | `CmsController@about` | `about` | About Digambar Jain Samaj page |
| `GET` | `/contact` | `CmsController@contact` | `contact.show` | Contact Us & support form |
| `POST` | `/contact` | `CmsController@submitContact` | `contact.submit` | Submits inquiry message |
| `GET` | `/privacy` | `CmsController@privacy` | `privacy` | Privacy Policy page |
| `GET` | `/terms` | `CmsController@terms` | `terms` | Terms & Conditions page |
| `GET` | `/committee` | `CmsController@community` | `community` | Executive Committee members directory |
| `GET` | `/news` | `CmsController@news` | `news` | News & Announcements page |
| `GET` | `/gallery` | `User\MediaController@gallery` | `gallery` | Public Photo & Video gallery |
| `GET` | `/success-stories` | `User\MediaController@successStories` | `stories` | Verified success stories |
| `GET` | `/image` | `ImageController@serve` | `image.serve` | Secure media streaming endpoint |
| `GET` | `/api/track-visit` | `VisitorCountController@track` | `visitor.track` | Async atomic unique visitor counter |
| `GET` | `/waiting-approval` | Closure | `waiting.approval` | Waiting screen for pending candidates |

---

## 👤 2. Authenticated Candidate Routes (`auth:web`, `profile.completed`)

| Method | URI | Controller Action | Name | Description |
|---|---|---|---|---|
| `GET` | `/dashboard` | `User\DashboardController@index` | `user.dashboard` | Candidate dashboard |
| `GET` | `/profile` | `User\ProfileController@myProfile` | `profile.my` | View own profile & payment status |
| `GET` | `/profile/edit` | `User\ProfileController@showEditForm` | `profile.edit` | Candidate profile edit form |
| `POST` | `/profile/edit` | `User\ProfileController@updateProfile` | `profile.update` | Updates candidate profile details |
| `POST` | `/profile/resubmit` | `User\ProfileController@resubmit` | `profile.resubmit` | Resubmits rejected profile for review |
| `POST` | `/profile/payment` | `User\ProfileController@uploadPayment` | `profile.payment.upload`| Uploads payment screenshot post-wizard |
| `DELETE` | `/profile` | `User\ProfileController@deleteProfile` | `profile.delete` | Candidate deletes/deactivates account |
| `GET` | `/profiles` | `User\ProfileSearchController@index` | `profiles` | Search candidates with filters |
| `GET` | `/profiles/{profile}` | `User\ProfileSearchController@showDetail` | `profiles.detail` | Candidate detail page / modal preview |
| `GET` | `/profiles/{profile}/pdf` | `User\ProfileSearchController@downloadPdf` | `profiles.pdf` | Printable Bio-Data / PDF view |
| `POST` | `/profiles/{profile}/like` | `User\ProfileSearchController@toggleLike` | `profiles.like` | Toggle shortlist/like |
| `POST` | `/user/profile/photo/rotate` | `User\PhotoEditorController@rotate` | `user.photo.rotate` | Rotate profile photo (0/90/180/270°) |
| `GET` | `/registration-wizard` | `ProfileWizardController@showWizard` | `registration.wizard` | 4-Step Registration Wizard |
| `POST` | `/registration-wizard/basic` | `ProfileWizardController@saveBasic` | `registration.save.basic` | Saves Wizard Step 1 |
| `POST` | `/registration-wizard/personal` | `ProfileWizardController@savePersonal` | `registration.save.personal` | Saves Wizard Step 2 |
| `POST` | `/registration-wizard/family` | `ProfileWizardController@saveFamily` | `registration.save.family` | Saves Wizard Step 3 |
| `POST` | `/registration-wizard/final` | `ProfileWizardController@saveFinal` | `registration.save.final` | Submits Wizard Step 4 |
| `POST` | `/registration-wizard/check-mobile` | `ProfileWizardController@checkMobile` | `registration.check-mobile` | AJAX check for duplicate mobile |
| `GET` | `/change-password` | `User\ChangePasswordController@showChangeForm` | `password.change` | Change password form |
| `POST` | `/change-password` | `User\ChangePasswordController@update` | `password.change.update` | Updates candidate password |
| `GET` | `/success-stories/add` | `User\MediaController@addSuccessStory` | `success-stories.add` | Add success story form |
| `POST` | `/success-stories/add` | `User\MediaController@storeSuccessStory` | `success-stories.store` | Submits wedding story for approval |
| `POST` | `/logout` | `Auth\LoginController@logoutUser` | `logout` | Candidate logout |

---

## 🔒 3. Admin Routes (`auth:admin`)

### 3.1 General Admin Operations
| Method | URI | Controller Action | Name | Description |
|---|---|---|---|---|
| `GET` | `/admin/login` | `Auth\LoginController@showAdminLoginForm` | `admin.login-form` | Admin login page |
| `POST` | `/admin/login` | `Auth\LoginController@adminLogin` | `admin.login` | Authenticates administrator |
| `POST` | `/admin/logout` | `Auth\LoginController@logoutAdmin` | `admin.logout` | Admin logout |
| `GET` | `/admin/dashboard` | `Admin\DashboardController@index` | `admin.dashboard` | Main admin analytics dashboard |
| `GET` | `/admin/account-approvals` | `Admin\AccountApprovalController@index` | `admin.account-approvals.index` | Stage 1 pre-registration requests |
| `POST` | `/admin/account-approvals/{id}/approve` | `Admin\AccountApprovalController@approve` | `admin.account-approvals.approve` | Approves Stage 1 account |
| `POST` | `/admin/account-approvals/{id}/reject` | `Admin\AccountApprovalController@reject` | `admin.account-approvals.reject` | Rejects & deletes Stage 1 account |
| `GET` | `/admin/approvals` | `Admin\ProfileApprovalController@index` | `admin.approvals.index` | Stage 2 completed profile review queue |
| `POST` | `/admin/approvals/{member}/approve` | `Admin\ProfileApprovalController@approve` | `admin.approvals.approve` | Approves profile & assigns JDM ID |
| `POST` | `/admin/approvals/{member}/reject` | `Admin\ProfileApprovalController@reject` | `admin.approvals.reject` | Rejects profile with reason |
| `GET` | `/admin/members` | `Admin\MemberController@index` | `admin.members.index` | Members management & filter list |
| `GET` | `/admin/members-incomplete` | `Admin\MemberController@incomplete` | `admin.members.incomplete` | Incomplete wizard registrations |
| `GET` | `/admin/members-requests` | `Admin\MemberController@requests` | `admin.members.requests` | Deletion & deactivation requests |
| `POST` | `/admin/members-requests/{id}/process` | `Admin\MemberController@processRequest` | `admin.members.requests.process` | Processes account deletion request |
| `GET` | `/admin/members/{member}` | `Admin\MemberController@show` | `admin.members.show` | Candidate verification bio-data |
| `GET` | `/admin/members/{member}/edit` | `Admin\MemberController@edit` | `admin.members.edit` | Admin edit profile form |
| `PUT` | `/admin/members/{member}` | `Admin\MemberController@update` | `admin.members.update` | Saves admin edits to profile |
| `POST` | `/admin/members/{member}/status` | `Admin\MemberController@updateStatus` | `admin.members.status` | Updates member status (approve/block/reject) |
| `DELETE`| `/admin/members/{member}` | `Admin\MemberController@destroy` | `admin.members.destroy` | Soft-deletes member |

### 3.2 Super Admin Protected Routes (`middleware('superadmin')`)
| Method | URI | Controller Action | Name | Description |
|---|---|---|---|---|
| `GET` | `/admin/contacts` | `Admin\ContactMessageController@index` | `admin.contacts.index` | View Contact inquiries |
| `DELETE`| `/admin/contacts/{id}` | `Admin\ContactMessageController@destroy` | `admin.contacts.destroy` | Deletes contact inquiry |
| `GET` | `/admin/bulk-email` | `Admin\BulkEmailController@index` | `admin.bulk-email.index` | Bulk email broadcasting tool |
| `POST` | `/admin/bulk-email` | `Admin\BulkEmailController@send` | `admin.bulk-email.send` | Dispatches bulk emails |
| `GET` | `/admin/bulk-whatsapp` | `Admin\BulkWhatsAppController@index` | `admin.bulk-whatsapp.index`| WhatsApp messaging tool |
| `GET` | `/admin/membership-plans` | `Admin\MembershipController@index` | `admin.membership-plans.index` | Subscription plans list |
| `POST` | `/admin/membership-plans` | `Admin\MembershipController@store` | `admin.membership-plans.store` | Creates new membership plan |
| `PUT` | `/admin/membership-plans/{plan}` | `Admin\MembershipController@update` | `admin.membership-plans.update` | Updates membership plan |
| `DELETE`| `/admin/membership-plans/{plan}` | `Admin\MembershipController@destroy` | `admin.membership-plans.destroy` | Deletes membership plan |
| `GET` | `/admin/registration-fields` | `Admin\RegistrationFieldController@index` | `admin.registration-fields.index` | Custom registration fields |
| `POST` | `/admin/registration-fields` | `Admin\RegistrationFieldController@store` | `admin.registration-fields.store` | Adds new custom field |
| `POST` | `/admin/registration-fields/visibility` | `Admin\RegistrationFieldController@saveVisibility` | `admin.registration-fields.visibility` | Toggles field visibility |
| `POST` | `/admin/registration-fields/{id}/options` | `Admin\RegistrationFieldController@updateOptions` | `admin.registration-fields.options` | Updates dropdown options |
| `DELETE`| `/admin/registration-fields/{id}` | `Admin\RegistrationFieldController@destroy` | `admin.registration-fields.destroy` | Deletes custom field |
| `GET` | `/admin/settings` | `Admin\SettingController@index` | `admin.settings.index` | System configuration & QR code |
| `POST` | `/admin/settings` | `Admin\SettingController@update` | `admin.settings.update` | Updates site settings |
| `GET` | `/admin/reports` | `Admin\ReportController@index` | `admin.reports.index` | Analytics & reporting dashboard |
| `GET` | `/admin/reports/export` | `Admin\ReportController@export` | `admin.reports.export` | CSV / Excel export of members |
| `GET` | `/admin/payments` | `Admin\PaymentController@index` | `admin.payments.index` | Payment verification queue |
| `POST` | `/admin/payments/manual` | `Admin\PaymentController@storeManual` | `admin.payments.manual` | Records offline cash payment |
| `POST` | `/admin/payments/{payment}/verify` | `Admin\PaymentController@verify` | `admin.payments.verify` | Verifies/rejects payment receipt |
| `GET` | `/admin/cms/news` | `Admin\NewsController@index` | `admin.cms.news.index` | News announcements manager |
| `POST` | `/admin/cms/news` | `Admin\NewsController@storeNews` | `admin.cms.news.store` | Creates news article |
| `PUT` | `/admin/cms/news/{news}` | `Admin\NewsController@updateNews` | `admin.cms.news.update` | Updates news article |
| `POST` | `/admin/cms/news/{news}/toggle` | `Admin\NewsController@toggleNews` | `admin.cms.news.toggle` | Toggles news active status |
| `DELETE`| `/admin/cms/news/{news}` | `Admin\NewsController@destroyNews` | `admin.cms.news.destroy` | Deletes news article |
| `POST` | `/admin/cms/marquee` | `Admin\NewsController@storeMarquee` | `admin.cms.marquee.store` | Adds marquee ticker text |
| `PUT` | `/admin/cms/marquee/{marquee}` | `Admin\NewsController@updateMarquee` | `admin.cms.marquee.update` | Updates marquee ticker text |
| `POST` | `/admin/cms/marquee/{marquee}/toggle` | `Admin\NewsController@toggleMarquee` | `admin.cms.marquee.toggle` | Toggles marquee status |
| `DELETE`| `/admin/cms/marquee/{marquee}` | `Admin\NewsController@destroyMarquee` | `admin.cms.marquee.destroy` | Deletes marquee notice |
| `GET` | `/admin/cms/gallery` | `Admin\GalleryController@index` | `admin.cms.gallery.index` | Photo & Video gallery manager |
| `POST` | `/admin/cms/gallery/photo` | `Admin\GalleryController@storePhoto` | `admin.cms.gallery.photo.store` | Uploads photo to gallery |
| `POST` | `/admin/cms/gallery/video` | `Admin\GalleryController@storeVideo` | `admin.cms.gallery.video.store` | Embeds YouTube/MP4 video |
| `GET` | `/admin/cms/stories` | `Admin\SuccessStoryController@index` | `admin.cms.stories.index` | Success stories review panel |
| `POST` | `/admin/cms/stories/{story}/status` | `Admin\SuccessStoryController@updateStatus` | `admin.cms.stories.status` | Approves/rejects success story |
| `DELETE`| `/admin/cms/stories/{story}` | `Admin\SuccessStoryController@destroy` | `admin.cms.stories.destroy` | Deletes success story |
| `GET` | `/admin/cms/ads` | `Admin\AdvertisementController@index` | `admin.cms.ads.index` | Advertisement banner manager |
| `POST` | `/admin/cms/ads` | `Admin\AdvertisementController@store` | `admin.cms.ads.store` | Creates new advertisement |
| `PUT` | `/admin/cms/ads/{ad}` | `Admin\AdvertisementController@update` | `admin.cms.ads.update` | Updates advertisement |
| `POST` | `/admin/cms/ads/{ad}/toggle` | `Admin\AdvertisementController@toggle` | `admin.cms.ads.toggle` | Toggles ad active status |
| `DELETE`| `/admin/cms/ads/{ad}` | `Admin\AdvertisementController@destroy` | `admin.cms.ads.destroy` | Deletes advertisement |
| `POST` | `/admin/cms/ads/latest-profiles-bottom` | `Admin\AdvertisementController@updateLatestProfilesBottomAd` | `admin.cms.ads.latest-profiles-bottom.update` | Updates bottom ad banner |
| `GET` | `/admin/cms/committee` | `Admin\CommitteeController@index` | `admin.cms.committee.index` | Committee members manager |
| `POST` | `/admin/cms/committee` | `Admin\CommitteeController@store` | `admin.cms.committee.store` | Adds committee member |
| `PUT` | `/admin/cms/committee/{member}` | `Admin\CommitteeController@update` | `admin.cms.committee.update` | Updates committee member |
| `POST` | `/admin/cms/committee/{member}/toggle` | `Admin\CommitteeController@toggle` | `admin.cms.committee.toggle` | Toggles member status |
| `DELETE`| `/admin/cms/committee/{member}` | `Admin\CommitteeController@destroy` | `admin.cms.committee.destroy` | Deletes committee member |
