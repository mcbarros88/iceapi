<?php

namespace App\Http\Controllers;

use App\Models\Mountpoint;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Classes\Icecast;
use Illuminate\Support\Facades\Input;
use \Illuminate\Http\JsonResponse;
use \App\Models\Icecast as Icemount;


class IceController extends Controller
{
    public function getIceList() {
        $list = Icemount::all();

        return new JsonResponse($list);
    }
    
    public function ice($id) {
        $config = Icemount::findorfail($id);

        return new JsonResponse($config);
        
    }
    
    public function iceStatus() {
        
    }
    
    public function iceCreate(Icecast $icecast) {

        /* MOUNTPOINT */
        $mountname = Input::get('mount-name');
        $password = Input::get('password');
        $maxListeners = Input::get('max-listeners');
        $bitrate = Input::get('bitrate');


        /* ICECAST */
        $adminUser = Input::get('admin-user');
        $adminMail = Input::get('admin-mail');
        $adminPassword = Input::get('admin-password');
        $port = $this->findFreePort();

        $iceMount = [
            'mount-name' => '/'.$mountname,
            'password' => $password,
            'max-listeners' => $maxListeners,
            'bitrate' => $bitrate,
        ];

        $iceServer = [
            'admin-user' => $adminUser,
            'admin-password' => $adminPassword,
            'admin-mail' => $adminMail,
            'port' => $port
        ];

        $verify = $icecast->createIcecast($iceMount, $iceServer);

        if ($verify == true) {
            $ice = Icemount::create($iceServer);
            Mountpoint::create([
                'mount-name' => '/'.$mountname,
                'password' => $password,
                'max-listeners' => $maxListeners,
                'bitrate' => $bitrate,
                'icecast_id' => $ice->id,
            ]);

            return new JsonResponse([
                'id' => $ice->id
            ]);
        } else {
            return new JsonResponse([
                'success' => 'false',
                'error' => 'Nome Mountpoint già eistente',
            ]);
        }
    }
    
    public function iceEdit($id, Icecast $icecast) {

        $edit=Icemount::findorfail($id);

        /* MOUNTPOINT */
        $mountname = Input::get('mount-name');
        $password = Input::get('password');
        $maxListeners = Input::get('max-listeners');
        $bitrate = Input::get('bitrate');

        /* ICECAST */
        $adminUser = Input::get('admin-user');
        $adminMail = Input::get('admin-mail');
        $adminPassword = Input::get('admin-password');


        $iceMount = [
            'mount-name' => '/'.$mountname,
            'password' => $password,
            'max-listeners' => $maxListeners,
            'bitrate' => $bitrate,
        ];

        $iceServer = [
            'admin-user' => $adminUser,
            'admin-password' => $adminPassword,
            'admin-mail' => $adminMail,

        ];

        $verify = $icecast->createIcecast($iceMount, $iceServer);

        if ($verify == true) {
            $edit -> update(
                array_merge($iceServer,$iceMount)
            );

            return new JsonResponse([
                'id' => $edit->id
            ]);
        } else {
            return new JsonResponse([
                'success' => 'false',
                'error' => 'Nome Mountpoint già eistente',
            ]);
        }


    }
    
    public function iceStart() {
        
    }
    
    public function iceStop() {
        
    }
    
    public function iceDestroy($id, Icecast $icecast)
    {
        $icemount = Icemount::find($id);

        if ($icemount == true) {

            $icemount->mountpoint()->delete();
            
            $icemount->delete();

            $icecast->deleteFolder($icemount->mountName);

            return new JsonResponse([
                'success' => 'true',
                'message' => 'Icecast cancellato con successo',
            ]);
        } else {
            return new JsonResponse([
                'success' => 'false',
                'error' => 'Icecast non cancellato'
            ]);
        }
    }

    /* FIND FREE PORT METHOD */
    public function findFreePort(){

        // Find first available port
        $mountpoints = Icemount::all();
        $quantity = $mountpoints->count();
        $start = config('radio.start-port');
        $end = config('radio.end-port');

        if($quantity && $start+$quantity<=$end){
            $range = range($start, $start+$quantity);
            return collect($range)->diff( $mountpoints->pluck('port') )->min();
        }
        return $start;

    }
}
