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
        $mountname = Input::get('mount_name');
        $password = Input::get('password');
        $maxListeners = Input::get('max_listeners');
        $bitrate = Input::get('bitrate');

        
        /* ICECAST */
        $adminUser = Input::get('admin_user');
        $adminMail = Input::get('admin_mail');
        $adminPassword = Input::get('admin_password');
        $port = $this->findFreePort();

        $mount = [
            'mount_name' => '/'.$mountname,
            'password' => $password,
            'max_listeners' => $maxListeners,
            'bitrate' => $bitrate,
        ];

        $find = Mountpoint::all('mount_name');
        $pippo = $find->search(function ($item, $key) use ($mount){
            return $item['mount_name'] == $mount['mount_name'] ;
        });

        if($pippo === false){
            $ice = Icemount::create([
                'admin_user' => $adminUser,
                'admin_password' => $adminPassword,
                'admin_mail' => $adminMail,
                'port' => $port
            ])->toArray();


            $mount = array_add($mount, 'icecast_id', $ice['id']);
            
            $justcreate = Mountpoint::create($mount)->toArray();
            
            $icecast->createIcecast($justcreate, $ice);

            return new JsonResponse([
                'id' => $ice['id']
            ]);

        } else {
            return new JsonResponse([
                'success' => 'false',
                'error' => 'Nome Mountpoint già eistente',
            ]);
        }
    }

    public function iceEdit($id, Icecast $icecast, Request $request) {

        $edit = Icemount::findorfail($id);

        if ($request->has('admin_user')){
            $edit->admin_user = $request->input('admin_user');
        }
        if ($request->has('admin_password')){
            $edit->admin_password = $request->input('admin_password');
        }
        if ($request->has('admin_mail')){
            $edit->admin_mail = $request->input('admin_mail');
        }
        $edit->save();
        $justupdated=$edit->toArray();
        $icecast->modIcecast($justupdated);



        return new JsonResponse([
            'id' => $edit->id
            ]);
    }
    
    public function iceStart() {
        
    }
    
    public function iceStop() {
        
    }
    
    public function iceDestroy($id, Icecast $icecast)
    {
        $icemount = Icemount::find($id);

        if ($icemount == true) {
            $icecast->deleteFolder($icemount->id);
            $icemount->mountpoint()->delete();
            
            $icemount->delete();

            

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
