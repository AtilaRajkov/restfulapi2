<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $users = User::all();
    //return response()->json(['data' => $users], 200);
    return $this->showAll($users, 200);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = [
      'name' => 'required',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6|confirmed',
    ];

    request()->validate($rules);

    $data = request()->all();
    $data['password'] = bcrypt(request()->password);
    $data['verified'] = User::UNVERIFIED_USER;
    $data['verification_token'] = User::generateVerificationCode();
    $data['admin'] = User::REGULAR_USER;

    $user = User::create($data);

    //return response()->json(['data' => $user], 201);
    return $this->showOne($user, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    // $user = User::findOrFail($id);
    //return response()->json(['data' => $user], 200);
    return $this->showOne($user, 200);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, User $user)
  {
    // $user = User::findOrFail($id);
    $rules = [
      'email' => 'email|unique:users,email,' . $user->id,
      'password' => 'min:6|confirmed',
      'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
    ];

    request()->validate($rules);

    if (request()->has('name')) {
      $user->name = request()->name;
    }

    if (
      request()->has('email') &&
      request('email') !== $user->email
    ) {
      $user->verified = User::UNVERIFIED_USER;
      $user->verification_token = User::generateVerificationCode();
      $user->email = request('email');
    }

    if (request()->has('password')) {
      $user->password = bcrypt(request('password'));
    }

    if (request()->has('admin')) {
      if (!$user->isVerified()) {
        return $this->errorResponse(
          'Only verified users can modify the admin field',
          409);
      }

      $user->admin = request('admin');
    }

    if (!$user->isDirty()) {
      return $this->errorResponse(
        'You need to specify a different value to update',
        409);
    }

    $user->save();

    return response()->json(['data' => $user], 200);
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
    //$user = User::findOrFail($id);
    $user->delete();
    return response()->json(['data' => $user], 200);
  }


  public function verify($token)
  {
    $user = User::where('verification_token', $token)->firstOrFail();

    $user->verified = User::VERIFIED_USER;
    $user->verification_token = null;
    $user->save();

    //return response()->json(['data' => $user], 200);
    return $this->showMessage('The account has been verified successfully.');
  }

  public function resend(User $user)
  {
    if ($user->isVerified()) {
      return $this->errorResponse('This user is already verified', 409);
    }

//    Mail::to($user->email)
//      ->send(new UserCreated($user));

    retry(5, function() use ($user) {
      Mail::to($user->email)
        ->send(new UserCreated($user));
    }, 200);

    //return response()->json(['data' => 'A verification email is sent to you.']);
    return $this->showMessage('The verification email has been sent.');
  }
}
