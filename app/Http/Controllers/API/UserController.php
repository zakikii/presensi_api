<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\ResetHistory;
use App\Models\User;
use App\Notifications\ResetEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Return_;

class UserController extends Controller
{
    public function register(Request $req)
    {

        $user = new User();
        $user->name = $req->input('name');
        $user->id_kelas = 0;
        $user->email = $req->input('email');
        $user->level = $req->input('level');
        $user->password = Hash::make($req->input('password'));

        if ($user->save()) {
            if (Auth::attempt(['email' => $req->input('email'), 'password' => $req->input('password')])) {
                return response(['result' => true, 'user' => Auth::user()]);
            }
        }
        return response(['result' => false, 'user' => new User()]);
    }

    public function login(Request $req)
    {
        if (Auth::attempt(['email' => $req->input('email'), 'password' => $req->input('password')])) {
            $user = User::where('email', $req->input('email'))->first();
            if ($user->device_id == null) {
                $user->device_id = $req->input('device_id');
                $user->status = 'in';
                $user->save();
                return response(['result' => true, 'user' => $user]);
            } else {
                if ($user->status == 'in') {
                    return response(['result' => 'on another device', 'message' => 'akun sedang login di perangkat lain']);
                } else {
                    if ($user->device_id != $req->input('device_id')) {
                        if ($user->date_out >= $user->date_can_login) {
                            $user->device_id = $req->input('device_id');
                            $user->status = 'in';
                            $user->save();
                            return response(['result' => true, 'user' => $user]);
                        } else {
                            return response(['result' => 'wait for 3 days', 'message' => 'akun terdeteksi login pada perangkat lain']);
                        }
                    } else {
                        $user->status = 'in';
                        $user->save();
                        return response(['result' => true, 'user' => $user]);
                    }
                }
            }
        }
    }

    public function logoutGuru(Request $req)
    {
        $user = User::where('id', $req->input('user_id'))->first();
        $user->status = 'out';
        if ($user->save()) {
            return response(['result' => true, 'user' => $user]);
        }
        return response(['result' => false]);
    }

    public function logout(Request $req)
    {
        $user = User::where('id', $req->input('user_id'))->first();
        $user->status = 'out';
        $date_out = Carbon::now();
        $date_can_login = Carbon::now()->addDays(3);
        $user->date_out = $date_out;
        $user->date_can_login = $date_can_login;

        if ($user->save()) {
            return response(['result' => true, 'user' => $user]);
        }
        return response(['result' => false]);
    }

    public function enterClass(Request $req)
    {

        $kelas = Kelas::where('kode_kelas', $req->input('kode_kelas'))->first();
        if ($kelas && Auth::attempt(['email' => $req->input('email'), 'password' => $req->input('password')])) {
            if ($kelas->kode_kelas == $req->kode_kelas) {
                $kelas->jumlah_siswa = $kelas->jumlah_siswa + 1;
                $kelas->save();
                $user = User::where('email', $req->email)->first();
                $user->id_kelas = $kelas->id;
                if ($user->save()) {
                    return response(['result' => true, 'user' => $user]);
                }
            }
        }
        return response(['result' => false, new User([])]);
    }


    public function checkAvailablePIN(Request $req)
    {
        $user_id = $req->input('user_id');
        $user = User::find($user_id);

        if ($user->user_pin != 0) {
            return response(['result' => true, 'user' => 'user have a pin, next to the next step']);
        }
        return response(['result' => false, 'user' => 'user dont have a pin, make it first']);
    }

    public function setUserPin(Request $req)
    {
        $user_id = $req->input('user_id');
        $user = User::find($user_id);
        $user->user_pin = $req->input('user_pin');

        if ($user->save()) {
            return response(['result' => true, 'user' => 'pendaftaran pin user berhasil']);
        }
        return response(['result' => false, 'user' => 'user id not found']);
    }

    public function checkUSerPin(Request $req)
    {
        $user_id = $req->input('user_id');
        $user_pin = $req->input('user_pin');
        $user = User::where('id', $user_id)->where('user_pin', $user_pin)->first();

        if ($user) {
            return response(['result' => true, 'user' => 'user ditemukan']);
        }
        return response(['result' => false, 'user' => 'user id not found']);
    }

    public function updateUserName(Request $req)
    {
        $user_id = $req->input('user_id');
        $username = $req->input('user_name');

        $user = User::find($user_id);
        $user->name = $username;

        if ($user->save()) {
            return response(['result' => true, 'user' => $user]);
        }
        return response(['result' => false, 'user' => 'user id not found']);
    }

    public function updateUserEmail(Request $req)
    {
        $user_id = $req->input('user_id');
        $useremail = $req->input('user_email');
        $user = User::find($user_id);
        $user->email = $useremail;

        if ($user->save()) {
            return response(['result' => true, 'user' => $user]);
        }
        return response(['result' => false, 'user' => 'user id not found']);
    }

    public function sendResetEmail(Request $req)
    {
        $user = User::where('email', $req->input('user_email'))->first();
        $resetHistory = new ResetHistory();
        $resetHistory->code = Str::random(5);
        $resetHistory->email = $req->input('user_email');
        $resetHistory->status = 'belum diubah';
        if ($resetHistory->save()) {
            $user->notify(new ResetEmailNotification($user, $resetHistory));
            return response(['result' => true, 'user' => $resetHistory]);
        }
        return response(['result' => false, 'user' => 'gagal menambahkan']);
    }

    public function setNewPassword(Request $req)
    {
        $user_email = $req->input('user_email');
        $code = $req->input('code');
        $reset = ResetHistory::where('email', $user_email)->where('code', $code)->first();
        if ($reset) {
            $reset->status = 'diubah';
            if ($reset->save()) {
                $user = User::where('email', $user_email)->first();
                $user->password = Hash::make($req->input('password'));
                $user->save();
                return response(['result' => true, 'message' => 'password berhasil diubah']);
            }

            return response(['result' => false, 'message' => 'kode tidak sesuai']);
        }
    }
}
