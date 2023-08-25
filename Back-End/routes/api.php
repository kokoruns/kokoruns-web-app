<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\UserDataController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('send_otp', [UserController::class, 'send_otp']);
Route::post('confirm_otp', [UserController::class, 'confirm_otp']);
Route::post('send_email', [UserController::class, 'send_email']);
Route::post('confirm_email', [UserController::class, 'confirm_email']);
Route::post('password_reset/email', [UserController::class, 'password_reset_email']);
Route::post('reset_password/email', [UserController::class, 'reset_password_email']);
Route::post('password_reset/phone', [UserController::class, 'password_reset_phone']);
Route::post('reset_password/phone', [UserController::class, 'reset_password_phone']);
Route::post('login/google', [UserController::class, 'google']);
Route::get('open', [UserDataController::class, 'open']);
// Route::get('addexperience', [DataController::class, 'addexperience']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('states', [UserDataController::class, 'states']);
    Route::get('lgas', [UserDataController::class, 'lgas']);
    Route::patch('update_password', [UserController::class, 'update_password']);
    Route::get('closed', [UserDataController::class, 'closed']);
    Route::post('addexperience', [UserDataController::class, 'addexperience']);
    Route::get('experience/{id}/', [UserDataController::class, 'experience']);
    Route::put('updateexperience/{id}/', [UserDataController::class, 'updateexperience']);
    Route::delete('deleteexperience/{id}/', [UserDataController::class, 'deleteexperience']);
    Route::get('experiences', [UserDataController::class, 'experiences']);
    Route::post('addeducation', [UserDataController::class, 'addeducation']);
    Route::get('educations', [UserDataController::class, 'educations']);
    Route::get('education/{id}/', [UserDataController::class, 'education']);
    Route::put('updateeducation/{id}/', [UserDataController::class, 'updateeducation']);
    Route::delete('deleteeducation/{id}/', [UserDataController::class, 'deleteeducation']);
    Route::post('addportfolio', [UserDataController::class, 'addportfolio']);
    Route::get('portfolios', [UserDataController::class, 'portfolios']);
    Route::post('adddocument', [UserDataController::class, 'adddocument']);
    Route::get('documents', [UserDataController::class, 'documents']);
    Route::post('addproskill', [UserDataController::class, 'addproskill']);
    Route::get('proskills', [UserDataController::class, 'proskills']);
    Route::post('addotherskill', [UserDataController::class, 'addotherskill']);
    Route::get('otherskills', [UserDataController::class, 'otherskills']);
    Route::post('addonlinelink', [UserDataController::class, 'addonlinelink']);
    Route::get('onlinelinks', [UserDataController::class, 'onlinelinks']);
    Route::get('userdetails', [UserController::class, 'userdetails']);
    Route::get('searchprofessions/{id}/', [UserController::class, 'searchprofessions']);
    Route::post('updatebio', [UserController::class, 'updatebio']);
    Route::post('profilesetup', [UserController::class, 'profilesetup']);
    Route::post('changeprofilepic', [UserController::class, 'changeprofilepic']);
    Route::post('createteam', [UserDataController::class, 'createteam']);
    Route::get('teams', [UserDataController::class, 'teams']);
    Route::post('createuserjob', [UserDataController::class, 'createuserjob']);
    Route::get('userjobs', [UserDataController::class, 'userjobs']);
    Route::post('cregister', [CompanyController::class, 'cregister']);
    Route::get('companies', [CompanyController::class, 'companies']);
    Route::get('company/{id}/', [CompanyController::class, 'companydetails']);
    Route::post('updatecompanyinfo/{id}/', [CompanyController::class, 'updatecompanyinfo']);
    Route::post('makecompanyadmin/{id}/', [CompanyController::class, 'makecompanyadmin']);
    Route::post('removecompanyadmin/{id}/', [CompanyController::class, 'removecompanyadmin']);
    Route::post('updatecompanyabout/{id}/', [CompanyController::class, 'updatecompanyabout']);
    Route::post('createcompanybranch/{id}/', [CompanyController::class, 'createcompanybranch']);
    Route::get('companybranches/{id}/', [CompanyController::class, 'companybranches']);
    Route::post('updatecompanysocials/{id}/', [CompanyController::class, 'updatecompanysocials']);
    Route::post('createcompanyevent/{id}/', [CompanyController::class, 'createcompanyevent']);
    Route::get('companyevents/{id}/', [CompanyController::class, 'companyevents']);
    Route::post('addcompanygallery/{id}/', [CompanyController::class, 'addcompanygallery']);
    Route::get('companygalleries/{id}/', [CompanyController::class, 'companygalleries']);
    Route::post('createcompanyebroadcast/{id}/', [CompanyController::class, 'createcompanyebroadcast']);
    Route::get('companyebroadcasts/{id}/', [CompanyController::class, 'companyebroadcasts']);
    Route::post('aregister', [AssociationController::class, 'aregister']);
    Route::get('associations', [AssociationController::class, 'associations']);
    Route::get('association/{id}/', [AssociationController::class, 'associationdetails']);
    Route::post('updateassociationinfo/{id}/', [AssociationController::class, 'updateassociationinfo']);
    Route::post('makeassociationadmin/{id}/', [AssociationController::class, 'makeassociationadmin']);
    Route::post('removeassociationadmin/{id}/', [AssociationController::class, 'removeassociationadmin']);
    Route::post('updateassociationabout/{id}/', [AssociationController::class, 'updateassociationabout']);
    Route::post('createassociationbranch/{id}/', [AssociationController::class, 'createassociationbranch']);
    Route::get('associationbranches/{id}/', [AssociationController::class, 'associationbranches']);
    Route::post('updateassociationsocials/{id}/', [AssociationController::class, 'updateassociationsocials']);
    Route::post('createassociationevent/{id}/', [AssociationController::class, 'createassociationevent']);
    Route::get('associationevents/{id}/', [AssociationController::class, 'associationevents']);
    Route::post('addassociationgallery/{id}/', [AssociationController::class, 'addassociationgallery']);
    Route::get('associationgalleries/{id}/', [AssociationController::class, 'associationgalleries']);
    Route::post('createassociationebroadcast/{id}/', [AssociationController::class, 'createassociationebroadcast']);
    Route::get('associationebroadcasts/{id}/', [AssociationController::class, 'associationebroadcasts']);
    
    
});