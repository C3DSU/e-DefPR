<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;
use DateTime;

class UserController extends Controller
{
    private $itemsPerPage = 10;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request)
    {
        $data = $request->json()->all();

        $user = User::where('email', '=', $data['login'])
            ->orWhere('cpf', '=', $data['login'])
            ->first();

        if (!$user) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        if ($user && Hash::check($data['password'], $user->getAuthPassword())) {
            $token = $user->createToken('auth')->accessToken;

            return JsonResponse::create([
                'token' => $token,
                'name' => $user->name,
                'mustChangePassword' => $user->must_change_password
            ], Response::HTTP_OK);
        }

        return JsonResponse::create([], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $users = User::paginate($this->itemsPerPage);

        return UserResource::collection($users);
    }

    /**
     * @param Request $request
     * @return UserResource|JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user =  new User();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->cpf = $request->input('cpf');
        $birthDate = DateTime::createFromFormat('d/m/Y', $request->input('birth_date'));
        $user->birth_date = $birthDate;
        $user->birth_place = $request->input('birth_place');
        $user->rg = $request->input('rg');
        $user->rg_issuer = $request->input('rg_issuer');
        $user->gender = $request->input('gender');
        $user->marital_status = $request->input('marital_status');
        $user->addresses = json_encode($request->input('addresses'));
        $user->note = $request->input('note');
        $user->profession = $request->input('profession');
        $user->must_change_password = $request->input('mush_change_password') ? $request->input('mush_change_password') : true;

        try {
            $user->saveOrFail();

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $id
     * @return UserResource|JsonResponse
     */
    public function show($id)
    {
        try {
            if (!is_numeric($id)) {
                throw new \Exception($e);
            }
            /** @var User $user */
            $user = User::findOrFail($id);

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return UserResource|JsonResponse
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        try {
            /** @var User $user */
            $user = User::findOrFail($id);

            $user->name = $request->input('name') ? $request->input('name') : $user->name;
            $user->email = $request->input('email') ? $request->input('email') : $user->email;
            $user->password = $request->input('password') ? Hash::make($request->input('password')) : $user->password;
            $user->cpf = $request->input('cpf') ? $request->input('cpf') : $user->cpf;
            $birthDate = DateTime::createFromFormat('d/m/Y', $request->input('birth_date') ? $request->input('birth_date') : $user->birth_date);
            $user->birth_date = $birthDate;
            $user->rg = $request->input('rg') ? $request->input('rg') : $user->rg;
            $user->rg_issuer = $request->input('rg_issuer') ? $request->input('rg_issuer') : $user->rg_issuer;
            $user->gender = $request->input('gender') ? $request->input('gender') : $user->gender;
            $user->marital_status = $request->input('marital_status') ? $request->input('marital_status') : $user->marital_status;
            $user->addresses = $request->input('addresses') ? json_encode($request->input('addresses')) : $user->addresses;
            $user->note = $request->input('note') ? $request->input('note') : $user->note;
            $user->profession = $request->input('profession') ? $request->input('profession') : $user->profession;
            $user->must_change_password = $request->input('must_change_password') ? $request->input('must_change_password') : true;

            $user->saveOrFail();

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param $id
     * @return UserResource|JsonResponse
     */
    public function destroy($id)
    {
        try {
            /** @var User $user */
            $user = User::findOrFail($id);

            $user->delete();

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @return UserResource|JsonResponse
     */
    public function info()
    {
        try {
            $user = Auth::user();

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $email = $request->input('email');
        $cpf = $request->input('cpf');

        $user = User::where('email', '=', $email)
            ->where('cpf', '=', $cpf)
            ->first();

        if ($user) {
            try {
                $user->resetPassword();
    
                return JsonResponse::create([
                    'message' => 'User password reseted with success'
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                return JsonResponse::create([
                    'message' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return JsonResponse::create([
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * @param $id
     * @param $permission
     * @return UserResource|JsonResponse
     */
    public function assignPermission($id, $permission)
    {
        $user = User::findOrFail($id);

        try {
            $user->givePermissionTo($permission);

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $id
     * @param $permission
     * @return UserResource|JsonResponse
     */
    public function unassignPermission($id, $permission)
    {
        $user = User::findOrFail($id);

        try {
            $user->revokePermissionTo($permission);

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $id
     * @param $role
     * @return UserResource|JsonResponse
     */
    public function assignRole($id, $role)
    {
        $user = User::findOrFail($id);

        try {
            $user->assignRole($role);

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $id
     * @param $role
     * @return UserResource|JsonResponse
     */
    public function unassignRole($id, $role)
    {
        $user = User::findOrFail($id);

        try {
            $user->removeRole($role);

            return new UserResource($user);
        } catch (\Exception $e) {
            return JsonResponse::create([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
