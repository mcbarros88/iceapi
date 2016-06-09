<?php

namespace App\Http\Controllers;

use App\Models\Mountpoint;
use App\Models\Icecast as Icemount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Classes\Icecast;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;

class MountController extends Controller
{
    public function getMountList() {
        $mountlist = Icemount::all();

        return new JsonResponse($mountlist);

    }

    public function mountSingleConfig($id) {
        $singlemount = Mountpoint::findorfail($id);

        return new JsonResponse($singlemount);

    }

    public function mountDetail($id) {
        $mountdetail = Mountpoint::findorfail($id);

    }

    public function mountCreate($id, Icecast $icecast)
    {
        $iceid = Icemount::find($id)->id;

        $mountname = Input::get('mount_name');
        $password = Input::get('password');
        $max_listeners = Input::get('max_listeners');
        $bitrate = Input::get('bitrate');

        $iceMount = [
            'mount_name' => '/' . $mountname,
            'password' => $password,
            'max_listeners' => $max_listeners,
            'bitrate' => $bitrate,
            'icecast_id' => $iceid,
        ];

        $mount = Mountpoint::create($iceMount)->toArray();

        $newmount = [
            'mount-name' => '/' . $mountname,
            'password' => $password,
            'max-listeners' => $max_listeners,
            'bitrate' => $bitrate
        ];

        $icecast->addMpToIcecast($iceid, $newmount);

        return new JsonResponse([
            'id' => $mount['id']
        ]);
    }

    public function mountEdit($id, Icecast $icecast, Request $request)
    {

        $mountedit = Mountpoint::findorfail($id);

        if ($request->has('max_listeners')) {
            $mountedit->max_listeners = $request->input('max_listeners');
        }

        if ($request->has('password')) {
            $mountedit->password = $request->input('password');
        }

        if ($request->has('bitrate')) {
            $mountedit->bitrate = $request->input('bitrate');
        }

        $mountedit->save();

        /*$idea = DB::table('mountpoints')->where('icecast_id', '1')->get();
        dd($idea);
        foreach ($idea as $mp){
            $newmount = [
                'mount-name' => $mp->mount_name,
                'password' => $mp->password,
                'max-listeners' => $mp->max_listeners,
                'bitrate' => $mp->bitrate
            ];
        }*/

        $newmount = [
            'password' => $mountedit->password,
            'max-listeners' => $mountedit->max_listeners,
            'bitrate' => $mountedit->bitrate
        ];


        $icecast->modifyMountPoint($mountedit->mount_name, $newmount, $mountedit->icecast_id);


        return new JsonResponse([
            'id' => $mountedit->id
        ]);
    }

    public function mountStart() {

    }

    public function mountStop() {

    }

    public function mountDestroy($id, Icecast $icecast) {
        $delete = Mountpoint::find($id);

        if ($delete == true) {
            $delete->delete();
            $icecast->removeMountPoint($delete->mount_name, $delete->icecast_id);
            return new JsonResponse([
                'success' => 'true',
                'message' => 'Mountpoint cancellato con successo',
            ]);
        } else {
            return new JsonResponse([
                'success' => 'false',
                'error' => 'Mountpoint non cancellato'
            ]);
        }
    }
}

