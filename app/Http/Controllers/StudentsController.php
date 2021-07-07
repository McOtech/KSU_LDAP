<?php

namespace App\Http\Controllers;

use App\Http\Traits\ADUser;
use Illuminate\Http\Request;
use App\Http\Traits\Utils;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StudentsController extends Controller
{
  use Utils, ADUser;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    try {
      $data = $request->validate([
        'regno' => ['required', 'string'],
        'email' => ['required', 'email'],
        'mobile' => ['required', 'string', 'max:20'],
        'password' => ['required', 'confirmed', 'string']
      ]);

      // Check to ascertain student records
      $student = Http::post(env('USER_VERIFICATION_URL') . "/students", [
        "regno" => $data['regno']
      ]);

      if ($student) {
        $groups = [env('LDAP_STUDENT_OU')];
        $user = $this->prepareUserAccountDetails(
          $this->preparedStringLiteral($student['fname']),
          $this->preparedInitial($student['lname']),
          $this->preparedStringLiteral($student['sname']),
          strtoupper($student['regno']),
          $data['password'],
          strtolower($data['email']),
          $data['mobile'],
          $groups,
          []
        );

        if (!isset($user[env('MESSAGE_LITERAL')])) {
          return response()->json($this->addUser($user['cn'], $user, $groups));
        } else {
          return response()->json($user);
        }
      } else {
        return response()->json($this->alert(env('WARNING_MESSAGE'), "Invalid Registration Number! Please check and try again."));
      }
    } catch (\Throwable $th) {
      return response()->json($this->alert(env('ERROR_MESSAGE'), $th->getMessage()));
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}