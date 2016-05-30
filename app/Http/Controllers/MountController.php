<?php

namespace App\Http\Controllers;

use App\Models\Mountpoint;
use App\Models\Icecast as Icemount;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Classes\Icecast;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;

class MountController extends Controller
{
    public function getMountList() {
        $mountlist = Icemount::all();

    }

    public function mountSingleConfig($id) {
        $singlemount=Mountpoint::findorfail($id);

    }

    public function mountDetail($id) {
        $mountdetail=Mountpoint::findorfail($id);

    }

    public function mountCreate($id, Icecast $icecast)
    {
        $iceid = Icemount::find($id)->id;

        $mountname = Input::get('mount-name');
        $password = Input::get('password');
        $maxListeners = Input::get('max-listeners');
        $bitrate = Input::get('bitrate');

        $iceMount = [
            'mount-name' => '/' . $mountname,
            'password' => $password,
            'max-listeners' => $maxListeners,
            'bitrate' => $bitrate,
            'ice_id' => $iceid,
        ];

        $icecast->addMountPoint($iceMount);

        $mount = Mountpoint::create($iceMount);

        return new JsonResponse([
            'id' => $mount->id
        ]);
    }

    public function mountEdit($id, Request $request) {
        $mountedit=Mountpoint::findorfail($id);
        $mountedit->update($request->all());


    }

    public function mountStart() {

    }

    public function mountStop() {

    }

    public function mountDestroy() {
        
    }
}