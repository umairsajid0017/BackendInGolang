<?php

use App\Http\Controllers\Api\CalenderController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SettingsController;
use App\Jobs\MarkBidsAsTimeoutCanceled;
use App\Jobs\MarkBidsAsTimeoutCanceledJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Auth\UserProfileController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Services\ServiceController;
use App\Http\Controllers\Api\Workers\WorkerController;
use App\Http\Controllers\Api\Bookmarks\BookmarkController;
use App\Http\Controllers\Api\Offers\OfferController;
use App\Http\Controllers\Api\Ratings\RatingController;
use App\Http\Controllers\Api\Biddings\BiddingController;
use App\Http\Controllers\Api\Biddings\BidResponseController;
use App\Http\Controllers\Api\Bookings\BookingController;
use App\Http\Controllers\Api\HelpCenterController;
use App\Http\Controllers\Api\LocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ************************** Registration & Login **************************

Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

Route::post('register', [RegisterController::class, 'register']); //working
Route::post('login', [AuthController::class, 'login']); //working
Route::post('sendOTP', [ResetPasswordController::class, 'sendOTP']); //working
Route::post('verifyOtp', [ResetPasswordController::class, 'otpVerify']); //working
Route::post('resetPassword', [ResetPasswordController::class, 'resetPassword']); //working
Route::post('checkCredentials', [AuthController::class, 'checkCredentials']); //working
Route::get('showFAQ', [HelpCenterController::class, 'showFAQ']); //added
Route::post('notificationCallBack', [PaymentController::class, 'notificationCallBack']); //added
Route::get('getAllCities', [LocationController::class, 'getAllCities']); //added

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']); //working

    // ************************** Booking Management **************************

    Route::post('/placeOrder', [BookingController::class, 'directBooking']); //fixed
    Route::get('/bookingsAgainstStatus', [BookingController::class, 'bookingsAgainstStatus']); //fixed
    Route::get('showAllCompletedBookings', [BookingController::class, 'showAllCompletedBookings']); //added
    Route::get('showAllCanceledBookings', [BookingController::class, 'showAllCanceledBookings']); //added
    Route::get('/bookings/upcoming', [BookingController::class, 'upcomingBookingWithUserID']);
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateBookingStatus']);
    Route::get('showActiveBookings', [BookingController::class, 'showActiveBookings']); //added
    Route::post('markAsCancelled', [BookingController::class, 'markAsCancelled']); //added
    Route::post('markAsStart', [BookingController::class, 'markAsStarted']); //added
    Route::post('markAsCompleted', [BookingController::class, 'markAsCompleted']); //added

    // ************************** Worker Management **************************

    Route::get('workers', [WorkerController::class, 'index']);
    Route::get('workerdetails', [WorkerController::class, 'getWorkerProfileDetailsWithID']);
    Route::get('showAllWorkerProfile', [WorkerController::class, 'showAllWorkerProfile']); //added
    Route::post('setWorkerStatus', [WorkerController::class, 'setWorkerStatus']); //added
    Route::patch('updateLocation', [WorkerController::class, 'updateLocation']); //added
    Route::get('checkOnlineWorkers', [WorkerController::class, 'checkOnlineWorkers']); //added
    Route::get('getWorkerAnalytic', [WorkerController::class, 'getWorkerAnalytic']); //added
    Route::get('getWorkerBookingCountMonth', [WorkerController::class, 'getWorkerBookingCountMonth']); //added
    Route::get('getWorkerWithdrawl', [WorkerController::class, 'getWorkerWithdrawl']);
    Route::get('getWorkerStatus', [WorkerController::class, 'getWorkerStatus']);

    // ************************** Service Management **************************

    Route::post('addNewService', [ServiceController::class, 'addNewService']); //Working
    Route::get('showMyServices', [ServiceController::class, 'showMyServices']); //Working
    Route::post('editMyServices', [ServiceController::class, 'editMyServices']);  //Working
    Route::delete('deleteServiceImages', [ServiceController::class, 'deleteServiceImages']);  //added
    Route::delete('deleteMyServices', [ServiceController::class, 'deleteMyServices']); //Working
    Route::get('services/popular', [ServiceController::class, 'popularServices']);
    Route::get('services/popularAgainstCategory', [ServiceController::class, 'popularAgainstCategory']);
    Route::get('servicesAgainstCategory', [ServiceController::class, 'servicesAgainstCategory']);
    Route::get('getSearch', [ServiceController::class, 'search']);
    Route::get('searchAll', [ServiceController::class, 'searchAll']); //added
    Route::get('getSearchHistory', [ServiceController::class, 'getSearchHistory']); //added
    Route::get('getServices', [ServiceController::class, 'getAllServices']); //Working
    Route::get('getServiceDetail', [ServiceController::class, 'getServiceDetail']); //added
    Route::get('getWorkerMultipleServices', [ServiceController::class, 'getWorkerMultipleServices']); //added

    // ************************** Categories Management **************************

    Route::get('getAllCategories', [CategoryController::class, 'getAllCategories']); //added

    // ************************** Bidding Management CUSTOMER **************************

    Route::get('/bids', [BiddingController::class, 'index']);
    Route::get('showPlaceBidsCustomers', [BiddingController::class, 'showPlaceBidsCustomers']); //show place bid to customers
    Route::post('bidCreationCustomer', [BiddingController::class, 'bidCreationCustomer']); //bid creation by customer
    Route::get('showOpenBids', [BiddingController::class, 'showOpenBids']); //response shown to the worker who provide same catogry
    Route::get('getBidDetails', [BiddingController::class, 'getBidDetails']); //bid detail response shown to the worker
    Route::get('getBookingDetails', [BiddingController::class, 'getBookingDetails']); //response shown to the worker and customer
    Route::post('markAsTimeOutCancelled', [MarkBidsAsTimeoutCanceledJob::class, 'markAsTimeOutCancelled']);

    // ************************** Bidding Management WORKER **************************

    Route::post('directAsWorker', [BidResponseController::class, 'directBidAsWorker']); //accept or decline direct as a worker without offering money
    Route::post('bidResponseWorker', [BidResponseController::class, 'bidResponseWorker']); //worker offer money response to the customer bid

    // ************************** Bidding Management CUSTOMER SHOWN RESPONSE **************************

    Route::get('showBidResponseCustomer', [BidResponseController::class, 'showBidResponseCustomer']); // bid responses shown to the customer

    // ************************** Bidding Management CUSTOMER **************************

    Route::post('directAsCustomer', [BidResponseController::class, 'directAsCustomer']); //bid accept or decline direct as a customer without offering money + booking creation + use the api here showActiveBookings
    Route::post('canceledBidCustomer', [BiddingController::class, 'canceledBidCustomer']); //canceled bid as customer

    Route::post('bidAgainOfferCustomer', [BidResponseController::class, 'bidAgainOfferCustomer']); //bid money again offers to general workers + use the API Here showOpenBids api to those workers

    // ************************** Profile Management **************************

    Route::post('updateProfile', [UserProfileController::class, 'updateProfile']); //Working
    Route::put('changePassword', [UserProfileController::class, 'changePassword']); //working
    Route::get('showProfile', [UserProfileController::class, 'showProfile']); //Working

    // ************************** Verification Management **************************

    Route::get('checkAccountVerification', [AuthController::class, 'checkAccountVerification']); //working
    Route::post('addVerificationCnic', [AuthController::class, 'addVerificationCnic']); //added
    Route::post('addVerificationLivePhoto', [AuthController::class, 'addVerificationLivePhoto']); //added
    Route::post('addVerificationPassport', [AuthController::class, 'addVerificationPassport']); //added

    // ************************** Rating Management **************************

    Route::post('addReview', [RatingController::class, 'addReview']); // for customer
    Route::get('getReviews', [RatingController::class, 'getReviews']); // for both

    // Route::post('submitCustomerRating', [RatingController::class, 'submitCustomerRating']); // for worker
    // Route::get('getWorkerSubmitReviews', [RatingController::class, 'getWorkerSubmitReviews']); // for worker


    Route::post('sendFeedback',  [HelpCenterController::class, 'sendFeedback']);

    // ************************** Bookmark Management **************************

    Route::get('showBookmark', [BookmarkController::class, 'showBookmark']); //added
    Route::post('addbookmark', [BookmarkController::class, 'addBookmark']); //added

    // ************************** Offer Management **************************

    Route::get('offers', [OfferController::class, 'index']);
    Route::get('showSpecialOffers', [OfferController::class, 'showSpecialOffers']); //working
    // Route::get('showCustomerOffers', [OfferController::class, 'showCustomerOffers']);

    // ************************** Calender Management Routes **************************

    Route::post('addWorkerAvailability', [CalenderController::class, 'addWorkerAvailability']);//for worker
    Route::get('getWorkerAvailability', [CalenderController::class, 'getWorkerAvailability']);// for worker
    Route::get('getAvailableWorkersByMonth', [CalenderController::class, 'getAvailableWorkersByMonth']); //for customer
    Route::get('getDirectBookingRequest', [CalenderController::class, 'getDirectBookingRequest']); //for worker
    Route::get('getBookingsByDate', [CalenderController::class, 'getBookingsByDate']); //for worker
    Route::post('directBookingAsWorker', [CalenderController::class, 'directBookingAsWorker']); //for worker
    Route::get('getMonthWiseBookings', [CalenderController::class, 'getMonthWiseBookings']); //for both

    Route::get('getDayWiseBookings', [CalenderController::class, 'getDayWiseBookings']); //added

    // ************************** Notifications Routes **************************

    Route::get('showNotifications', [NotificationController::class, 'showNotifications']); //Working
    Route::get('showChatList', [NotificationController::class, 'showChatList']); //added
    Route::post('sendChatNotification',  [NotificationController::class, 'sendChatNotification']); //added
    Route::delete('clearAllNotifications', [NotificationController::class, 'clearAllNotifications']); //Working
    Route::delete('deleteNotification', [NotificationController::class, 'deleteNotification']); //Working
    Route::post('markReadNotification', [NotificationController::class, 'markReadNotification']);  //Working
    Route::get('testNotifications', [NotificationController::class, 'testNotifications']); //added
    Route::post('addNotification',  [NotificationController::class, 'addNotification']); //added
    Route::post('updateToken',  [NotificationController::class, 'updateToken']); //Working


    // ************************** Worker Transactions Route **************************

    Route::get('showWorkerTransaction', [WorkerController::class, 'showWorkerTransaction']); //Added
    Route::post('addWorkerTransaction', [WorkerController::class, 'addWorkerTransaction']); //Added

    // ************************** Customer Transactions Route **************************

    Route::get('showCustomerTransaction', [PaymentController::class, 'showCustomerTransaction']); //Added


    // ************************** Payment Route **************************

    Route::post('calculateCharges', [PaymentController::class, 'calculateCharges']); //added
    Route::get('showPaymentHistory', [PaymentController::class, 'showPaymentHistory']); //added
    Route::get('showAllPaymentMethods', [PaymentController::class, 'showAllPaymentMethods']); //added
    Route::get('showSavedPaymentMethod', [PaymentController::class, 'showSavedPaymentMethod']);
    Route::post('addPaymentMethod', [PaymentController::class, 'addPaymentMethod']);
    Route::patch('updatePaymentMethod', [PaymentController::class, 'updatePaymentMethod']);
    Route::delete('deletePaymentMethod', [PaymentController::class, 'deletePaymentMethod']);
    Route::post('initPaymentGateway', [PaymentController::class, 'initPaymentGateway']);//added
    Route::get('showWalletCredit', [PaymentController::class, 'showWalletCredit']);  //added
    Route::post('deductBookingAmount', [PaymentController::class, 'deductBookingAmount']);  //added

    // ************************** Settings Route **************************

    Route::get('showSettings', [SettingsController::class, 'showSettings']); //added

    // ************************** Language Route **************************

    Route::get('language/{locale}', [LanguageController::class, 'switchLanguage']);
});
