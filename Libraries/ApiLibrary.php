<?php

class Api extends Library{

  public $encrypt;
  private $api_key;
  private $return_data;
  private $uri;
  private $debug;


  function __construct(){
    parent::__construct();
    $this->load->library('Encrypt');
    $this->encrypt=new Encrypt();
    $this->api_key=get_api_key(base_url());
    //$this->api_key='PI2M71-8UF03-QQVY1';
    if($this->api_key==''){
      echo "Fatal!!!!!! Api Key not set. Exiting";
      exit;
    }
  }

  function Prepare($entrypoint,$data){

    if(is_array($data)){
      $data=urlencode(json_encode($data));
    }
    else{
      $data=urlencode($data);
    }
    $p['entrypoint']=$entrypoint;
    $p['data']=$data;
    $p['api_key']=$this->api_key;
    $p=json_encode($p);
    $p=$this->encrypt->Encrypt($p);

    // $this->uri=$this->config['Login_Url']."API/Gateway/?entry=".$enrtypoint.'&data='.$data;
	 
    $this->uri='https://'.$this->config['Login_Url']."API/Gateway?param=".urlencode($p);
    //print_r($this->uri);exit;
    $this->ProcessRequest();
  }

  function ProcessRequest(){

    // $fp=file_get_contents($this->uri);
    // if($this->debug){
    //   print_r($fp);exit;
    // }

    // $this->return_data=json_decode($fp,true);
    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, $this->uri);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);      
    $dataList = curl_exec($cURLConnection);
    curl_close($cURLConnection);
    $jsonArrayResponse = json_decode($dataList);
    return $jsonArrayResponse;
  }

  function SendRequest($entrypoint,$data,$debug=false){
    $this->debug=$debug;
    $this->Prepare($entrypoint,$data);
    return $this->return_data;
  }

  function GetRequest($entrypoint,$data ,$debug=false){
    $this->debug=$debug;
    $this->Prepare($entrypoint,$data);
    return $this->return_data;
  }

  function PostRequest(){}

}
