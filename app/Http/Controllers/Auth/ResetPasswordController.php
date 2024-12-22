<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HasApiResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\Admin;

class ResetPasswordController extends Controller
{
  use HasApiResponse;

  /**
   * Reset the user password
   *
   * @return JsonResponse
   */
  public function __invoke(ResetPasswordRequest $request)
  {
      $checkToken = DB::table('password_reset_tokens')
          ->where([
              // 'email' => $request->email,
              'token' => $request->token,
          ])
          ->first();

      if (! $checkToken) {
          return $this->errorResponse('Invalid token');
      }

      $user = Admin::query()
          ->where('email', $checkToken->email)
          ->first();
      $user->password = bcrypt($request->password);
      $user->save();

      DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

      return $this->successResponse('Password reset successfully');
  }
}
