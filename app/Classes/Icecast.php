<?php
/**
 * Created by IntelliJ IDEA.
 * User: massimo
 * Date: 12/05/16
 * Time: 9.37
 */

namespace App\Classes;

use App\Models\Mountpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Array_;
use Sabre;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use \Illuminate\Http\JsonResponse;


class Icecast
{



    private $xmlTemplatePath;
    private $port;
    private $iceconfig;
    private $icepath;

    private $xmlTemplate;
    private $mountTemplate ;

    private $xml;
    private $mount;

    public function __construct()
    {
        $this->templatePath = 'icecast/template';
        $this->port = config('radio.port');
        $this->icepath = config('radio.root');
        $this->mountpath = 'icecast/mountpoint';

        $this->init();

    }

    public function init(){

        $this->xmlTemplate = $this->getXmlTemplate();
        $this->mountTemplate = $this->getMountTemplate();

        $this->xml = $this->xmlTemplate;
        $this->mount = $this->mountTemplate;
        $this->setHostname(config('radio.hostname'));

    }

    // Get default Icecast xml blueprint
    public function getXmlTemplate(){

        $xml = Storage::get($this->templatePath.'/icecast.xml');
        return $this->xmltoArray($xml);

    }

    // Get default Mountpoint xml blueprint
    public function getMountTemplate(){

        $xml = Storage::get($this->templatePath.'/mount.xml');
        return $this->xmltoArray($xml);

    }

    // Get Current xml By mount id
    public function getXml($name){

        $xml = Storage::get('icecast/'.$name.'/'.$name.'.xml');
        return $this->xmltoArray($xml);

    }

    // Convert raw xml to array
    public function xmltoArray ($xml){

        $reader = new Sabre\Xml\Reader();
        $reader->xml($xml);
        return $reader->parse();

    }

    // Convert array to Xml
    public function arrayToXml($xml){

        // Init writer
        $writer = new Sabre\Xml\Writer();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument();
        $writer->write($xml);

        return $writer->outputMemory();

    }

    // Save single icecast xml
    public function saveXml($name, $content){

        $file = 'icecast/'.$name.'/'.$name.'.xml';
        Storage::put($file, $content);

    }

    // Return index for a given name( only root level of provided RAW xml )
    public function searchName($name , $xml){

        // Return index of '$name' value
        return collect($xml['value'])->search( function($element) use ($name) {
            return ($element['name'] == '{}'.$name);
        });

    }

    /**********************
     *  Mount point
     **********************/

    // Modifiy the current mountpoint template
    private function modifyMountTemplate ($name, $newProp) {

        // Search new Mountpoint template for property '$name' index
        $index = $this->searchName($name, $this->mount);

        // Change value
        $this->mount['value'][$index]['value'] = $newProp;

    }
    public function modIcecast(Array $icecast){

        $this->xml = $this->getXml($icecast['id']);

        $this->setAdmin($icecast['admin_mail'], $icecast['admin_user'], $icecast['admin_password']);

        $updateXml = $this->arrayToXml($this->xml);
        $this->saveXml($icecast['id'], $updateXml);

    }
    public function createIcecast(Array $mount , Array $icecast){

        /*if( isset( $mount['id']) ){

            $mountExist = $this->folderExist('icecast/mountpoint'.$mount['id']);

            if(!$mountExist) {*/

                $this->createFilesAndFolders($mount['id']);

                // Modify Icecast properties
                $this->setPaths($mount['mount-name']);
                $this->setPort($icecast['port']);
                $this->setAdmin($icecast['admin_mail'], $icecast['admin_user'], $icecast['admin_password']);

                // Add mountpoint
                $this->addMountPoint($mount);

                $newXml = $this->arrayToXml($this->xml);
                $this->saveXml($mount['id'], $newXml);

                return true;
            /*}
            else {
                return false;
            }

        }*/
    }


    // Add Mountpoint to xml
    public function addMountPoint (Array $array){

        // Modify mount standard template
        foreach ($array as $key => $value){
            $this->modifyMountTemplate($key, $value);
        }

        // Add mount to xml
        $index  = count( $this->xml['value'] );
        $this->xml['value'][$index] = $this->mount;


    }

    public function addMpToIcecast($iceid, Array $mount ){

        $this->xml = $this->getXml($iceid);
        $this->addMountPoint($mount);

        $updateXml = $this->arrayToXml($this->xml);
        $this->saveXml($iceid, $updateXml);

    }

    // Return index of a mountpoint by name
    public function findMountpoint($name){

        $icecastProp = $this->xml['value'];

        foreach ($icecastProp as $index => $value) {

            // Search only mountpoint tag
            if( $value['name'] == '{}mount' ){

                //  Cylce trough sigle prop
                foreach($value['value'] as $item){

                    if($item['name'] == '{}mount-name' && $item['value'] == $name){
                        return $index;
                    };
                }
            };
        }

        return false;

    }

    // Remove Mountpoint with a given name property
    public function removeMountPoint($name, $iceid){

        $this->xml = $this->getXml($iceid);

        if( $index = $this->findMountpoint($name) )
        {
            // Delete mounpoint
            unset($this->xml['value'][$index]);
            $updateXml = $this->arrayToXml($this->xml);
            $this->saveXml($iceid, $updateXml);

        }
        return false;
    }

    // Modify Mountpoint by name
    public function modifyMountPoint ($name, Array $arr, $iceid){

        $this->xml = $this->getXml($iceid);

        if( $mountIndex = $this->findMountpoint($name) ){

            foreach ($arr as $propName => $propValue) {

                // if key exist
                $propIndex = $this->findOne( $this->xml['value'][$mountIndex], $propName);

                if( $propIndex == 0 || $propIndex ){
                    $this->xml['value'][$mountIndex]['value'][$propIndex]['value'] = $propValue;
                };

            }
            $updateXml = $this->arrayToXml($this->xml);
            $this->saveXml($iceid, $updateXml);

        };

    }

    /******************
     *  Icecast
     * *****************/

    public function startIcecast($name){

        $descriptorspec = array(array("pipe", "r"), array("pipe", "w"), array("pipe", "a"));
        $process = proc_open(' icecast2 -c '.$this->icepath.'/mountpoint/'.$name.'/'.$name.'.xml', $descriptorspec, $pipes);
        $status = proc_get_status($process);

        $pid = $status['pid'];

        $this->savePid($name ,$pid);

        return $pid;

    }

    public function stopIcecast($name){

        if( !$this->getAllIcecastPid() )
            return true;

        $pid = $this->getPid($name);

        $this->stopIcecastByPid($pid);

    }
    public function stopIcecastByPid($pid){

        exec('pgrep -P '.$pid.' | xargs kill');
        exec('kill '.$pid.'');

        while( $this->icecastStatusByPid($pid)){
            exec('pgrep -P '.$pid.' | xargs kill');
            exec('kill '.$pid);
        };

    }

    public function stopAllIcecast(){

        $pids = $this->getAllIcecastPidArray();

        if($pids){
            foreach ($pids as $pid){
                $this->stopIcecastByPid($pid);
            }
        }

    }

    public function getAllIcecastPid (){
        $results = '';
        exec('pidof icecast2', $results);

        if( count($results)>0){
            return $results[0];
        }

        return false;

    }

    public function getAllIcecastPidArray (){
        if( $pids = $this->getAllIcecastPid() ){
            return explode(' ', $pids);
        }
        return false;
    }

    public function getPid($name){

        return Storage::get($this->mountpath.'/'.$name.'/pid.txt');

    }

    public function savePid($name, $pid){

        return Storage::put($this->mountpath.'/'.$name.'/pid.txt', $pid);

    }

    public function icecastStatus($name){

        $pid = $this->getPid($name);
        return $this->icecastStatusByPid($pid);

    }
    public function icecastStatusByPid($pid){

        $pids = $this->getAllIcecastPidArray();

        if(is_array($pids) && count($pids))
            return in_array($pid, $pids);
        else return false;

    }

    public function setPort ($port){

        $index = $this->findOne($this->xml, 'listen-socket');
        $this->xml['value'][$index]['value'][0]['value'] = $port;

    }

    public function setAdmin($email, $username, $password){

        $index = $this->findOne($this->xml, 'authentication');
        $authentication = $this->xml['value'][$index];

        $adminMail = $this->findOne($this->xml, 'admin');
        $adminUser = $this->findOne($authentication, 'admin-user');
        $adminPwd = $this->findOne($authentication, 'admin-password');

        $this->xml['value'][$adminMail]['value'] = $email;
        $this->xml['value'][$index]['value'][$adminUser]['value'] = $username;
        $this->xml['value'][$index]['value'][$adminPwd]['value'] = $password;
//        dd( $adminUser, $adminPwd, $adminMail, $this->xml);
    }

    public function setPaths ($name){

        $array = [
            'basedir' => $this->icepath.'/mountpoint'.$name,
            'logdir' =>  $this->icepath.'/mountpoint'.$name.'/log',
            'webroot' => $this->icepath.'/web'
        ];

        $pathsIndex = $this->findOne($this->xml, 'paths');
        $paths = $this->xml['value'][$pathsIndex];

        foreach ($array as $key => $value){

            $index = $this->findOne($paths, $key);
            $this->xml['value'][$pathsIndex]['value'][$index]['value'] = $value;

        }

    }


    public function setHostname($name){

        // hostname
        $hostname = $this->findOne($this->xml, 'hostname');

        $this->xml['value'][$hostname]['value'] = $name;


    }

    /**************************
     *  Generic xml parser
     **************************/

    public function findOne($xml, $property){

        $property = '{}'.$property;

        foreach ($xml['value'] as $key => $value) {
            if ( $value['name'] === $property ) return $key;
        }
        return false;

    }


    /*****************
     * File adn Folders
     * ********************/


    public function createXml($name){

        $path = 'icecast/'.$name.'/'.$name.'.xml';
        Storage::put($path, $this->arrayToXml($this->xmlTemplate) );

    }

    public function crateFiles($name){

        $this->createXml($name);
        $path = 'icecast/'.$name;

        Storage::put($path.'/log/access.log', '');
        Storage::put($path.'/log/error.log', '');
        Storage::put($path.'/pid.txt', '');

        $this->changePermission( $this->icepath, '774' );

    }


    public function changePermission($path, $permission){

        exec('chmod -R '.$permission.' '.$path);

    }

    public function deleteXml($name){
        $path = $this->mountpath.$name.'/';

        Storage::deleteDirectory($path);

    }

    public function createFilesAndFolders($name){

        $this->createFolderTree($name);
        $this->createXml($name);
        $this->crateFiles($name);

    }

    public function createFolderTree ($name){

        $this->createFolder($name);
        $this->createFolder($name.'/log');
        $this->createFolder($name.'/admin');

    }

    public function deleteFolder($name){
        $path = 'icecast/'.$name;

        if($this->folderExist($path) ){
            Storage::deleteDirectory($path);
        }
    }

    public function createFolder($name){

        $path = 'icecast/'.$name;
        Storage::makeDirectory($path);

    }

    public function folderExist($path) {

        $folder = Storage::files($path);

        return ( is_array($folder) && count($folder) );

    }

    /**************
     * Process
     ***************/


    public function getParentPid($pid){

        return trim(exec('ps hoppid '.$pid));

    }

    public function getChildPid($pid){
        return trim(exec('pgrep -P '.$pid));
    }

}