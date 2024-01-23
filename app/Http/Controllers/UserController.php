<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\SideNav;
use App\Models\CustomerLocation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $user = $request->user();
        $result = DB::table('users')
            ->join('user_roles', 'user_roles.user_id', 'users.id')
            ->join('user_role_details', 'user_role_details.id', 'user_roles.user_role_details_id')
            ->join('accesses', 'accesses.user_role_details_id', 'user_role_details.id')
            ->rightJoin('side_navs', 'side_navs.id', 'accesses.side_nav_id')
            ->where('user_roles.user_id', $user->id)
            ->where('user_roles.status', 'active')
            ->whereNull('accesses.deleted_at')
            ->select('side_navs.name', 'side_navs.id')
            ->distinct('side_navs.name')
            ->get();
        $childrenAccess = DB::table('users')
            ->join('user_roles', 'user_roles.user_id', 'users.id')
            ->join('user_role_details', 'user_role_details.id', 'user_roles.user_role_details_id')
            ->join('side_nav_child_accesses', 'side_nav_child_accesses.user_role_details_id', 'user_role_details.id')
            ->join('side_nav_children', 'side_nav_children.id', 'side_nav_child_accesses.side_nav_children_id')
            ->where('user_roles.user_id', $user->id)
            ->select('side_nav_children.name')
            ->get();
        $result = $result->merge($childrenAccess);
        return $result;
    }

    public function GetSideNav(Request $request)
    {
        $user = $request->user();
        $result = DB::table('users')
            ->join('user_roles', 'user_roles.user_id', 'users.id')
            ->join('user_role_details', 'user_role_details.id', 'user_roles.user_role_details_id')
            ->join('accesses', 'accesses.user_role_details_id', 'user_role_details.id')
            ->rightJoin('side_navs', 'side_navs.id', 'accesses.side_nav_id')
            ->where('user_roles.user_id', $user->id)
            ->where('user_roles.status', 'active')
            ->whereNull('accesses.deleted_at')
            ->select('side_navs.name', 'side_navs.id', 'side_navs.mdi_icon', 'side_navs.pic_icon')
            ->distinct('side_navs.name')
            ->get()
            ->each(function ($q) use ($user) {
                $childrenAccess = DB::table('users')
                    ->join('user_roles', 'user_roles.user_id', 'users.id')
                    ->join('user_role_details', 'user_role_details.id', 'user_roles.user_role_details_id')
                    ->join('side_nav_child_accesses', 'side_nav_child_accesses.user_role_details_id', 'user_role_details.id')
                    ->join('side_nav_children', 'side_nav_children.id', 'side_nav_child_accesses.side_nav_children_id')
                    ->where('user_roles.user_id', $user->id)
                    ->where('user_roles.status', 'active')
                    ->pluck('side_nav_children.id')
                    ->toArray();
                $q->side_nav_children = DB::table('side_nav_children')
                    ->where('side_nav_children.parent_side_nav_id', $q->id)
                    ->whereIn('side_nav_children.id', $childrenAccess)
                    ->select('side_nav_children.name', 'side_nav_children.id', 'side_nav_children.mdi_icon', 'side_nav_children.pic_icon')
                    ->get();
            });
        return $result;
    }

    public function GetAllSideNav(Request $request)
    {
        $SideNav = SideNav::all();
        return $SideNav;
    }

    public function Register(Request $request)
    {
        $form = json_decode($request['form'], true);
        $applicantCredential = json_decode($request['applicantCredential'], true);

        $validator = Validator::make($form, [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:user_details',
        ]);

        if ($validator->fails()) {
            $validationError = $validator->errors()->first();
            return $validationError;
        }

        $User = new User();
        $User->username = $form['username'];
        $User->password = bcrypt($form['password']);
        $User->save();

        $UserDetail = new UserDetail();
        $UserDetail->user_id = $User->id;
        $UserDetail->first_name = $form['first_name'];
        $UserDetail->middle_name = $form['middle_name'];
        $UserDetail->last_name = $form['last_name'];
        $UserDetail->gender = $form['gender'];
        // $UserDetail->age = $form['age'];
        $UserDetail->phone_number = $form['phone_number'];
        $UserDetail->address = $form['address'];
        $UserDetail->email = $form['email'];
        $UserDetail->balance = 0;
        $UserDetail->save();

        $UserRole = new UserRole();
        $UserRole->user_id = $User->id;
        $UserRole->user_role_details_id = 2;
        $UserRole->status = 'pending';
        $UserRole->save();

        $CustomerLocation = new CustomerLocation();
        $CustomerLocation->user_role_id = $UserRole->id;
        $CustomerLocation->latitude = $form['latitude'];
        $CustomerLocation->longitude = $form['longitude'];
        $CustomerLocation->address = $form['address'];
        $CustomerLocation->save();

        if ($request->hasFile('files')) {
            $i = 0;
            foreach ($request->file('files') as $file) {
                $file_name = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $name = explode('.', $file_name)[0] . '-' . uniqid() . '.' . $ext;
                $name = str_replace(' ', '', $name);
                $file->move(public_path('applicant_credentials'), $name);

                DB::table('applicant_credentials')->insert([
                    'requirement_details_id' => $applicantCredential[$i]['id'],
                    'user_role_id' => $UserRole->id,
                    'picture_path' => '/applicant_credentials/' . $name,
                    'created_at' => now(), // Set the created_at timestamp to the current date and time
                ]);

                $i++;
            }
        }

        return 'success';
    }

    public function Logout(Request $request)
    {
        $user = $request->user();
        $user->token()->revoke();
        return 'success';
    }

    public function GetUserDetails()
    {
        $userId = Auth::user()->id;
        $userDetail = DB::table('users')
            ->join('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('users.id', '=', $userId)
            ->select(
                'users.username',
                'users.id as user_id',
                'user_details.*',
                DB::raw("CONCAT_WS(' ', user_details.first_name, user_details.middle_name, user_details.last_name) as name")
            )
            ->first();

        if ($userDetail->profile_pic_path != null) {
            $image_type = substr($userDetail->profile_pic_path, -3);
            $image_format = '';
            if ($image_type == 'png' || $image_type == 'jpg') {
                $image_format = $image_type;
            }
            $base64str = '';
            $base64str = base64_encode(file_get_contents(public_path($userDetail->profile_pic_path)));
            $userDetail->base64img = 'data:image/' . $image_format . ';base64,' . $base64str;
        }

        $user_role_details = DB::table('user_roles')
            ->where('user_roles.user_id', $userId)
            ->join('user_role_details', 'user_role_details.id', 'user_roles.user_role_details_id')
            ->select('user_role_details.name', 'user_role_details.id', 'user_roles.status', 'user_roles.id as user_role_id')
            ->get()
            ->each(function ($q) {
                if ($q->id == 3) {
                    $q->store_details = DB::table('stores')
                        ->where('user_role_id', $q->user_role_id)
                        ->select('stores.id as store_id', 'stores.name', 'stores.address')
                        ->get()
                        ->each(function ($q2) {
                            $q2->store_type_details = DB::table('store_types')
                                ->where('store_types.store_id', $q2->store_id)
                                ->join('store_type_details', 'store_type_details.id', 'store_types.store_type_details_id')
                                ->select('store_type_details.name')
                                ->get();
                        });
                }
                if ($q->id == 4) {
                    $q->delivery_details = DB::table('delivery_locations')
                        ->where('user_role_id', $q->user_role_id)
                        ->select('id as delivery_id', 'latitude', 'longitude')
                        ->get();
                }
            });
        $userDetail->user_role_details = $user_role_details;

        $customer_locations = DB::table('customer_locations')
            ->join('user_roles', 'user_roles.id', 'customer_locations.user_role_id')
            ->where('user_roles.user_id', $userId)
            ->select('customer_locations.latitude', 'customer_locations.longitude')
            ->first();
        if ($customer_locations != null) {
            $userDetail->customer_locations = $customer_locations;
        }

        $userDetail->isAdmin = $user_role_details->contains(function($item){
            return $item->id === 1;
        });
        $userDetail->isCustomer = $user_role_details->contains(function($item){
            return $item->id === 2;
        });
        $userDetail->isSeller= $user_role_details->contains(function($item){
            return $item->id === 3;
        });
        $userDetail->isDelivery = $user_role_details->contains(function($item){
            return $item->id === 4;
        });

        return $userDetail;
    }

    public function UpdateUserBalance(Request $request)
    {
        $userId = Auth::user()->id;
        $userDetail = DB::table('users')
            ->join('user_details', 'users.id', 'user_details.user_id')
            ->where('users.id', $userId)
            ->select('users.username', 'users.id as user_id', 'user_details.*')
            ->first();
        DB::table('user_details')
            ->where('user_id', $userId)
            ->update(['balance' => $userDetail->balance + $request['topupAmount']]);
        return 'success';
    }

    public function UpdateUserByUserID(Request $request)
    {
        $form = json_decode($request['data'], true);
        $file = $request->file('file');

        $validator = Validator::make($form, [
            'username' => 'required|unique:users,username,' . $form['user_id'],
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'email' => 'required|unique:user_details,email,' . $form['user_id'],
            'gender' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            $validationError = $validator->errors()->first();
            return $validationError;
        }

        $UserDetail = UserDetail::find($form['user_id']);
        $UserDetail->first_name = $form['first_name'];
        $UserDetail->middle_name = $form['middle_name'];
        $UserDetail->last_name = $form['last_name'];
        $UserDetail->email = $form['email'];
        $UserDetail->address = $form['address'];

        if ($file != null) {
            $file_name = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $name = explode('.', $file_name)[0] . '-' . uniqid() . '.' . $ext;
            $name = str_replace(' ', '', $name);
            $file->move(public_path('ProfilePic'), $name);
            $UserDetail->profile_pic_path = '/ProfilePic/' . $name;
        }

        $UserDetail->save();

        return 'success';
    }

    public function UpdateDeviceToken(Request $request){
        $UserDetail = UserDetail::find(Auth::user()->id);
        $UserDetail->device_token = $request['device_token'];
        $UserDetail->save();
    }

    public function Login(Request $request)
    {
        $credentials = $request->validate([
            'username'    => 'required',
            'password' => 'required'
        ]);
        $login = DB::table('users')
            ->where('username', $request->username)
            ->join('user_roles', 'user_roles.user_id', 'users.id')
            ->select('users.password', 'users.username', 'user_roles.status')
            ->first();

        if ($login) {
            if ($login->status != 'active') {
                return ['message' => 'not active'];
            }
            if (Hash::check($request->password, $login->password)) {
                $passwordGrantClient = Client::where('password_client', 1)->first();
                $response = [
                    'grant_type'    => 'password',
                    'client_id'     => $passwordGrantClient->id,
                    'client_secret' => $passwordGrantClient->secret,
                    'username'      => $request->username,
                    'password'      => $request->password,
                    'scope'         => '*',
                ];
                if (Auth::attempt($credentials)) {
                    $tokenRequest = Request::create('/oauth/token', 'post', $response);
                    $response = app()->handle($tokenRequest);
                    $data = json_decode($response->getContent());
                    $token = $data->access_token;
                    $responseContent = [
                        'message' => 'success',
                        'token' => $token,
                    ];
                    return response()->json($responseContent, 200);
                }
            } else {
                return response()->json(
                    [
                        'message' => 'Incorrect Password.'
                    ],
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'The username were incorrect'
                ],
            );
        }
    }


    public function FIND_USER_WITHIN_RADIUS(Request $request)
    {
        $latitude = $request['latitude'];
        $longitude = $request['longitude'];
        $radiusInMeters = $request['radius'];

        // Convert the radius from meters to kilometers
        $radiusInKm = ($radiusInMeters + 1) / 1000;

        $result = DB::table('customer_locations')
            ->select('*')
            ->selectRaw('( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radiusInKm)
            ->get();

        return $result;
    }

    public function index()
    {
        $users = User::all();
        return $users;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'regex:/^[A-Za-z0-9]+$/', 'unique:users,username'],
            'password' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            return $errorMessages[0];
        }

        $user = new User();
        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return 'success';
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return 'User not found';
        }
        return $user;
    }

    public function update(string $id, Request $request)
    {
        $user = User::where('id', $id)->first();
        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return 'success';
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return 'User not found';
        }
        // Delete the user
        $user->delete();
        return 'success';
    }
}
