<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Mail;
use App\Library\Services\EmailService;
use App\Library\Services\SmsService;
use App\Models\Profession;
use App\Models\OTP;
use App\Models\Token;
use Carbon\Carbon;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validate = [
            'type' => 'required',
        ];
        $credentials = ['password' => Arr::get($request, 'password', ' ')];
        if ($request->type == 'google') {
            $validate['name'] = 'required';
            $validate['email'] = 'required|email';
            $validate['id'] = 'required';

            $credentials['first_name'] = Arr::get($request, 'name', ' ');
            $credentials['email'] = Arr::get($request, 'email', ' ');
            $credentials['google_id'] = Arr::get($request, 'id', ' ');
            $credentials['profile_image'] = Arr::get($request, 'avatar', ' ');
        } else if ($request->type == 'facebook') {
            $validate['name'] = 'required';
            $validate['email'] = 'required|email';
            $validate['id'] = 'required';

            $credentials['first_name'] = Arr::get($request, 'name', ' ');
            $credentials['email'] = Arr::get($request, 'email', ' ');
            $credentials['facebook_id'] = Arr::get($request, 'id', ' ');
            $credentials['profile_image'] = Arr::get($request, 'avatar', ' ');
        } else if ($request->type == 'email') {
            $validate['email'] = 'required|email';
            $validate['password'] = 'required|string|min:6';
            $credentials['email'] = Arr::get($request, 'email', ' ');
        } else {
            $validate['phone'] = 'required|string|min:14|max:14';
            $validate['password'] = 'required|string|min:6';
            $credentials['phone'] = Arr::get($request, 'phone', ' ');
        }


        $validator = Validator::make($request->all(), $validate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }


        if ($request->type == 'google' || $request->type == 'facebook') {
            $user_data = User::where('email', $credentials['email']);
            if (!$user_data->exists()) {
                $user_data = new User;
                $user_data->first_name = $credentials['first_name'];
                $user_data->email = $credentials['email'] ?? null;
                if ($request->type == 'google') $user_data->google_id = $credentials['google_id'];
                else if ($request->type == 'facebook') $user_data->facebook_id = $credentials['facebook_id'];
                $user_data->profile_image = $credentials['profile_image'];
                $user_data->user_id = strtolower($request->first_name . "." . rand(10, 1000));
                $user_data->save();
            } else {
                $user_data = $user_data->get()->first();
                if ($request->type == 'google') $user_data->google_id = $credentials['google_id'];
                else if ($request->type == 'facebook') $user_data->facebook_id = $credentials['facebook_id'];
                $user_data->first_name = $credentials['first_name'];
                $user_data->profile_image = $credentials['profile_image'];
                $user_data->save();
            }
            $token = Auth::fromUser($user_data);
        } else {
            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid email or password'], 401);
            }

            if ($request->type == 'email' && !Auth::user()->email_verified) {
                return response()->json([
                    "message" => "Verify your email address before proceeding.",
                    "data" => ["email_verified" => Auth::user()->email_verified, "phone_verified" => Auth::user()->phone_verified]
                ], 400);
            }
            if ($request->type == 'phone' && !Auth::user()->phone_verified) {
                return response()->json([
                    "message" => "Verify your phone number before proceeding.",
                    "data" => ["email_verified" => Auth::user()->email_verified, "phone_verified" => Auth::user()->phone_verified]
                ], 400);
            }
            $user_data = Auth::user();
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user_data
        ]);
    }


    public function register(Request $request)
    {
        if (!$request->email && !$request->phone) {
            return response()->json([
                'message' => 'Phone Number or Email is required'
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'phone' => 'string|min:14|max:14|unique:users',
            'email' => 'string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['user_id' => strtolower($request->first_name . "." . $request->last_name . "." . rand(1, 10))],
            ['email' => $request->email ?? null],
            ['phone' => $request->phone ?? null],
            ['password' => bcrypt($request->password)]
        ));
        $token = Auth::fromUser($user);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function send_otp(Request $request, SmsService $sms)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }

        $user = User::where('phone', $request->phone);
        if (!$user->exists()) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $user = $user->get()->first();


        $data = [
            "id" => $user->id,
            "phone" => $user->phone,
            "phone_verified" => $user->phone_verified
        ];
        if ($user->phone_verified) {
            return response()->json([
                "message" => "Phone Number is already verified"
            ], 404);
        }
        //Send OTP
        $otp = rand(100000, 999999);
        $current = Carbon::now();
        $otp_expires_in = $current->addMinutes(15);

        $sms->sendOTP($user, $otp);
        $otp_data = OTP::where('user_id', $user->id);
        $otp_data->delete();

        $new_otp = new OTP;
        $new_otp->phone = $request->phone;
        $new_otp->otp = $otp;
        $new_otp->expires_in = $otp_expires_in;
        $new_otp->user_id = $user->id;
        $new_otp->save();
        return response()->json([
            "message" => "OTP has been sent to your phone number.",
            "data" => $data
        ], 200);
    }

    public function confirm_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }

        $user = User::where('phone', $request->phone);
        if (!$user->exists()) {
            return response()->json([
                "message" => "Phone number not registered"
            ], 404);
        }
        $user = $user->get()->first();


        $data = [
            "id" => $user->id,
            "phone" => $user->phone,
            "phone_verified" => $user->phone_verified
        ];
        if ($user->phone_verified) {
            return response()->json([
                "message" => "Phone Number is already verified"
            ], 400);
        }
        $otp = OTP::where(['phone' => $request->phone, 'otp' => $request->otp]);
        if (!$otp->exists()) {
            return response()->json([
                "message" => "Invalid OTP",
                "data" => $data
            ], 400);
        }
        $otp = $otp->get()->first();
        $current = Carbon::now();
        if ($current->isAfter($otp->expires_in)) {
            return response()->json([
                "message" => "OTP has expired",
                "data" => $data
            ], 400);
        }
        if ($otp->used) {
            return response()->json([
                "message" => "OTP used",
                "data" => $data
            ], 400);
        }
        $user->phone_verified = true;
        $user->save();

        $otp->used = true;
        $otp->save();
        $data['phone_verified'] = $user->phone_verified;
        return response()->json([
            "message" => "OTP confirmed",
            "data" => $data
        ], 200);
    }

    public function send_email(Request $request, EmailService $email)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }

        $user = User::where('email', $request->email);
        if (!$user->exists()) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $user = $user->get()->first();

        $data = [
            "id" => $user->id,
            "email" => $user->email,
            "email_verified" => $user->email_verified
        ];
        if ($user->email_verified) {
            return response()->json([
                "message" => "Email is already verified"
            ], 400);
        }
        //Send Email OTP
        $token = rand(100000, 999999);
        $current = Carbon::now();
        $expires_in = $current->addHours(24);
        $email->emailVerification($user, $token);


        $token_data = Token::where(['user_id' => $user->id, 'action' => 'email_verification']);
        $token_data->delete();

        $new_token = new Token;
        $new_token->email = $request->email;
        $new_token->token = $token;
        $new_token->expires_in = $expires_in;
        $new_token->action = 'email_verification';
        $new_token->user_id = $user->id;
        $new_token->save();

        return response()->json([
            "message" => "Verification link sent to your email.",
            "data" => $data
        ], 200);
    }

    public function confirm_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }
        $token = Token::where(['token' => $request->token, 'action' => 'email_verification']);

        if (!$token->exists()) {
            return response()->json([
                "message" => "Invalid token was provided"
            ], 400);
        }
        $token = $token->get()->first();
        $current = Carbon::now();
        if ($current->isAfter($token->expires_in)) {
            return response()->json([
                "message" => "Token has expired",
                "data" => []
            ], 400);
        }
        if ($token->used) {
            return response()->json([
                "message" => "Token used"
            ], 400);
        }

        $user = User::where('id', $token->user_id);
        if (!$user->exists()) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $user = $user->get()->first();

        if ($user->email_verified) {
            return response()->json([
                "message" => "Email is already verified"
            ], 400);
        }
        $user->email_verified = true;
        $user->save();

        $token->used = true;
        $token->save();

        $data = [
            "id" => $user->id,
            "email" => $user->email,
            "email_verified" => $user->email_verified
        ];
        return response()->json([
            "message" => "Email verified",
            "data" => $data
        ], 200);
    }

    public function password_reset_email(Request $request, EmailService $email)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }

        $user = User::where('email', $request->email);
        if (!$user->exists()) {
            return response()->json([
                "message" => "Email not found"
            ], 404);
        }
        $user = $user->get()->first();

        //Send Email
        $token = rand(100000, 999999);
        $current = Carbon::now();
        $expires_in = $current->addHours(24);

        $email->passwordReset($user, $token);

        $token_data = Token::where(['user_id' => $user->id, 'action' => 'password_reset']);
        $token_data->delete();

        $new_token = new Token;
        $new_token->email = $request->email;
        $new_token->token = $token;
        $new_token->expires_in = $expires_in;
        $new_token->action = 'password_reset';
        $new_token->user_id = $user->id;
        $new_token->save();
        return response()->json([
            "message" => "Password reset email sent.",
            "data" => null
        ], 200);
    }

    public function reset_password_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }
        $token = Token::where(['token' => $request->token, 'action' => 'password_reset']);

        if (!$token->exists()) {
            return response()->json([
                "message" => "Invalid token was provided"
            ], 400);
        }
        $token = $token->get()->first();
        $current = Carbon::now();
        if ($current->isAfter($token->expires_in)) {
            return response()->json([
                "message" => "Token has expired",
                "data" => []
            ], 400);
        }
        if ($token->used) {
            return response()->json([
                "message" => "Token used"
            ], 400);
        }
        $user = User::where('id', $token->user_id);
        if (!$user->exists()) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $user = $user->get()->first();

        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "Can not update password to old password"
            ], 400);
        }
        $user->password = bcrypt($request->password);
        $user->save();

        $token->used = true;
        $token->save();

        return response()->json([
            "message" => "Password updated",
            "data" => null
        ], 200);
    }

    public function password_reset_phone(Request $request, SmsService $sms)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }

        $user = User::where('phone', $request->phone);
        if (!$user->exists()) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $user = $user->get()->first();

        //Send Email
        $otp = rand(100000, 999999);
        $current = Carbon::now();
        $otp_expires_in = $current->addMinutes(15);

        $sms->sendOTP($user, $otp);
        $otp_data = OTP::where('user_id', $user->id);
        $otp_data->delete();

        $new_otp = new OTP;
        $new_otp->phone = $request->phone;
        $new_otp->otp = $otp;
        $new_otp->expires_in = $otp_expires_in;
        $new_otp->user_id = $user->id;
        $new_otp->save();
        return response()->json([
            "message" => "Password reset otp sent.",
            "data" => null
        ], 200);
    }

    public function reset_password_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }

        $user = User::where('phone', $request->phone);
        if (!$user->exists()) {
            return response()->json([
                "message" => "Phone number not registered"
            ], 404);
        }
        $user = $user->get()->first();

        $otp = OTP::where(['phone' => $request->phone, 'otp' => $request->otp]);
        if (!$otp->exists()) {
            return response()->json([
                "message" => "Invalid OTP",
                "data" => null
            ], 400);
        }
        $otp = $otp->get()->first();
        $current = Carbon::now();
        if ($current->isAfter($otp->expires_in)) {
            return response()->json([
                "message" => "OTP has expired",
                "data" => null
            ], 400);
        }
        if ($otp->used) {
            return response()->json([
                "message" => "OTP used",
                "data" => null
            ], 400);
        }
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "Can not update password to old password"
            ], 400);
        }
        $user->password = bcrypt($request->password);
        $user->save();

        $otp->used = true;
        $otp->save();

        return response()->json([
            "message" => "Password updated",
            "data" => null
        ], 200);
    }

    public function update_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }
        $user = User::where('id', Auth::user()->id);
        if (!$user->exists()) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $user = $user->get()->first();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                "message" => "Old password entered is invalid"
            ], 400);
        }
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                "message" => "Old password password used. Enter a new password."
            ], 400);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json([
            "message" => "Password updated",
            "data" => null
        ], 200);
    }

    public function google(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson()], 422);
        }
        // $user = Socialite::driver('google')->userFromToken($request->access_token);
        // $user_data = User::where('email', $user->email);
        // if (!$user_data->exists()) {
        //     $user_data = new User;
        //     $user_data->first_name = $user->name;
        //     $user_data->email = $user->email;
        //     $user_data->google_id = $user->id;
        //     $user_data->profile_image = $user->avatar;
        //     $user_data->save();
        // } else {
        //     $user_data = $user_data->get()->first();
        //     $user_data->google_id = $user->id;
        //     $user_data->profile_image = $user->avatar;
        //     $user_data->save();
        // }

        // $user->getId();
        // $user->getNickname();
        // $user->getName();
        // $user->getEmail();
        // $user->getAvatar();

        // $token = Auth::fromUser($user_data);
        return response()->json([
            'message' => 'Google auth successful',
        ], 201);
    }

    public function userdetails(Request $request)
    {
        $user_details = User::where('user_id', auth()->user()->user_id)->first();
        return response()->json(compact('user_details'));
    }


    public function updatebio(Request $request)
    {



        $data = [

            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'disabled' => $request->disabled,
            'educational_qualification' => $request->educational_qualification,
            'other_professions1' => $request->other_professions1,
            'other_professions2' => $request->other_professions2,
            'other_professions3' => $request->other_professions3,
            'other_professions4' => $request->other_professions4,
            'languages1' => $request->languages1,
            'languages2' => $request->languages2,
            'languages3' => $request->languages3,
            'languages4' => $request->languages4,
            'languages5' => $request->languages5,
            'current_employer' => $request->current_employer,
            'state' => $request->state,
            'lga' => $request->lga,
            'employment_type' => $request->employment_type,
            'preferred_job' => $request->preferred_job,
            'preferred_job_location_state' => $request->preferred_job_location_state,
            'preferred_job_location_lga' => $request->preferred_job_location_lga,
            'availability_start_date' => $request->availability_start_date,
        ];


        $updated = User::where('user_id', auth()->user()->user_id)->update($data);

        return response()->json(compact('updated'));
    }


    public function profilesetup(Request $request)
    {


        $user_details = User::where('profile_id', strtolower($request->first_name . '.' . $request->last_name))->first();
        if (empty($user_details)) {
            $profile_id = strtolower($request->first_name . '.' . $request->last_name);
        } else {
            //user ID already exists. Add 1 to it
            $id2 = $user_details['id'] + 3;
            $profile_id = $user_details['profile_id'] . '.' . $id2;
        }

        $data = [

            'first_name'  => $request->first_name,
            'last_name'  => $request->last_name,
            'profile_id'  => $profile_id,
            'phone'  => $request->user_phonenum,
            'email_profile_setup'  => $request->user_email,
            //'age_range'  => $request->age_range,
            'marital_status' => $request->marital_status,
            'profession'  => $request->profession,
            'employment_type'  => $request->employment_type,
            'employment_status'  => $request->employment_status,
            'educational_qualification'  => $request->educational_qualification,
            'gender' => $request->gender,
            'disabled' => $request->disabled,
            'current_employer' => $request->current_employer,
            'languages1' => $request->languages1,
            'languages2' => $request->languages2,
            'languages3' => $request->languages3,
            'languages4' => $request->languages4,
            'languages5' => $request->languages5,
            'other_professions1' => $request->other_professions1,
            'other_professions2' => $request->other_professions2,
            'other_professions3' => $request->other_professions3,
            'other_professions4' => $request->other_professions4,
            'state'  => $request->selectedState,
            'lga'  => $request->selectedLGA,
            'preferred_job_location_state'  => $request->selectedState2,
            'preferred_job_location_lga'  => $request->selectedLGA2,
            'about' => $request->about,
            'active'  => 1,

        ];



        $updated = User::where('user_id', auth()->user()->user_id)->update($data);

        return response()->json(compact('updated'));
    }

    public function changeprofilepic(Request $request)
    {
        if ($request->hasFile('profilepic')) {
            // $profilepic_name = time().uniqid().'.'.$request->profilepic->extension();
            // // $fileSize = $request->atm_card_file_name->getClientSize();
            // $request->profilepic->move(public_path('uploads/userprofilepics'), $profilepic_name);

            $file = $request->file('profilepic');
            $profilepic_name = time() . uniqid() . '.' . $request->profilepic->extension();
            $file->storeAs('userprofilepics', $profilepic_name, 's3');


            $data = [

                'profile_image' => $profilepic_name,
            ];

            $uploaded = User::where('user_id', auth()->user()->user_id)->update($data);

            return response()->json([
                "success" => true,
                "message" => "Profile Image successfully uploaded",
            ]);
        }
    }

    public function searchprofessions($term)
    {

        $professions = Profession::where('name', 'LIKE', '%' . $term . '%')->where('parent_id', '!=', 0)->get();


        $related_professions = array();

        for ($i = 0; $i < count($professions); $i++) {

            $related_professions[] = Profession::where('parent_id', $professions[$i]->parent_id)->get();
        }


        return response()->json($related_professions);

        //var_dump($teams);
    }
}
