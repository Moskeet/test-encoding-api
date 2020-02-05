<?php

namespace App\Http\Controllers;

use App\UserKey;
use Illuminate\Http\Request;
use ParagonIE\EasyRSA\KeyPair;
use ParagonIE\EasyRSA\EasyRSA;
use ParagonIE\EasyRSA\PrivateKey;
use ParagonIE\EasyRSA\PublicKey;

class KeygenController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|max:255',
            'user_public_key' => 'required|min:1',
        ]);
        $key_pair = KeyPair::generateKeyPair(2048);
        $secret_key = $key_pair->getPrivateKey();
        $public_key = $key_pair->getPublicKey();
        if (UserKey::where('username',$request->get('username'))->count() > 0) {
            return response()->json('Please select another username',401);
        }
        UserKey::create([
            'username'   =>  $request->get('username'),
            'user_public_key'   =>  $request->get('user_public_key'),
            'server_public_key' =>  $public_key->getKey(),
            'server_private_key'    => $secret_key->getKey(),
            'message'               =>  null,
        ]);

        return response()->json(null,200);
    }

    public function getServerKey(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|max:255',
            'user_public_key' => 'required',
        ]);
        $key = UserKey::where('username', $request->get('username'))
            ->where('user_public_key', $request->get('user_public_key'))
            ->first();
        if ($key !== null) {
            return response()->json(['server_public_key' => $key->server_public_key],200);
        }

        return response()->json(['Register your username and public_key'], 401);
    }

    public function storeSecret(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|max:255',
            'secret_name' => 'required|max:255',
            'encrypted_secret' => 'required',
            'user_public_key' => 'required',
        ]);
        $key = UserKey::where('username',$request->get('username'))
            ->where('user_public_key',$request->get('user_public_key'))
            ->first();

        if ($key === null) {
            return response()->json(['Register your username and public_key'], 401);
        }

        $signature = EasyRSA::sign($request->get('secret_name'), new PrivateKey($key->server_private_key));
        $publicKey = new PublicKey($key->server_public_key);

        if (EasyRSA::verify($request->get('secret_name'), $signature, $publicKey)) {
            $secret = EasyRSA::encrypt($request->get('encrypted_secret'), $publicKey);
            $key->update([
                'message'   => $secret,
            ]);

            return response()->json(['Your secret encrypted and save!'],200);
        }

        return response()->json(['Register your username and public_key'], 401);
    }

    public function getSecret(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|max:255',
            'secret_name' => 'required|max:255',
            'user_public_key' => 'required',
        ]);
        $key = UserKey::where('username',$request->get('username'))
            ->where('user_public_key',$request->get('user_public_key'))
            ->first();
        if ($key !== null) {
            $publicKey = new PublicKey($key->server_public_key);
            $privateKey = new PrivateKey($key->server_private_key);
            $userPublicKey = new PublicKey($key->user_public_key);
            $signature = EasyRSA::sign($request->get('secret_name'), $privateKey);

            if (EasyRSA::verify($request->get('secret_name'), $signature, $publicKey)) {
                $decrypt_text = EasyRSA::decrypt($key->message, $privateKey);
                $encrypt_text = EasyRSA::encrypt($decrypt_text, $userPublicKey);
                return response()->json(['encrypt_text' => $encrypt_text],200);
            }
            return response()->json(['Register your username and public_key'], 401);
        }

        return response()->json(['Register your username and public_key'], 401);

    }

}
