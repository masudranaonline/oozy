<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use InvalidArgumentException;
use App\Traits\HasApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\Admin;
use Illuminate\Database\UniqueConstraintViolationException;

class ForgotPasswordController extends Controller
{
  use HasApiResponse;

  public function __invoke(ForgotPasswordRequest $request)
  {
      try {
          $user = Admin::where('email', $request->email)->firstOrFail();
          if (! $user) {
              return response()->json(['message' => 'User not found with this email'], Response::HTTP_NOT_FOUND);
          }
          $token = Str::uuid();

          DB::table('password_reset_tokens')->insert([
              'email' => $request->email,
              'token' => $token,
              'created_at' => now(),
          ]);

          $resetLink = URL::temporarySignedRoute(
              'reset-password',
              now()->addMinutes(10),
              ['token' => $token]
          );

          Mail::raw("Reset your password by clicking on this link: $resetLink OR copy this token $token", function ($message) use ($request) {
              $message->to($request->email);
              $message->subject('Password Reset Link');
              // Log::info('Mail sent to: ' . $request->email);
          });

          return $this->successResponse('Reset password link sent to your email', [], Response::HTTP_OK);
      } catch (UniqueConstraintViolationException $exception) {
          DB::table('password_reset_tokens')->where('email', $request->email)->delete();

          return response()->json(['error' => 'Database server error'], Response::HTTP_INTERNAL_SERVER_ERROR);

      } catch (InvalidArgumentException $exception) {
          DB::table('password_reset_tokens')->where('email', $request->email)->delete();
          // Log::error($exception->getMessage());
          return response()->json(['error' => 'Email sending failed. try again later'], Response::HTTP_INTERNAL_SERVER_ERROR);

      } catch (Exception $exception) {
          DB::table('password_reset_tokens')->where('email', $request->email)->delete();

          return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
      }
  }
}
