<?php

class Dialplan extends Library {

    private $IVRModel;
    private $IVRDynModel;
    private $DialplanSettings;
    private $Priority;
    private $MacroPriority;
    private $counter = 0;
    private $IVR = array();

    public function __construct() {
        parent::__construct();
        $this->load->library('Encrypt');
        $this->IVRModel = new Model('ivr');
        $this->IVRDynModel = new Model('ivr_dynamic');
        $this->DialplanSettings = new Model('dialplan_settings',1);
    }

    public function Generate() {
        $auth = Session::get('Auth');
        //print_r($auth);

        $dialplan = 'exten => 10,1,Hangup()' . PHP_EOL;
        //$dialplan.=' n,Set(GLOBAL(data)=${data}|Start))'.PHP_EOL;
        $this->Priority = 2;
        $ivrs = $this->IVRModel->where('company_id', $auth['Company']['unique_company_identifier'])->get_all();

        $ivrmodd = array();
        $c = 0;
        foreach ($ivrs['Data'] as $i) {

            $ivrmodd[$c] = $i;
            $ivrmodd[$c]['children'] = $this->GetIVRChilds($i['id']);

            $c++;
        }

        $dialplan.=$this->FilterIVRArray($ivrmodd);
        
        file_put_contents(base_dir() . 'BroadcastScript/asterisk/config/extensions_blazon_outbound.conf', $dialplan);

        $dialplan_key = 'local';
        if(isset($this->config['Dialplan_KEY'])){
            $dialplan_key = $this->config['Dialplan_KEY'];
        }

        if($dialplan_key == 'local'){
            //local
            $cmd = 'sudo asterisk -rx "dialplan reload" 2>&1'; //local
            //$cmd=  'sudo ssh root@10.10.99.247 \'asterisk -rx "dialplan reload"\' 2>&1';

            exec($cmd);
            //
        }
        else{
            //remote
            
            $dialplan_settings = $this->DialplanSettings->get_single();
            if(!empty($dialplan_settings['Data'])){
                $ds_host = $dialplan_settings['Data']['ds_host'];
                $ds_port = $dialplan_settings['Data']['ds_port'];
                $ds_user = $dialplan_settings['Data']['ds_user'];
                $ds_pass = $dialplan_settings['Data']['ds_pass'];

                $ds_decrypt = new Encrypt();
                $ds_pass = $ds_decrypt->decrypt($ds_pass);

                //remote
                $connection = ssh2_connect($ds_host, $ds_port);

                if (ssh2_auth_password($connection, $ds_user, $ds_pass)) {
                    
                } else {
                    die('Authentication Failed...');
                }
                //

                $stream = ssh2_exec($connection, 'asterisk -rx "dialplan reload"');
            }
            else{
                die('Missing Dialplan Settings');
            }
        }
    }

    public function GenerateInbound($id) {
        
        $auth = Session::get('Auth');
        $this->Generate();
        $dialplan = 'exten => 20,1,Answer()' . PHP_EOL;
        $dialplan.='same => 2,Set(GLOBAL(ivrId)='.$id.')' . PHP_EOL;
        $this->Priority = 3;
        $ivrs = $this->IVRModel->where('company_id', $auth['Company']['unique_company_identifier'])->where('id', $id)->get_all();
        $ivrmodd = array();
        $c = 0;
        foreach ($ivrs['Data'] as $i) {
            $ivrmodd[$c] = $i;
            $ivrmodd[$c]['children'] = $this->GetIVRChilds($i['id']);
            $c++;
        }
        $dialplan.=$this->FilterIVRArray($ivrmodd, 'inbound');
        //print_r($dialplan);exit;
        file_put_contents(base_dir() . 'BroadcastScript/asterisk/config/extensions_blazon_inbound.conf', $dialplan);
        return $dialplan;
    }

    public function GetIVRChilds($id, $parent = '') {
        $returndata = array();
        $data = $this->IVRDynModel->where('ivr_title', $id)->where('parent', $parent)->get_all();
        $c = 0;
        foreach ($data['Data'] as $d) {
            $returndata[$c] = $d;
            if ($d['has_child_flag'] == 1) {
                $returndata[$c]['children'] = $this->GetIVRChilds($id, $d['id']);
            }
            $c++;
        }
        return($returndata);
    }

    public function FilterIVRArray($data, $mode = 'outbound') {
        $app = '';
        $macro = '';
        foreach ($data as $d) {

            $macroname = "[macro-".$mode.'-' . str_replace(' ', '-', $d['name']) . "]";
            //$app.='same => '. $this->Priority .',NoCDR'.PHP_EOL;
            //$this->Priority ++;
            //print_r($d);exit;
            $this->IVRModel->where('id', $d['id'])->update(array('priority' => $this->Priority));
            $app.='same=> ' . $this->Priority . ',Gosub(macro-'.$mode.'-' . str_replace(' ', '-', $d['name']) . ',s,1)' . PHP_EOL;
            $this->Priority ++;




            $macro.=$macroname . PHP_EOL;
            $macro.='exten => s,1,NoCDR' . PHP_EOL;
            $macro.='same => 2,Set(CHANNEL(hangup_handler_push)=blazon-' . $mode . '-hangup,s,1(args))' . PHP_EOL;
            $macro.='same => 3,Set(GLOBAL(data${cnum})=Start)' . PHP_EOL;
            $macro.='same => 4,Set(GLOBAL(data${cnum})=${data${cnum}}|' . str_replace(' ', '', $d["name"]) . ')' . PHP_EOL;
            $this->MacroPriority = 5;

            if (isset($d['children']) || !empty($d['children'])) {
                $this->GetIvrs($d['children']);

                $macro.=$this->GetChildMacros($d['children']);
            }

            //print_r($this->MacroPriority);exit;
            $macro.='same => ' . $this->MacroPriority . ',Set(GLOBAL(data${cnum})=${data${cnum}}|END)' . PHP_EOL;
            $this->MacroPriority++;
            $macro.='same => ' . $this->MacroPriority . ',Hangup()' . PHP_EOL;
            $this->MacroPriority++;
        }
        return $app . $macro;
    }

    public function GetIvrs($data, $prikey = '') {


        foreach ($data as $d) {
            $this->array[$d['id']] = $d;
            $this->array[$d['id']]['priority'] = $prikey;
            $this->counter++;
            if ($d['has_child_flag'] == 1) {
                $this->GetIvrs($d['children'], $prikey . $d['ivr_key']);
            }
        }
    }

    public function CleanIvrs() {
        foreach ($this->array as $key => $a) {
            unset($this->array[$key]['children']);
        }
    }

    public function GetChildMacros($data) {
        $app = '';
        foreach ($data as $d) {
            //if ($d['parent'] == '') {
            //    $this->MacroPriority++;
            //}
            $app.=$this->GetDialplan($d);
        }
        return $app;
    }

    public function GetPriority($id) {
        if (isset($this->array[$id])) {
            $pri = str_replace('0', '', $this->array[$id]['priority']);

            if ($pri != '') {
                $dialstep = $pri . $this->array[$id]['ivr_key'] . $this->array[$id]['ivr_key'];
            } else {
                $dialstep = $this->MacroPriority;
            }
        } else {
            $dialstep = '';
        }
        return $dialstep;
    }

    public function GetDialplan($d) {

        $dialstep = $this->GetPriority($d['id']);
        $app = '';
        if (!empty($d['ivr_recording'])) {
            $ivrfilearr = explode('/', $d['ivr_recording']);
            $ivrfilenamearr = explode('.', $ivrfilearr[1]);
            $ivrfilename = $ivrfilenamearr[0];
        } else {

            $ivrfilename = '';
        }
        if ($d['call_type'] == 'ivr') {
            $app.=$this->ivr_dialplan($d, $dialstep, $ivrfilename);
        } else if ($d['call_type'] == 'ivr_with_child') {
            $app.=$this->ivr_child_dialplan($d, $dialstep, $ivrfilename);
        } else if ($d['call_type'] == 'voicemail') {
            $app.=$this->voicemail_dialplan($d, $dialstep, $ivrfilename);
        } else if ($d['call_type'] == 'live_agent_bridge') {
            $app.=$this->live_agent_dialplan($d, $dialstep, $ivrfilename);
        } else if ($d['call_type'] == 'return_to_mm') {
            $app.=$this->main_menu_dialplan($d, $dialstep);
        } else if ($d['call_type'] == 'feedback') {
            $app.=$this->feedback_dialplan($d, $dialstep, $ivrfilename);
        } else if ($d['call_type'] == 'hangup') {
            $app.=$this->hangup_dialplan($d, $dialstep, $ivrfilename);
        }

        return $app;
    }

    /**
     * Creates Dialplan steps for Ivr type childs
     *
     *
     * @param array $d takes ivr Dynamic Child Table row
     * @param int $dialstep take current priority number
     * @param string $ivrfilenamearr take IVR recording Filename
     *
     * @return dialplan functione for ivr type
     */
    function ivr_dialplan($d, $dialstep, $ivrfilenamearr) {
        if ($ivrfilenamearr != '') {
            $app = 'same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|' . $d['ivr_name'] . ')' . PHP_EOL;
            $dialstep++;
            $app.='same => ' . $dialstep . ',Playback(' . $ivrfilenamearr . ')' . PHP_EOL;
            $dialstep++;
            if ($d['parent'] != '') {
                $app.='same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|END)' . PHP_EOL;
                $dialstep++;
                $app.='same => ' . $dialstep . ',Hangup()' . PHP_EOL;
                $dialstep++;
            }
            if ($d['parent'] == '') {
                $this->MacroPriority+=2;
            }

            $app.=PHP_EOL;
        } else {
            $app = '';
        }
        return $app;
    }

    function ivr_child_dialplan($d, $dialstep, $ivrfilenamearr) {
        if ($ivrfilenamearr != '') {
            $app = 'same => ' . $dialstep . ',Read(option' . $d['ivr_key'] . ',' . $ivrfilenamearr . ',1,skip,0,5)' . PHP_EOL;
            $dialstep++;
            $app.= 'same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|' . $d['ivr_name'] . ':${option' . $d['ivr_key'] . '})' . PHP_EOL;
            $dialstep++;
            $child_conditions = array();
            if (isset($d['children'])) {
                foreach ($d['children'] as $dc) {
                    $child_conditions[] = '${option' . $d['ivr_key'] . '}="' . $dc['ivr_key'] . '"';
                }
            }
            $s = $this->GetPriority($d['id']);

            if ($s == '' || $s == $this->MacroPriority) {
                $s = $d['ivr_key'];
            } else if (strlen($s) > 1) {
                $s = substr_replace($s, "", -1);
            }

            $cond = '$[' . implode('|', $child_conditions) . ']?' . $s . '${option' . $d['ivr_key'] . '}${option' . $d['ivr_key'] . '}:' . ($dialstep + 1) . '';
            $app.='same => ' . $dialstep . ',GoToIf(' . $cond . ') ' . PHP_EOL;
            $dialstep++;
            $app.='same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|' . $d['ivr_name'] . ':Invalid DTMF Caught)' . PHP_EOL;
            $dialstep++;
            if ($d['parent'] != '') {
                $app.='same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|END)' . PHP_EOL;
                $dialstep++;
                $app.='same => ' . $dialstep . ',Hangup()' . PHP_EOL;
                $dialstep++;
            }
            if ($d['parent'] == '') {
                $this->MacroPriority+=4;
            }
            $app.=PHP_EOL;
        } else {
            $app = '';
        }
        return $app;
    }

    function voicemail_dialplan($d, $dialstep, $ivrfilenamearr) {

        $app = 'same => ' . $dialstep . ',Set(file_name=ivr_${cnum}_' . $d['ivr_name'] . '_${EPOCH})' . PHP_EOL;
        $dialstep++;
        $app.= 'same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|' . $d['ivr_name'] . '<Voicemail>:${file_name})' . PHP_EOL;
        $dialstep++;

        //same => 8,Set(file_name=ivr_${main_menu}_1_${CALLERID(num)}_${EPOCH})
        if (!empty($d['ivr_recording'])) {
            $app.='same => ' . $dialstep . ',Playback(' . $ivrfilenamearr . ')' . PHP_EOL;
            $dialstep++;
            $app.='same => ' . $dialstep . ',Record(' . base_dir() . 'Records/${file_name}:wav,5,30,skip)' . PHP_EOL;
            $dialstep++;
        } else {

            $app.='same => ' . $dialstep . ',Record(' . base_dir() . 'Records/${file_name}:wav,5,30,skip)' . PHP_EOL;
            $dialstep++;
        }
        $app.='same => ' . $dialstep . ',Playback(thank-you)' . PHP_EOL;
        $dialstep++;
        if ($d['parent'] != '') {
            $app.='same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|END)' . PHP_EOL;
            $dialstep++;
            $app.='same => ' . $dialstep . ',Hangup()' . PHP_EOL;
            $dialstep++;
        }
        $s = (!empty($d['ivr_recording']) ? 5 : 4);
        if ($d['parent'] == '') {
            $this->MacroPriority+=$s;
        }
        $app.=PHP_EOL;
        return $app;
    }

    function live_agent_dialplan($d, $dialstep, $ivrfilenamearr) {
        $app = '';
        $app.=PHP_EOL;
        return $app;
    }

    function main_menu_dialplan($d, $dialstep) {
        $app = 'same => ' . $dialstep . ', Set(GLOBAL(data${cnum})=${data${cnum}}|Return to Main Menu)' . PHP_EOL;
        $dialstep++;
        $app.='same => ' . $dialstep . ', Goto(4)' . PHP_EOL;
        $app.=PHP_EOL;
        return $app;
    }

    function feedback_dialplan($d, $dialstep, $ivrfilenamearr) {
        $app = 'same => ' . $dialstep . ',Read(option' . $d['ivr_key'] . ',' . $ivrfilenamearr . ',1,skip,0,5)' . PHP_EOL;
        $dialstep++;
        $app.= 'same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|' . $d['ivr_name'] . ':${option' . $d['ivr_key'] . '})' . PHP_EOL;
        $dialstep++;
        if ($d['parent'] != '') {
            $app.='same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|END)' . PHP_EOL;
            $dialstep++;
            $app.='same => ' . $dialstep . ',Hangup()' . PHP_EOL;
            $dialstep++;
        }

        if ($d['parent'] == '') {
            $this->MacroPriority+=2;
        }
        $app.=PHP_EOL;
        return $app;
    }

    function hangup_dialplan($d, $dialstep, $ivrfilenamearr) {
        $macro = 'same => ' . $dialstep . ',Set(GLOBAL(data${cnum})=${data${cnum}}|END)' . PHP_EOL;
        $dialstep++;
        $macro.='same => ' . $dialstep . ',Hangup()' . PHP_EOL;
        $dialstep++;

        if ($d['parent'] == '') {
            $this->MacroPriority+=2;
        }
        $macro.=PHP_EOL;
        return $macro;
    }

}
