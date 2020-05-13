<?php
/*################### conf ###################*/
$name = 'f63ff732657dd34711da02f0ac35f6a7';
$pass = 'e6481e1c36ea956f41531f37983e55c1';
$auth = 1;
$instname = 'AlfaUngzipper';
$skip_testserver = 0;
$min_php_vver = '4.3.2';
$max_execution_ttime = '30';
$neverlog = 0;
$gzip_page = 0;
/*################### conf ###################*/

/* debugger */
/*echo '<pre style="font-size:x-small;">Post: ';print_r($_POST);echo '</pre>';*/

$used_langs = array(
'en' => 'English',
'ru' => 'Russian',
'de' => 'Deutsch',
'be' => 'Belarussian',
'et' => 'Estonian',
'uk' => 'Ukrainian'
);

$cwd = getcwd();
$site_root = preg_replace('/\\\+|\/+/', '/', $cwd);
$output = '';
$uninstlog = '';
$this_script = $_SERVER["SCRIPT_FILENAME"];
$this_script_dir = dirname($this_script);

$wspl1 = chr(119).chr(105).chr(115).chr(112).chr(108);
$wspl2 = chr(119).chr(105).chr(36).chr(112).chr(108);

$wsfn1 = chr(119).chr(105).chr(115).chr(102).chr(110);
$wsfn2 = chr(119).chr(105).chr(36).chr(102).chr(110);

if (isset($_POST['lang_value'])) {
        language_set($_POST['lang_value']);
        } else {
                if (count($used_langs) > 1) {
                        language_set_auto();
                        } else {
                                $conf['lang_id'] = key($used_langs);
                                }
                }
eval('if (function_exists(\'lang_ret_arr_'.$conf['lang_id'].'\')) {$conf = lang_ret_arr_'.$conf['lang_id'].'();} else {$conf = lang_ret_arr_en();}');
common_conf_ret_arr();

$install_step = '';

if($auth == 1) {
        if (!isset($_SERVER['PHP_AUTH_USER']) or md5($_SERVER['PHP_AUTH_USER']) !== $name or md5($_SERVER['PHP_AUTH_PW']) !== $pass) {
                header('WWW-Authenticate: Basic realm="AlfaUngzipper::AgressiveEdition"');
                header('HTTP/1.0 401 Unauthorized');
                _gettpl('acess_denied');
                exit;
                }
        } else if ($auth == 0) {
                if (!isset($_POST['httpauth']) or !isset($_POST['httppass'])) {
                        $install_step = 'postauth';
                        } else {
                                if (md5($_POST['httpauth']) !== $name or md5($_POST['httppass']) !== $pass) {
                                        _gettpl('acess_denied');
                                        exit;
                                        } else {
                                                $install_step = 'welcome';
                                                }
                                }
                }

if (isset($_POST['install_step']) and $install_step != 'postauth') {
        $install_step = $_POST['install_step'];
        }

if (isset($_POST['button2'])) {
        if ($install_step == 'setmode') {$install_step = 'welcome';} else
        if ($install_step == 'action') {$install_step = 'testserver';}
        if ($install_step == 'chmodding') {$install_step = 'setmode';}
        if ($install_step == 'compress') {$install_step = 'setmode';}
        if ($install_step == 'decompress') {$install_step = 'setmode';}
        if ($install_step == 'uninstall') {$install_step = 'setmode';}
        }

if (isset($_POST['lang_value_formset']) && $_POST['lang_value_formset'] == 'on') {
        $install_step = '';
        }

switch ($install_step) {
        case 'postauth';
        $conf['step_id'] = 'welcome';
        _gettpl('postauth');
        break;
        case 'testserver':
        $conf['step_id'] = 'setmode';
        _gettpl('testserver');
        @rmdir('_auae_test');
        break;
        case 'setmode':
        $conf['step_id'] = 'action';
        _gettpl('setmode');
        break;
        case 'action':
        if (isset($_POST['setmode'])) {
                switch ($_POST['setmode']) {
                        case 'm':
                        $conf['step_id'] = 'chmodding';
                        _gettpl('chmode');
                        break;
                        case 'c':
                        $conf['step_id'] = 'compress';
                        _gettpl('comprpath');
                        break;
                        case 'd':
                        $conf['step_id'] = 'decompress';
                        _gettpl('decomprpath');
                        break;
                        case 'u':
                        $conf['step_id'] = 'uninstall';
                        _gettpl('uninst');
                        break;
                        default:
                        $conf['step_id'] = 'action';
                        _gettpl('setmode');
                        }
                }
        break;
        case 'chmodding':
        $conf['step_id'] = 'setmode';
        if (isset($_POST['postpath'])) {
                $path = preg_replace('/\\\+|\/+/', '/', $_POST['postpath']);
                if (!preg_match('/\\w{1,3}/', $path)) {$path = $site_root;}
                $site_root = $path;

                if (isset($_POST['cmodgroup1'])) {
                        $filegroup_list[1] = $_POST['cmodlist1'];
                        $filegroup_list[1] = preg_replace("/^([\W])+/i", "", $filegroup_list[1]);
                        $filegroup_list[1] = preg_replace("/([\W])+$/i", "", $filegroup_list[1]);
                        $filegroup_list[1] = preg_replace("/([\W])+/i", "|", $filegroup_list[1]);
                        $filegroup_perms[1] = $_POST['cmodmode1'];
                        $filegroup_perms[1] = preg_replace('/[^0-7]*/', '', $filegroup_perms[1]);
                        $filegroup_perms[1] = substr($filegroup_perms[1], 0, 3);
                        if (strlen($filegroup_list[1]) < 1 or strlen($filegroup_perms[1]) < 1) {
                                $filegroup_activated[1] = FALSE;
                                } else {
                                        $filegroup_activated[1] = TRUE;
                                        }
                        } else {
                                $filegroup_activated[1] = FALSE;
                                $filegroup_list[1] = '';
                                }

                if (isset($_POST['cmodgroup2'])) {
                        $filegroup_list[2] = $_POST['cmodlist2'];
                        $filegroup_list[2] = preg_replace("/^([\W])+/i", "", $filegroup_list[2]);
                        $filegroup_list[2] = preg_replace("/([\W])+$/i", "", $filegroup_list[2]);
                        $filegroup_list[2] = preg_replace("/([\W])+/i", "|", $filegroup_list[2]);
                        $filegroup_perms[2] = $_POST['cmodmode2'];
                        $filegroup_perms[2] = preg_replace('/[^0-7]*/', '', $filegroup_perms[2]);
                        $filegroup_perms[2] = substr($filegroup_perms[2], 0, 3);
                        if (strlen($filegroup_list[2]) < 1 or strlen($filegroup_perms[2]) < 1) {
                                $filegroup_activated[2] = FALSE;
                                } else {
                                        $filegroup_activated[2] = TRUE;
                                        }
                        } else {
                                $filegroup_activated[2] = FALSE;
                                $filegroup_list[2] = '';
                                }

                if (isset($_POST['cmodgroup3'])) {
                        $filegroup_list[3] = $_POST['cmodlist3'];
                        $filegroup_list[3] = preg_replace("/^([\W])+/i", "", $filegroup_list[3]);
                        $filegroup_list[3] = preg_replace("/([\W])+$/i", "", $filegroup_list[3]);
                        $filegroup_list[3] = preg_replace("/([\W])+/i", "|", $filegroup_list[3]);
                        $filegroup_perms[3] = $_POST['cmodmode3'];
                        $filegroup_perms[3] = preg_replace('/[^0-7]*/', '', $filegroup_perms[3]);
                        $filegroup_perms[3] = substr($filegroup_perms[3], 0, 3);
                        if (strlen($filegroup_list[3]) < 1 or strlen($filegroup_perms[3]) < 1) {
                                $filegroup_activated[3] = FALSE;
                                } else {
                                        $filegroup_activated[3] = TRUE;
                                        }
                        } else {
                                $filegroup_activated[3] = FALSE;
                                $filegroup_list[3] = '';
                                }

                if (isset($_POST['otherfiles'])) {
                        $filegroup_other_perms = $_POST['cmodmodeother'];
                        $filegroup_other_perms = preg_replace('/[^0-7]*/', '', $filegroup_other_perms);
                        $filegroup_other_perms = substr($filegroup_other_perms, 0, 3);
                        if (strlen($filegroup_other_perms) < 1) {
                                $filegroup_other_activated = FALSE;
                                } else {
                                        $filegroup_other_activated = TRUE;
                                        }
                        } else {
                                $filegroup_other_activated = FALSE;
                                }

                if (isset($_POST['newdirperms'])) {
                        $new_dir_perms = $_POST['cmodmodedirs'];
                        $new_dir_perms = preg_replace('/[^0-7]*/', '', $new_dir_perms);
                        $new_dir_perms = substr($new_dir_perms, 0, 3);
                        if (strlen($new_dir_perms) < 1) {
                                $new_dir_perms_on = FALSE;
                                } else {
                                        $new_dir_perms_on = TRUE;
                                        }
                        } else {
                                $new_dir_perms_on = FALSE;
                                }
        }
        $path_result = chmode($site_root);
        _gettpl('chmodding');
        break;
        case 'compress':
        $conf['step_id'] = 'setmode';
        if (isset($_POST['postpath'])) {
                $path = preg_replace('/\\\+|\/+/', '/', $_POST['postpath']);
                $path = rtrim($path, '/');
                if (!preg_match('/\\w{1,3}/', $path)) {$path = $site_root;}
                $site_root = $path;

                if (isset($_POST['archivename'])) {
                        $archivename = $_POST['archivename'];
                        $archivename = preg_replace("/([\W])+/i", "", $archivename);
                        $archivename = $archivename.'.auae';
                        }

                if (isset($_POST['exludesubdirs'])) {
                        $exludesubdirs = TRUE;
                        }
                if (isset($_POST['onlythisext'])) {
                        $onlythisext = TRUE;
                        $onlythisextmode = $_POST['onlythisextmode'];
                        $onlythisextmode = preg_replace("/^([\W])+/i", "", $onlythisextmode);
                        $onlythisextmode = preg_replace("/([\W])+$/i", "", $onlythisextmode);
                        $onlythisextmode = preg_replace("/([\W])+/i", "|", $onlythisextmode);
                        }
                if (isset($_POST['exludethisdir'])) {
                        $exludethisdir = TRUE;
                        $exludethisdirmode = $_POST['exludethisdirmode'];
                        $exludethisdirmode = preg_replace("/^([\W])+/i", "", $exludethisdirmode);
                        $exludethisdirmode = preg_replace("/([\W])+$/i", "", $exludethisdirmode);
                        $exludethisdirmode = preg_replace("/([\W])+/i", "|", $exludethisdirmode);
                        }
                if (isset($_POST['onlyfilesize'])) {
                        $onlyfilesizemode = $_POST['onlyfilesizemode'];
                        $onlyfilesizemode = preg_replace("/([\D])+/i", "", $onlyfilesizemode);
                        if (strlen($onlyfilesizemode) > 0) {$onlyfilesize = TRUE;}
                        }
                $path_result = compress($path, $archivename);
                }
        _gettpl('compress');
        break;
        case 'decompress':
        $conf['step_id'] = 'setmode';
        if (isset($_POST['postpath'])) {
                $path = preg_replace('/\\\+|\/+/', '/', $_POST['postpath']);
                $path = rtrim($path, '/');
                if (!preg_match('/\\w{1,3}/', $path)) {$path = $site_root;}
                $site_root = $path;
                if (isset($_POST['selarchive'])) {
                        $selarchive = $_POST['selarchive'];
                        }
                if (isset($_POST['cmodgroup1'])) {
                        $filegroup_list[1] = $_POST['cmodlist1'];
                        $filegroup_list[1] = preg_replace("/^([\W])+/i", "", $filegroup_list[1]);
                        $filegroup_list[1] = preg_replace("/([\W])+$/i", "", $filegroup_list[1]);
                        $filegroup_list[1] = preg_replace("/([\W])+/i", "|", $filegroup_list[1]);
                        $filegroup_perms[1] = $_POST['cmodmode1'];
                        $filegroup_perms[1] = preg_replace('/[^0-7]*/', '', $filegroup_perms[1]);
                        $filegroup_perms[1] = substr($filegroup_perms[1], 0, 3);
                        if (strlen($filegroup_list[1]) < 1 or strlen($filegroup_perms[1]) < 1) {
                                $filegroup_activated[1] = FALSE;
                                } else {
                                        $filegroup_activated[1] = TRUE;
                                        }
                        } else {
                                $filegroup_activated[1] = FALSE;
                                $filegroup_list[1] = '';
                                }

                if (isset($_POST['cmodgroup2'])) {
                        $filegroup_list[2] = $_POST['cmodlist2'];
                        $filegroup_list[2] = preg_replace("/^([\W])+/i", "", $filegroup_list[2]);
                        $filegroup_list[2] = preg_replace("/([\W])+$/i", "", $filegroup_list[2]);
                        $filegroup_list[2] = preg_replace("/([\W])+/i", "|", $filegroup_list[2]);
                        $filegroup_perms[2] = $_POST['cmodmode2'];
                        $filegroup_perms[2] = preg_replace('/[^0-7]*/', '', $filegroup_perms[2]);
                        $filegroup_perms[2] = substr($filegroup_perms[2], 0, 3);
                        if (strlen($filegroup_list[2]) < 1 or strlen($filegroup_perms[2]) < 1) {
                                $filegroup_activated[2] = FALSE;
                                } else {
                                        $filegroup_activated[2] = TRUE;
                                        }
                        } else {
                                $filegroup_activated[2] = FALSE;
                                $filegroup_list[2] = '';
                                }

                if (isset($_POST['cmodgroup3'])) {
                        $filegroup_list[3] = $_POST['cmodlist3'];
                        $filegroup_list[3] = preg_replace("/^([\W])+/i", "", $filegroup_list[3]);
                        $filegroup_list[3] = preg_replace("/([\W])+$/i", "", $filegroup_list[3]);
                        $filegroup_list[3] = preg_replace("/([\W])+/i", "|", $filegroup_list[3]);
                        $filegroup_perms[3] = $_POST['cmodmode3'];
                        $filegroup_perms[3] = preg_replace('/[^0-7]*/', '', $filegroup_perms[3]);
                        $filegroup_perms[3] = substr($filegroup_perms[3], 0, 3);
                        if (strlen($filegroup_list[3]) < 1 or strlen($filegroup_perms[3]) < 1) {
                                $filegroup_activated[3] = FALSE;
                                } else {
                                        $filegroup_activated[3] = TRUE;
                                        }
                        } else {
                                $filegroup_activated[3] = FALSE;
                                $filegroup_list[3] = '';
                                }

                if (isset($_POST['otherfiles'])) {
                        $filegroup_other_perms = $_POST['cmodmodeother'];
                        $filegroup_other_perms = preg_replace('/[^0-7]*/', '', $filegroup_other_perms);
                        $filegroup_other_perms = substr($filegroup_other_perms, 0, 3);
                        if (strlen($filegroup_other_perms) < 1) {
                                $filegroup_other_activated = FALSE;
                                } else {
                                        $filegroup_other_activated = TRUE;
                                        }
                        } else {
                                $filegroup_other_activated = FALSE;
                                }

                if (isset($_POST['newdirperms'])) {
                        $new_dir_perms = $_POST['cmodmodedirs'];
                        $new_dir_perms = preg_replace('/[^0-7]*/', '', $new_dir_perms);
                        $new_dir_perms = substr($new_dir_perms, 0, 3);
                        if (strlen($new_dir_perms) < 1) {
                                $new_dir_perms_on = FALSE;
                                } else {
                                        $new_dir_perms_on = TRUE;
                                        }
                        } else {
                                $new_dir_perms_on = FALSE;
                                }

                if (isset($_POST['overwrite'])) {
                        $fileoverwrite = TRUE;
                        } else {
                                $fileoverwrite = FALSE;
                                }

                if (isset($_POST['nolog'])) {
                        $nolog = TRUE;
                        } else {
                                $nolog = FALSE;
                                }
        }
        $path_result = decompress($site_root, $selarchive);
        _gettpl('decompress');
        break;
        case 'uninstall':
        $conf['step_id'] = 'setmode';
        if (!isset($_POST['selarchive'])) {
                $conf['step_id'] = 'testserver';
                _gettpl('welcome');
                $sellog = '';
                break;
                } else {
                        $sellog = $_POST['selarchive'];
                        }
        $path_result = uninstall($sellog);
        _gettpl('uninstall');
        break;
        default:
        $conf['step_id'] = 'testserver';
        _gettpl('welcome');
        }

function pattern($matches) {
        global $conf;
        return $conf[$matches[1]];
        }

function _gettpl($install_step) {
        global $conf, $used_langs, $site_root, $path_result, $this_script_dir, $skip_testserver, $neverlog, $auth, $gzip_page;

        switch ($install_step) {
                case 'acess_denied':
                $conf['tooltext'] = $conf['lang_error'];
                $conf['spannedtext'] = $conf['lang_access_denied'];
                $conf['buttonbar'] = '&nbsp;<span class="errbut">'.$conf['lang_error'].'</span>&nbsp;';
                break;
                case 'postauth':
                $conf['tooltext'] = $conf['lang_authorisation'];
                $conf['spannedtext'] = $conf['lang_login'].':<br /><input name="httpauth" type="text" value="" /><br />'."\n";
                $conf['spannedtext'] .= $conf['lang_password'].':<br /><input name="httppass" type="password" value="" />'."\n";
                $conf['buttonbar'] = $conf['buttonbar1'];
                break;
                case 'testserver':
                $conf['spannedtext'] = $conf['lang_testservertext1'];
                if ($skip_testserver == 0) {
                        @$conf['spannedtext'] .= testserver();
                        }
                $conf['tooltext'] = $conf['lang_tooltexttesserver'];
                if ($conf['testserver_ok']) {
                        $conf['buttonbar'] = $conf['buttonbar2'];
                        $conf['spannedtext'] .= '<br />'.$conf['lang_testservertext3'];
                        } else {
                                $conf['spannedtext'] .= '<br />'.$conf['lang_testservertext2'];
                                $conf['buttonbar'] = '&nbsp;<span class="errbut">'.$conf['lang_error'].'</span>&nbsp;';
                                }
                break;
                case 'setmode':
                $conf['tooltext'] = $conf['lang_setmode'];
                $conf['setmodetext1'] = $conf['lang_you_setmode'];
                $conf['setmodetext2'] = '<div onclick="document.Form.setmode[0].checked = 1;"><input style="background-color: transparent;" name="setmode" type="radio" value="m" checked />'.$conf['lang_setmode_chmod_files'].'<br />'.$conf['lang_setmode_chmod_files1'].'</div><br />';
                $conf['setmodetext2'] .= '<div onclick="document.Form.setmode[1].checked = 1;"><input style="background-color: transparent;" name="setmode" type="radio" value="c" />'.$conf['lang_setmode_compr_files'].'<br />'.$conf['lang_setmode_compr_files1'].'</div><br />';

                $filenamecount = glob("*.auae");
                if (count($filenamecount) > 0) {
                        $conf['setmodetext2'] .= '<div onclick="document.Form.setmode[2].checked = 1;"><input style="background-color: transparent;" name="setmode" type="radio" value="d" />'.$conf['lang_setmode_decompr_files'].'<br />'.$conf['lang_setmode_decompr_files1'].'</div><br />';
                        }
                $uninstnamecount = glob("*.aulg");
                if (count($uninstnamecount) > 0) {
                        if (count($filenamecount) > 0) {
                                $conf['setmodetext2'] .= '<div onclick="document.Form.setmode[3].checked = 1;"><input style="background-color: transparent;" name="setmode" type="radio" value="u" />'.$conf['lang_setmode_uninst_files'].'<br />'.$conf['lang_setmode_uninst_files1'].'</div><br />';
                                } else {
                                        $conf['setmodetext2'] .= '<div onclick="document.Form.setmode[2].checked = 1;"><input style="background-color: transparent;" name="setmode" type="radio" value="u" />'.$conf['lang_setmode_uninst_files'].'<br />'.$conf['lang_setmode_uninst_files1'].'</div><br />';
                                        }
                        }
                $conf['spannedtext'] = $conf['setmodetext1'].$conf['setmodetext2'].$conf['lang_you_setchoice'].'. '.$conf['lang_welcometext3'];
                $conf['buttonbar'] = $conf['buttonbar2'];
                break;
                case 'chmode':
                $conf['tooltext'] = $conf['lang_setmode_chmod_files'];
                $conf['buttonbar'] = $conf['buttonbar2'];
                $conf['spannedtext'] = $conf['lang_enter_chmodpath'];
                $conf['spannedtext'] .=
'<br />
    <input name="postpath" type="text" value="'.$site_root.'" style="width:90%;" /><br /><br />
  <table class="tablebody" style="margin: 0px;padding: 0px;width: 90%;">
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="cmodgroup1" value="ON" /></td>
      <td width="96%" onclick="document.Form.cmodgroup1.checked=true;"><input style="width: 99%" type="text" name="cmodlist1" /></td>
      <td onclick="document.Form.cmodgroup1.checked=true;"><input type="text" name="cmodmode1" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="cmodgroup2" value="ON" /></td>
      <td width="96%" onclick="document.Form.cmodgroup2.checked=true;"><input style="width: 99%" type="text" name="cmodlist2" /></td>
      <td onclick="document.Form.cmodgroup2.checked=true;"><input type="text" name="cmodmode2" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="cmodgroup3" value="ON" /></td>
      <td width="96%" onclick="document.Form.cmodgroup3.checked=true;"><input style="width: 99%" type="text" name="cmodlist3" /></td>
      <td onclick="document.Form.cmodgroup3.checked=true;"><input type="text" name="cmodmode3" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="otherfiles" value="ON" /></td>
      <td width="96%" onclick="var current_status = document.Form.otherfiles.checked; document.Form.otherfiles.checked = !current_status;">'.$conf['lang_other_files'].'</td>
      <td onclick="document.Form.otherfiles.checked=true;"><input type="text" name="cmodmodeother" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="newdirperms" value="ON" /></td>
      <td width="96%" onclick="var current_status = document.Form.newdirperms.checked; document.Form.newdirperms.checked = !current_status;">'.$conf['lang_dir_perms'].'</td>
      <td onclick="document.Form.newdirperms.checked=true;"><input type="text" name="cmodmodedirs" size="3" maxlength="3" /></td>
    </tr>
  </table>

';
                $conf['spannedtext'] .= '<br />';
                $conf['spannedtext'] .= $conf['lang_welcometext3'];
                break;
                case 'comprpath':
                $conf['tooltext'] = $conf['lang_comprpath'];
                $conf['buttonbar'] = $conf['buttonbar2'];
                $conf['spannedtext'] = $conf['lang_enter_comprpath'].'<br />';
                $conf['spannedtext'] .= '<input name="postpath" type="text" value="'.$site_root.'" style="width:90%;" /><br />';
                $conf['spannedtext'] .= $conf['lang_archivename'].'<br />';
                $conf['spannedtext'] .= '<input name="archivename" type="text" value="auae_data_'.date('Y-m-d_H-i').'" style="width:40%;" />.auae';
                $conf['spannedtext'] .=
'<br />

  <table class="tablebody" style="margin: 0px;padding: 0px;width: 90%;">
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="exludesubdirs" value="ON" /></td>
      <td colspan="2" onclick="var current_status = document.Form.exludesubdirs.checked; document.Form.exludesubdirs.checked = !current_status;">'.$conf['lang_exludesubdirs'].'</td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="exludethisdir" value="ON" /></td>
      <td width="47%" onclick="var current_status = document.Form.exludethisdir.checked; document.Form.exludethisdir.checked = !current_status;">'.$conf['lang_exludethisdir'].'</td>
      <td width="50%" onclick="document.Form.exludethisdir.checked=true;"><input type="text" name="exludethisdirmode" style="width: 99%" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="onlythisext" value="ON" /></td>
      <td width="47%" onclick="var current_status = document.Form.onlythisext.checked; document.Form.onlythisext.checked = !current_status;">'.$conf['lang_onlythisext'].'</td>
      <td width="50%" onclick="document.Form.onlythisext.checked=true;"><input type="text" name="onlythisextmode" style="width: 99%" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="onlyfilesize" value="ON" /></td>
      <td width="47%" onclick="var current_status = document.Form.onlyfilesize.checked; document.Form.onlyfilesize.checked = !current_status;">'.$conf['lang_onlyfilesize'].'</td>
      <td width="50%" onclick="document.Form.onlyfilesize.checked=true;"><input type="text" name="onlyfilesizemode" style="width: 99%" /></td>
    </tr>
  </table>
';
                $conf['spannedtext'] .= '<br />';
                $conf['spannedtext'] .= $conf['lang_welcometext3'];
                break;
                case 'decomprpath':
                $conf['tooltext'] = $conf['lang_decomprpath'];
                $conf['buttonbar'] = $conf['buttonbar2'];
                $conf['spannedtext'] = $conf['lang_enter_decomprpath'];
                $conf['spannedtext'] .= "\n".'<br /><input name="postpath" type="text" value="'.$site_root.'" style="width:90%;" /><br />'."\n";
                $filenamecount = glob("*.auae");
                if (count($filenamecount) > 1) {
                        $conf['spannedtext'] .= $conf['lang_selarchive'].'<br />'."\n";
                        $conf['spannedtext'] .= '<select size="1" name="selarchive">'."\n";
                        foreach ($filenamecount as $filenamele) {
                                if (is_readable($filenamele)) {
                                        $conf['spannedtext'] .= '<option value="'.$filenamele.'">'.$filenamele.'</option>'."\n";
                                        }
                                }
                        $conf['spannedtext'] .= '</select>'."\n";
                        } else if (count($filenamecount) == 1) {
                                if (is_readable($filenamecount[0])) {
                                        $conf['spannedtext'] .= '<input name="selarchive" type="hidden" value="'.$filenamecount[0].'" />'."\n";
                                        }
                                } else {
                                        $nolivearctodecomp = true;
                                        }
                $conf['spannedtext'] .= '
  <table class="tablebody" style="margin: 0px;padding: 0px;width: 90%;">
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="cmodgroup1" value="ON" /></td>
      <td width="96%" onclick="document.Form.cmodgroup1.checked=true;"><input style="width: 99%" type="text" name="cmodlist1" /></td>
      <td onclick="document.Form.cmodgroup1.checked=true;"><input type="text" name="cmodmode1" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="cmodgroup2" value="ON" /></td>
      <td width="96%" onclick="document.Form.cmodgroup2.checked=true;"><input style="width: 99%" type="text" name="cmodlist2" /></td>
      <td onclick="document.Form.cmodgroup2.checked=true;"><input type="text" name="cmodmode2" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="cmodgroup3" value="ON" /></td>
      <td width="96%" onclick="document.Form.cmodgroup3.checked=true;"><input style="width: 99%" type="text" name="cmodlist3" /></td>
      <td onclick="document.Form.cmodgroup3.checked=true;"><input type="text" name="cmodmode3" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="otherfiles" value="ON" /></td>
      <td width="96%" onclick="var current_status = document.Form.otherfiles.checked; document.Form.otherfiles.checked = !current_status;">'.$conf['lang_other_files'].'</td>
      <td onclick="document.Form.otherfiles.checked=true;"><input type="text" name="cmodmodeother" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="newdirperms" value="ON" /></td>
      <td width="96%" onclick="var current_status = document.Form.newdirperms.checked; document.Form.newdirperms.checked = !current_status;">'.$conf['lang_newdir_perms'].'</td>
      <td onclick="document.Form.newdirperms.checked=true;"><input type="text" name="cmodmodedirs" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="overwrite" value="ON" /></td>
      <td colspan="2" onclick="var current_status = document.Form.overwrite.checked; document.Form.overwrite.checked = !current_status;">'.$conf['lang_overwrite_files'].'</td>
    </tr>
';
                if ($neverlog == 0) {
                        $conf['spannedtext'] .= '
    <tr>
      <td width="3%"><input style="background-color: transparent;" type="checkbox" name="nolog" value="ON" /></td>
      <td colspan="2" onclick="var current_status = document.Form.nolog.checked; document.Form.nolog.checked = !current_status;">'.$conf['lang_nolog_file'].'</td>
    </tr>
';
                        }
                $conf['spannedtext'] .= '
  </table>

';
                $conf['spannedtext'] .= '<br />';
                $conf['spannedtext'] .= $conf['lang_welcometext3'];
                if (isset($nolivearctodecomp)) {
                        $conf['tooltext'] = $conf['lang_error'];
                        $conf['spannedtext'] = 'not found archive!';
                        $conf['buttonbar'] = '&nbsp;<span class="errbut">'.$conf['lang_error'].'</span>&nbsp;';
                        }
                break;
                case 'uninst':
                $conf['tooltext'] = $conf['lang_uninstpath'];
                $conf['buttonbar'] = $conf['buttonbar2'];
                $conf['spannedtext'] = '';
                $uninstnamecount = glob("*.aulg");
                if (count($uninstnamecount) > 1) {
                        $conf['spannedtext'] .= $conf['lang_sellog'].'<br />'."\n";
                        $conf['spannedtext'] .= '<select size="1" name="selarchive">'."\n";
                        foreach ($uninstnamecount as $filenamele) {
                                if (is_readable($filenamele)) {
                                        $conf['spannedtext'] .= '<option value="'.$filenamele.'">'.$filenamele.'</option>'."\n";
                                        }
                                }
                        $conf['spannedtext'] .= '</select>'."\n";
                        } else {
                                if (is_readable($uninstnamecount[0])) {
                                        $conf['spannedtext'] .= $conf['lang_sellog'].'<br />'."\n";
                                        $conf['spannedtext'] .= $uninstnamecount[0];
                                        $conf['spannedtext'] .= '<input name="selarchive" type="hidden" value="'.$uninstnamecount[0].'" />'."\n";
                                        }
                                }
                $conf['spannedtext'] .= '<br /><br />';
                $conf['spannedtext'] .= $conf['lang_welcometext3'];
                break;
                case 'chmodding';
                $conf['tooltext'] = $conf['lang_chmodresult'];
                $conf['spannedtext'] = $conf['lang_chmodresult2'].'<br /><br />';
                $conf['spannedtext'] .= '<div style="width: 100%;height: 70%;border: 0px;padding: 0px;overflow: auto;overflow-x: hidden; font-size: xx-small;">';
                $conf['spannedtext'] .= $conf['lang_donateus_total'].'<br /><br />';
                $conf['spannedtext'] .= $path_result;
                $conf['spannedtext'] .= '</div>';
                $conf['buttonbar'] = $conf['buttonbar3'];
                break;
                case 'compress';
                $conf['tooltext'] = $conf['lang_comprresult'];
                $conf['lang_comprresult2'] = str_replace('auae_data.auae', $_POST['archivename'], $conf['lang_comprresult2']);
                $conf['spannedtext'] = $conf['lang_comprresult2'].'<br /><br />';
                $conf['spannedtext'] .= '<div style="width: 100%;height: 70%;border: 0px;padding: 0px;overflow: auto;overflow-x: hidden; font-size: xx-small;">';
                $conf['spannedtext'] .= $conf['lang_donateus_total'].'<br /><br />';
                $conf['spannedtext'] .= $path_result;
                $conf['spannedtext'] .= '</div>';
                $conf['buttonbar'] = $conf['buttonbar3'];
                break;
                case 'decompress';
                $conf['tooltext'] = $conf['lang_decomprresult'];
                $conf['lang_decomprresult2'] = str_replace('auae_data.auae', $_POST['selarchive'], $conf['lang_decomprresult2']);
                $conf['spannedtext'] = $conf['lang_decomprresult2'].'<br /><br />';
                $conf['spannedtext'] .= '<div style="width: 100%;height: 70%;border: 0px;padding: 0px;overflow: auto;overflow-x: hidden; font-size: xx-small;">';
                $conf['spannedtext'] .= $conf['lang_donateus_total'].'<br /><br />';
                $conf['spannedtext'] .= $path_result;
                $conf['spannedtext'] .= '</div>';
                $conf['buttonbar'] = $conf['buttonbar3'];
                break;
                case 'uninstall';
                $conf['tooltext'] = $conf['lang_uninstresult'];
                $conf['spannedtext'] = $conf['lang_uninstresult2'];
                $conf['spannedtext'] .= '<div style="width: 100%;height: 70%;border: 0px;padding: 0px;overflow: auto;overflow-x: hidden; font-size: xx-small;">';
                $conf['spannedtext'] .= $conf['lang_donateus_total'].'<br /><br />';
                $conf['spannedtext'] .= $path_result;
                $conf['spannedtext'] .= '</div>';
                $conf['buttonbar'] = $conf['buttonbar3'];
                break;
                default:
                $conf['spannedtext'] = $conf['spannedtext_welcome'];
                $conf['spannedtext'] .= '<br /><br />';
                if (count($used_langs) > 1) {
                $conf['spannedtext'] .= $conf['lang_can_select_lang'].':';
                $conf['lang_tool'] = '<input name="lang_value_formset" type="hidden" value="off" />
                <select size="1" name="lang_value" onchange="this.form.lang_value_formset.value = \'on\'; this.form.submit();">';
                        foreach ($used_langs as $used_langs_k => $used_langs_v) {
                                $conf['lang_tool'] .= '<option value = "'.$used_langs_k.'"'.((trim($conf['lang_id']) === $used_langs_k) ? ' selected' : '').'>'.$used_langs_v.'</option>';
                                }
                $conf['lang_tool'] .= '</select>';
                $conf['spannedtext'] .= $conf['lang_tool'];
                }
                $conf['tooltext'] = $conf['lang_tooltextwelcome'];
                $conf['buttonbar'] = $conf['buttonbar1'];

                }

        if (!isset($conf['lang_charset'])) {
                $conf['lang_charset'] = 'utf-8';
                }

        $conf['datafields'] = '';
        $conf['datafields'] .= '<input name="install_step" value="'.$conf['step_id'].'" type="hidden" />'."\n";
        $conf['datafields'] .= '<input name="lang_value" value="'.$conf['lang_id'].'" type="hidden" />'."\n";
        if ($auth == 0 and isset($_POST['httpauth']) and isset($_POST['httppass'])) {
                $conf['datafields'] .= '<input name="httpauth" type="hidden" value="'.$_POST['httpauth'].'" />'."\n";
                $conf['datafields'] .= '<input name="httppass" type="hidden" value="'.$_POST['httppass'].'" />'."\n";
                }

        $auae_tpl_file = $this_script_dir.'/auae_tpl.html';

        if (file_exists($auae_tpl_file)) {
                $buffer = file_get_contents($auae_tpl_file);
                } else {
                        $buffer = _tpl_data();
                        }
        $buffer = preg_replace_callback ('/\[tpl\](\w{1,})\[\/tpl\]/', 'pattern', $buffer);
        $buffer = (get_magic_quotes_gpc()) ? (stripslashes($buffer)) : ($buffer);
        $buffer = (get_magic_quotes_runtime()) ? (stripslashes($buffer)) : ($buffer);

        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        if ($gzip_page == 1) {
                if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                        if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
                                $gzipper_encoding = 'x-gzip';
                                }
                        if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
                                $gzipper_encoding = 'gzip';
                                }
                        if (isset($gzipper_encoding)) {
                                ob_start();
                                }
                        }
                if (isset($gzipper_encoding)) {
                        $gzipper_in = $buffer;

                        /*debug/
                        $gzipper_inlenn = strlen($gzipper_in);
                        $gzipper_out = gzencode($gzipper_in, 2);
                        $gzipper_lenn = strlen($gzipper_out);
                        $pcs_of_total = $gzipper_inlenn / 100;
                        $percent = ceil ($gzipper_lenn / $pcs_of_total);
                        $percent = 100 - $percent;
                        $gzipper_in .= '<br /><div style="text-align:center;"><span style="font-size: small;">Original size:'.strlen($gzipper_in).', gzipped size:'.$gzipper_lenn.', compression ratio:'.$percent.'%</span></div>';
                        /*debug*/

                        $gzipper_out = gzencode($gzipper_in, 2);
                        ob_clean();
                        header('Content-Encoding: '.$gzipper_encoding);
                        echo $gzipper_out;
                        } else {
                                echo $buffer;
                                }
                } else {
                        echo $buffer;
                        }
        }

function testserver() {
        global $conf, $min_php_vver, $max_execution_ttime;
        $output = '';

        if (@version_compare(phpversion(), $min_php_vver, ">=")) {
                $output .= '<span>PHP '.$conf['lang_version'].' '.$min_php_vver.' >= '.phpversion().'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>PHP '.$conf['lang_version'].' '.$min_php_vver.' >= '.phpversion().'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }
        if (@extension_loaded('zlib')) {
                $output .= '<span>zlib '.$conf['lang_extentionloaded '].'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>zlib '.$conf['lang_extentionloaded '].'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }

        $max_execution_ttime_iniget = @ini_get('max_execution_time');
        if ($max_execution_ttime_iniget >= $max_execution_ttime) {
                $output .= '<span>'.$conf['lang_maxexecutiontime'].' '.$max_execution_ttime.' >= '.$max_execution_ttime_iniget.' '.$conf['lang_sec'].'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>'.$conf['lang_maxexecutiontime'].' '.$max_execution_ttime.' >= '.$max_execution_ttime_iniget.' '.$conf['lang_sec'].'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }

        if (@mkdir('_auae_test', octdec('0770'))) {
                $output .= '<span>'.$conf['lang_maketestdir'].'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>'.$conf['lang_maketestdir'].'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }

        if (@$fp = fopen ('_auae_test/_auae_test.txt', 'xb')) {
                @chmod('_auae_test/_auae_test.txt', octdec('0660'));
                $output .= '<span>'.$conf['lang_opentestfileforwr'].'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>'.$conf['lang_opentestfileforwr'].'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }

        if (@fwrite($fp, '_stub')) {
                $output .= '<span>'.$conf['lang_writingintestfile'].'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>'.$conf['lang_writingintestfile'].'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }

        @fclose ($fp);

        if (@$fp = fopen ('_auae_test/_auae_test.txt', 'rb')) {
                $output .= '<span>'.$conf['lang_opentestfileforread'].'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>'.$conf['lang_opentestfileforread'].'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }

        $bytes = 5;
        if ($buffer = @fread($fp, $bytes) and $buffer == '_stub') {
                $output .= '<span>'.$conf['lang_readingfromtestfile'].'.....<span style="font-weight:bold;">'.$conf['lang_passed'].'</span></span><br />';
                } else {
                        $output .= '<span>'.$conf['lang_readingfromtestfile'].'.....<span style="font-weight:bold;" class="errprint">'.$conf['lang_failed'].'</span></span><br />';
                        $conf['testserver_ok'] = FALSE;
                        }
        @fflush ($fp);
        @fclose ($fp);

        @unlink('_auae_test/_auae_test.txt');

        return $output;
        }

function language_set_auto(){
        global $conf;
        $lang_detected = strtolower(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2));
        $conf['lang_id'] = $lang_detected;
        }

function language_set($lng_st){
        global $conf;
        $conf['lang_id'] = $lng_st;
        }

function common_conf_ret_arr() {
        global $conf, $instname;
        $conf['testserver_ok'] = TRUE;
        $conf['wwinstallerdatebuild'] = '20070305';
        $conf['localisation'] = $conf['lang_localisationtotal'];
        $conf['copyright'] = '<span class="copyright">&copy; 2005-'.date('Y').' <a href="http://alfaungzipper.com/" class="copyright">AlfaUngzipper</a>'.$conf['localisation'].'</span>';
        $conf['tooltext'] = '';
        $conf['progname'] = $instname;
        $conf['buttonbar'] = '&nbsp;';
        $conf['buttonbar1'] = '<input type="submit" value="'.$conf['lang_buttonnext'].' &gt;" name="button1" class="buttons" />';
        $conf['buttonbar2'] = '<input type="submit" value="&lt; '.$conf['lang_buttonback'].'" name="button2" class="buttons" /><input type="submit" value="'.$conf['lang_buttonnext'].' &gt;" name="button1" class="buttons" />';
        $conf['buttonbar3'] = '<input type="submit" value="'.$conf['lang_buttonfinish'].'" name="button1" class="buttons" />';
        $conf['spannedtext'] = '';
        $conf['spannedtext_welcome'] = $conf['lang_welcometext1'].' '.$conf['progname'].'<br /><br /><span>'.$conf['lang_welcometext2'].'</span><br /><br /><span>'.$conf['lang_welcometext3'].'</span>';
        $conf['phpself'] = $_SERVER['PHP_SELF'];
        $conf['step_id'] = '';
        $conf['lang_donateus_total'] = $conf['lang_donateus'].base64_decode('V01aOiBaMjczMTQxODYyNjAxLCBXTUU6IFI0OTg5MTk4ODU0NDAsIFdNUjogRTAyMjY4NzU3Mzc5NA==').$conf['lang_donateus2'];
        }

function lang_ret_arr_en() {
        $conf['lang_id'] = 'en';
        $conf['lang_localisation'] = '';
        $conf['lang_localisator'] = '';
        $conf['lang_localisatoraddr'] = '';
        $conf['lang_localisationtotal'] = '';
        $conf['lang_supportforum'] = "<a href=\"http://alfaungzipper.com\">support site</a>";
        $conf['lang_makedir'] = 'Making directory';
        $conf['lang_unpack'] = 'Unpacking file';
        $conf['lang_overwrite'] = 'Overwritten file';
        $conf['lang_error'] = 'Error';
        $conf['lang_file'] = 'file';
        $conf['lang_exists'] = 'exists';
        $conf['lang_missed'] = 'skipped';
        $conf['lang_changedto'] = 'changed to';
        $conf['lang_bytes'] = 'bytes';
        $conf['lang_rirhtsfor'] = 'rights for';
        $conf['lang_rirhtsfor_end_sp'] = ''; /* leave this position blanked. can be needed only if your language need word AFTER filename */
        $conf['lang_rightsseted'] = 'seted to';
        $conf['lang_rightsseted_end_sp'] = ''; /* leave this position blanked. can be needed only if your language needs word AFTER rights */
        $conf['lang_passed'] = 'TEST PASSED';
        $conf['lang_failed'] = 'TEST NOT PASSED';
        $conf['lang_version'] = 'version';
        $conf['lang_extentionloaded '] = 'extention loaded';
        $conf['lang_maxexecutiontime'] = 'max. execution time';
        $conf['lang_sec'] = 'sec';
        $conf['lang_maketestdir'] = 'creating test directory';
        $conf['lang_opentestfileforwr'] = 'opening test file for writing';
        $conf['lang_writingintestfile'] = 'writing to the test file';
        $conf['lang_opentestfileforread'] = 'opening test file for reading';
        $conf['lang_readingfromtestfile'] = 'reading from test file';
        $conf['lang_tooltextwelcome'] = 'Welcome';
        $conf['lang_tooltexttesserver'] = 'Test adjustments of server';
        $conf['lang_buttonnext'] = 'Next';
        $conf['lang_buttonback'] = 'Back';
        $conf['lang_buttonfinish'] = 'Finish';
        $conf['lang_welcometext1'] = 'Welcome to the master of installation';
        $conf['lang_welcometext2'] = 'Now the master of installation will determine adjustments of a server. The test catalogue and a temporary file will be created. Also will be detected maximum execution time of script. This check is necessary for the further confident work of script';
        $conf['lang_welcometext3'] = 'Press &laquo;'.$conf['lang_buttonnext'].'&raquo; to continue.';
        $conf['lang_testservertext1'] = 'Results testing of the server<br /><br />';
        $conf['lang_testservertext2'] = 'During execution of the script there were errors. Visit '.$conf['lang_supportforum'].' or write to the author. Do not forget to describe a problem in detail.';
        $conf['lang_testservertext3'] = 'All ready to continue execution script. Press &laquo;'.$conf['lang_buttonnext'].'&raquo; to continue.';
        $conf['lang_access_denied'] = 'Access denied!<br />Check correctness of the name and password.';
        $conf['lang_can_select_lang'] = 'You can choose preferred language';
        $conf['lang_setmode'] = 'Choice of action mode';
        $conf['lang_you_setchoice'] = 'Select an action';
        $conf['lang_you_setmode'] = 'Below possible actions are offered<br /><br />';
        $conf['lang_setmode_compr_files'] = 'Compress files';
        $conf['lang_setmode_compr_files1'] = 'Compress files on server to archive';
        $conf['lang_setmode_decompr_files'] = 'Decompress files';
        $conf['lang_setmode_decompr_files1'] = 'Decompress files from uploaded archive to server';
        $conf['lang_comprpath'] = 'Compress options';
        $conf['lang_enter_comprpath'] = 'Specify a way on a server, compress of files in archive whence will begin';
        $conf['lang_comprresult'] = 'Compress result';
        $conf['lang_comprresult2'] = 'Following files have been compressed into a file auae_data.auae:';
        $conf['lang_decomprpath'] = 'Decompress options';
        $conf['lang_enter_decomprpath'] = 'Specify a way on a server where decompressing of files from archive will begin';
        $conf['lang_other_files'] = 'other files';
        $conf['lang_newdir_perms'] = 'rights to new directories';
        $conf['lang_overwrite_files'] = 'overwrite files';
        $conf['lang_decomprresult'] = 'Decompress result';
        $conf['lang_decomprresult2'] = 'From a file auae_data.auae following files have been decompressed:';
        $conf['lang_exludesubdirs'] = 'exlude subdirectories';
        $conf['lang_onlythisext'] = 'only this extentions';
        $conf['lang_onlyfilesize'] = 'not more ('.$conf['lang_bytes'].')';
        $conf['lang_exludethisdir'] = 'not this directories';
        $conf['lang_setmode_chmod_files'] = 'Change rights';
        $conf['lang_setmode_chmod_files1'] = 'Change rights at files and directories';
        $conf['lang_enter_chmodpath'] = 'Specify a way on a server, where it is necessary change rights';
        $conf['lang_dir_perms'] = 'rights to directories';
        $conf['lang_chmodresult'] = 'Result of change of the rights';
        $conf['lang_chmodresult2'] = 'The rights at the following files and directories were changed:';
        $conf['lang_archivename'] = 'Name of the archive';
        $conf['lang_selarchive'] = 'Archive to decompress';
        $conf['lang_notsupportedarchive'] = 'Not supported archive!';
        $conf['lang_setmode_uninst_files'] = 'Uninstall files';
        $conf['lang_setmode_uninst_files1'] = 'Uninstall files unpacked with AlfaUngzipper (rollback)';
        $conf['lang_nolog_file'] = 'not make uninstall log';
        $conf['lang_uninstpath'] = 'Uninstall options';
        $conf['lang_sellog'] = 'Select uninstall log';
        $conf['lang_uninstresult'] = 'Uninstall result';
        $conf['lang_uninstresult2'] = 'Following files and directories have been uninstalled:';
        $conf['lang_authorisation'] = 'Authorisation';
        $conf['lang_login'] = 'Login';
        $conf['lang_password'] = 'Password';
        $conf['lang_donateus'] = 'You can support the development of the script, making a donation:<br />';
        $conf['lang_donateus2'] = '<br />The script depends on you!';
        return $conf;
        }

function lang_ret_arr_ru() {
        $conf['lang_id'] = 'ru';
        $conf['lang_localisation'] = '';
        $conf['lang_localisator'] = ''; /*localisator's name*/
        $conf['lang_localisatoraddr'] = ''; /*localisator's mail*/
        $conf['lang_localisationtotal'] = '';
        $conf['lang_supportforum'] = "<a href=\"http://alfaungzipper.com\">сайт поддержки</a>";
        $conf['lang_makedir'] = 'Создаётся каталог';
        $conf['lang_unpack'] = 'Распаковывается файл';
        $conf['lang_overwrite'] = 'Перезаписывается файл';
        $conf['lang_error'] = 'Ошибка';
        $conf['lang_file'] = 'файл';
        $conf['lang_exists'] = 'существует';
        $conf['lang_missed'] = 'пропущен';
        $conf['lang_bytes'] = 'байт';
        $conf['lang_rirhtsfor'] = 'права для';
        $conf['lang_rirhtsfor_end_sp'] = ''; /* leave this position blanked. can be needed only if your language need word AFTER filename */
        $conf['lang_rightsseted'] = 'установлены в';
        $conf['lang_rightsseted_end_sp'] = ''; /* leave this position blanked. can be needed only if your language needs word AFTER rights */
        $conf['lang_passed'] = 'ТЕСТ ПРОЙДЕН';
        $conf['lang_failed'] = 'ТЕСТ НЕ ПРОЙДЕН';
        $conf['lang_version'] = 'версия';
        $conf['lang_extentionloaded '] = 'расширение загружено';
        $conf['lang_maxexecutiontime'] = 'макс. время исполнения';
        $conf['lang_sec'] = 'сек';
        $conf['lang_maketestdir'] = 'создаётся тестовый каталог';
        $conf['lang_opentestfileforwr'] = 'открывается тестовый файл для записи';
        $conf['lang_writingintestfile'] = 'запись в тестовый файл';
        $conf['lang_opentestfileforread'] = 'открывется тестовый файл для чтения';
        $conf['lang_readingfromtestfile'] = 'чтение из тестового файла';
        $conf['lang_tooltextwelcome'] = 'Добро пожаловать';
        $conf['lang_tooltexttesserver'] = 'Тест настроек сервера';
        $conf['lang_buttonnext'] = 'Далее';
        $conf['lang_buttonback'] = 'Назад';
        $conf['lang_buttonfinish'] = 'Готово';
        $conf['lang_welcometext1'] = 'Добро пожаловать в мастер установки';
        $conf['lang_welcometext2'] = 'Сейчас мастер установки определит настройки сервера. Будет создан тестовый каталог и временный файл. Также будет исследовано максимальное время, отпущенное для работы скрипта. Эта проверка необходима для дальнейшей уверенной работы скрипта';
        $conf['lang_welcometext3'] = 'Для продолжения нажмите &laquo;'.$conf['lang_buttonnext'].'&raquo;.';
        $conf['lang_testservertext1'] = 'Результаты тестирования сервера<br /><br />';
        $conf['lang_testservertext2'] = 'В процессе работы скрипта произошли ошибки. Посетите '.$conf['lang_supportforum'].' или напишите автору. Не забудьте подробно описать проблему.';
        $conf['lang_testservertext3'] = 'Всё готово для продолжения работы скрипта. Для продолжения нажмите &laquo;'.$conf['lang_buttonnext'].'&raquo;';
        $conf['lang_access_denied'] = 'Доступ запрещён!<br />Проверьте правильность введённых имени и пароля.';
        $conf['lang_can_select_lang'] = 'Можете выбрать предпочитаемый язык';
        $conf['lang_setmode'] = 'Выбор режима работы';
        $conf['lang_you_setchoice'] = 'Сделайте выбор';
        $conf['lang_you_setmode'] = 'Ниже предложены возможные действия<br /><br />';
        $conf['lang_setmode_compr_files'] = 'Упаковать файлы';
        $conf['lang_setmode_compr_files1'] = 'Упаковать файлы, находящиеся на сервере в архив';
        $conf['lang_setmode_decompr_files'] = 'Распаковать файлы';
        $conf['lang_setmode_decompr_files1'] = 'Распаковать файлы из загруженного архива на сервер';
        $conf['lang_comprpath'] = 'Опции упаковки';
        $conf['lang_enter_comprpath'] = 'Укажите путь на сервере, откуда начнётся упаковка файлов в архив';
        $conf['lang_comprresult'] = 'Результат упаковки';
        $conf['lang_comprresult2'] = 'В файл auae_data.auae были упакованы следующие файлы:';
        $conf['lang_decomprpath'] = 'Опции распаковки';
        $conf['lang_enter_decomprpath'] = 'Укажите путь на сервере, куда начнётся распаковка файлов из архива';
        $conf['lang_other_files'] = 'остальные файлы';
        $conf['lang_newdir_perms'] = 'права на создаваемые каталоги';
        $conf['lang_overwrite_files'] = 'перезаписывать файлы';
        $conf['lang_decomprresult'] = 'Результат распаковки';
        $conf['lang_decomprresult2'] = 'Из файла auae_data.auae были распакованы следующие файлы:';
        $conf['lang_exludesubdirs'] = 'не обрабатывть подкаталоги';
        $conf['lang_onlythisext'] = 'только с расширением';
        $conf['lang_onlyfilesize'] = 'не более ('.$conf['lang_bytes'].')';
        $conf['lang_exludethisdir'] = 'не эти каталоги';
        $conf['lang_setmode_chmod_files'] = 'Изменить права';
        $conf['lang_setmode_chmod_files1'] = 'Изменить права у файлов и каталогов';
        $conf['lang_enter_chmodpath'] = 'Укажите путь на сервере, где необходимо изменить права';
        $conf['lang_dir_perms'] = 'права на каталоги';
        $conf['lang_chmodresult'] = 'Результат смены прав';
        $conf['lang_chmodresult2'] = 'Были изменены права у следующих файлов и каталогов:';
        $conf['lang_archivename'] = 'Имя архива';
        $conf['lang_selarchive'] = 'Архив для распаковки';
        $conf['lang_notsupportedarchive'] = 'Не поддерживаемый архив!';
        $conf['lang_setmode_uninst_files'] = 'Деинсталляция файлов';
        $conf['lang_setmode_uninst_files1'] = 'Деинсталляция файлов, распакованных с помощью AlfaUngzipper (откат)';
        $conf['lang_nolog_file'] = 'не создавать лог деинсталляции';
        $conf['lang_uninstpath'] = 'Опции деинсталляции';
        $conf['lang_sellog'] = 'Укажите лог деинсталляции';
        $conf['lang_uninstresult'] = 'Результат деинсталляции';
        $conf['lang_uninstresult2'] = 'Следующие файлы и каталоги были удалены:';
        $conf['lang_authorisation'] = 'Авторизация';
        $conf['lang_login'] = 'Логин';
        $conf['lang_password'] = 'Пароль';
        $conf['lang_donateus'] = 'Вы можете поддержать разработку скрипта, сделав пожертвование:<br />';
        $conf['lang_donateus2'] = '<br />Развитие скрипта зависит от вас!';
        return $conf;
        }

function lang_ret_arr_be() {
        $conf['lang_id'] = 'be';
        $conf['lang_localisation'] = 'пераклад';
        $conf['lang_localisator'] = 'WarGot'; /*localisator's name*/
        $conf['lang_localisatoraddr'] = 'mailto:wargot@gmail.com'; /*localisator's mail*/
        $conf['lang_localisationtotal'] = ',&nbsp;'.$conf['lang_localisation'].':&nbsp;<a href="'.$conf['lang_localisatoraddr'].'" class="copyright">'.$conf['lang_localisator'].'</a>';
        $conf['lang_supportforum'] = "<a href=\"http://alfaungzipper.com\">бачына падтрымкі</a>";
        $conf['lang_makedir'] = 'Ствараецца каталог';
        $conf['lang_unpack'] = 'Распакоўваецца файл';
        $conf['lang_overwrite'] = 'Перазапісваецца файл';
        $conf['lang_error'] = 'Памылка';
        $conf['lang_file'] = 'файл';
        $conf['lang_exists'] = 'існуе';
        $conf['lang_missed'] = 'прапушчаны';
        $conf['lang_bytes'] = 'байт';
        $conf['lang_rirhtsfor'] = 'правы для';
        $conf['lang_rirhtsfor_end_sp'] = ''; /* leave this position blanked. can be needed only if your language need word AFTER filename */
        $conf['lang_rightsseted'] = 'устаноўленыя ў';
        $conf['lang_rightsseted_end_sp'] = ''; /* leave this position blanked. can be needed only if your language needs word AFTER rights */
        $conf['lang_passed'] = 'ТЭСТ ПРОЙДЗЕНЫ';
        $conf['lang_failed'] = 'ТЭСТ НЕ ПРОЙДЗЕНЫ';
        $conf['lang_version'] = 'версія';
        $conf['lang_extentionloaded '] = 'пашырэнне iснуе';
        $conf['lang_maxexecutiontime'] = 'максімальны час выканання';
        $conf['lang_sec'] = 'сек';
        $conf['lang_maketestdir'] = 'ствараецца тэставы каталаг';
        $conf['lang_opentestfileforwr'] = 'адчыняецца тэставы файл для запісу';
        $conf['lang_writingintestfile'] = 'запіс у тэставы файл';
        $conf['lang_opentestfileforread'] = 'адчыняецца тэставы файл для чытання';
        $conf['lang_readingfromtestfile'] = 'чытанне з тэставага файла';
        $conf['lang_tooltextwelcome'] = 'Сардэчна запрашаем';
        $conf['lang_tooltexttesserver'] = 'Тэст налад сервера';
        $conf['lang_buttonnext'] = 'Далей';
        $conf['lang_buttonback'] = 'Назад';
        $conf['lang_buttonfinish'] = 'Усё зроблена';
        $conf['lang_welcometext1'] = 'Сардэчна запрашаем у майстра налады';
        $conf['lang_welcometext2'] = 'Зараз майстар налады вызначыць налады сервера. Будзе створаны теставы каталог і часовы файл. Таксама будзе даследаван максімальны час, дапушчаны для працы скрыпту. Гэтая праверка неабходная для далейшай упэўненай працы скрыпту';
        $conf['lang_welcometext3'] = 'Для працягу націсніце &laquo;'.$conf['lang_buttonnext'].'&raquo;.';
        $conf['lang_testservertext1'] = 'Вынікі тэставання сервера<br /><br />';
        $conf['lang_testservertext2'] = 'У працэсе працы скрыпту адбыліся памылкі. Наведаеце '.$conf['lang_supportforum'].' або напішыце автору. Не забудзьце подробно описать праблему.';
        $conf['lang_testservertext3'] = 'Усё зроблена для працягу працы скрыпту. Для працягу націсніце &laquo;'.$conf['lang_buttonnext'].'&raquo;';
        $conf['lang_access_denied'] = 'Доступ забаронены !<br />Праверце правільнасць уведзенага імені і пароля.';
        $conf['lang_can_select_lang'] = 'Можаце вылучыць упадабаную мову';
        $conf['lang_setmode'] = 'Выбранне рэжыму працы';
        $conf['lang_you_setchoice'] = 'Зрабіце выбранне';
        $conf['lang_you_setmode'] = 'Ніжэй Вы бачыце магчымыя дзеянні <br /><br />';
        $conf['lang_setmode_compr_files'] = 'Спакаваць файлы';
        $conf['lang_setmode_compr_files1'] = 'Спакаваць файлы, змешчаныя на серверы ў архіў ';
        $conf['lang_setmode_decompr_files'] = 'Распакаваць файлы';
        $conf['lang_setmode_decompr_files1'] = 'Распакаваць файлы з загружанага архіва на сервер';
        $conf['lang_comprpath'] = 'Налады пакавання';
        $conf['lang_enter_comprpath'] = 'Пакажыце шлях на серверы, адкуль пачнецца пакаванне файлаў у архіў';
        $conf['lang_comprresult'] = 'Вынік пакавання';
        $conf['lang_comprresult2'] = 'У файл auae_data.auae былі запакаваныя наступныя файлы:';
        $conf['lang_decomprpath'] = 'Налады распакаванні';
        $conf['lang_enter_decomprpath'] = 'Пакажыце шлях на серверы, куды пачнецца распакаванне файлаў з архіва';
        $conf['lang_other_files'] = 'астатнія файлы';
        $conf['lang_newdir_perms'] = 'правы на ствараемыя каталогі';
        $conf['lang_overwrite_files'] = 'перазапісваць файлы';
        $conf['lang_decomprresult'] = 'Вынік распакавання';
        $conf['lang_decomprresult2'] = 'З файла auae_data.auae былі распакаваныя наступныя файлы:';
        $conf['lang_exludesubdirs'] = 'не апрацоўваць каталогi';
        $conf['lang_onlythisext'] = 'толькі з пашырэннем';
        $conf['lang_onlyfilesize'] = 'не больш ('.$conf['lang_bytes'].')';
        $conf['lang_exludethisdir'] = 'не гэтыя каталогi';
        $conf['lang_setmode_chmod_files'] = 'Змяніць правы';
        $conf['lang_setmode_chmod_files1'] = 'Змяніць правы ў файлаў і каталогаў';
        $conf['lang_enter_chmodpath'] = 'Пакажыце шлях на серверы, дзе неабходна змяніць правы';
        $conf['lang_dir_perms'] = 'правы на каталогі';
        $conf['lang_chmodresult'] = 'Вынiк змены прав';
        $conf['lang_chmodresult2'] = 'Былі змененыя правы ў наступных файлаў і каталогаў:';
        $conf['lang_archivename'] = 'Iмя архiва';
        $conf['lang_selarchive'] = 'Архiў для распакоўкi';
        $conf['lang_notsupportedarchive'] = 'Не падтрымлiваемы архiў!';
        $conf['lang_setmode_uninst_files'] = 'Выдаленне файлаў';
        $conf['lang_setmode_uninst_files1'] = 'Выдаленне файлаў, распакаваных з дапамогай AlfaUngzipper (адкат)';
        $conf['lang_nolog_file'] = 'не ствараць лог выдалення';
        $conf['lang_uninstpath'] = 'Налады выдалення';
        $conf['lang_sellog'] = 'Азначце лог выдалення';
        $conf['lang_uninstresult'] = 'Вынік выдалення';
        $conf['lang_uninstresult2'] = 'Наступныя файлы і каталогі былі выдалены:';
        $conf['lang_authorisation'] = 'Аўтарызацыя';
        $conf['lang_login'] = 'Логiн';
        $conf['lang_password'] = 'Пароль';
        $conf['lang_donateus'] = 'Вы можаце падтрымаць распрацоўку скрыпту, зрабіўшы ахвяраванне:<br />';
        $conf['lang_donateus2'] = '<br />Развiццё скрыпту залежыць ад вас!';
        return $conf;
        }

function lang_ret_arr_et() {
        $conf['lang_id'] = 'et';
        $conf['lang_localisation'] = 'paigustumine';
        $conf['lang_localisator'] = 'Acsid'; /*localisator's name*/
        $conf['lang_localisatoraddr'] = 'mailto:acsid@hot.ee'; /*localisator's mail*/
        $conf['lang_localisationtotal'] = ',&nbsp;'.$conf['lang_localisation'].':&nbsp;<a href="'.$conf['lang_localisatoraddr'].'" class="copyright">'.$conf['lang_localisator'].'</a>';
        $conf['lang_supportforum'] = "<a href=\"http://alfaungzipper.com\">Toetamise sait</a>";
        $conf['lang_makedir'] = 'rajab kataloog';
        $conf['lang_unpack'] = 'fail pakkib lahti';
        $conf['lang_overwrite'] = 'fail umberkirjutab';
        $conf['lang_error'] = 'Viga';
        $conf['lang_file'] = 'fail';
        $conf['lang_exists'] = 'eksisteerib';
        $conf['lang_missed'] = 'labi lasatud';
        $conf['lang_bytes'] = 'bait';
        $conf['lang_rirhtsfor'] = 'oigused'; /*---(для) должно стоять в конце предложения а то тебя непоймут..*/
        $conf['lang_rirhtsfor_end_sp'] = 'jaoks'; /* leave this position blanked. can be needed only if your language need word AFTER filename */
        $conf['lang_rightsseted'] = 'paigutatud'; /*-----(в) должно быть в конце предложения а то тебя не поймут*/
        $conf['lang_rightsseted_end_sp'] = 'sisse'; /* leave this position blanked. can be needed only if your language needs word AFTER rights */
        $conf['lang_passed'] = 'test sooritanud';
        $conf['lang_failed'] = 'test ie ole sooritanud';
        $conf['lang_version'] = 'versioon';
        $conf['lang_extentionloaded '] = 'laiendamine on koormunud';
        $conf['lang_maxexecutiontime'] = 'maksimaalne taitamise aeg';
        $conf['lang_sec'] = 'Sekundit';
        $conf['lang_maketestdir'] = 'rajatakse teksti kataloog';
        $conf['lang_opentestfileforwr'] = 'avatakse teksti fail kirjutamise jaoks';
        $conf['lang_writingintestfile'] = 'kirjapanek teksti faili sisse';
        $conf['lang_opentestfileforread'] = 'avatakse teksti fail lugemise jaoks';
        $conf['lang_readingfromtestfile'] = 'lugemine teksti failist';
        $conf['lang_tooltextwelcome'] = 'Tere tulemast';
        $conf['lang_tooltexttesserver'] = 'Test serveri haalestusi';
        $conf['lang_buttonnext'] = 'Edasi';
        $conf['lang_buttonback'] = 'Tagasi';
        $conf['lang_buttonfinish'] = 'On valmis';
        $conf['lang_welcometext1'] = 'Tere tulemast paigutamise meistrisse';
        $conf['lang_welcometext2'] = 'Praegu  paigutamise meister  paneb  serveri haalestusi.Tulevikul skipt loomatab teksti kataloog ja ajutine  fail.Tulevikul skipt uurima maksimaalne aeg, labi loonud skripti tootamise jaoks.See kontroll on tarvilik skripti oguse tootamise jaoks.';
        $conf['lang_welcometext3'] = 'Jatkamise jaoks andke pihta &laquo;'.$conf['lang_buttonnext'].'&raquo;.';
        $conf['lang_testservertext1'] = 'Serveri testimese tagajarg <br /><br />';
        $conf['lang_testservertext2'] = 'Skripti tootamise ajal tekkis viga.Kulastagt '.$conf['lang_supportforum'].' voi kirjutage kirja autirile. Arge unustage uksikasjalikult kirjeldata oma pribleemi.';
        $conf['lang_testservertext3'] = 'Koik on valmis skripti too jatkamise jaoks.Jatkamise jaoks andke pihta &laquo;'.$conf['lang_buttonnext'].'&raquo;';
        $conf['lang_access_denied'] = 'Sisseminek keelatud!<br />Kontrollige nime ehk parolli.';
        $conf['lang_can_select_lang'] = 'Voite valida programmi keel';
        $conf['lang_setmode'] = 'Too reziimi viis valimus';
        $conf['lang_you_setchoice'] = 'Tehke valikut';
        $conf['lang_you_setmode'] = 'Madalamalt on pakkutud voimalikut tegevused<br /><br />';
        $conf['lang_setmode_compr_files'] = 'Pakkima faile';
        $conf['lang_setmode_compr_files1'] = 'Pakkima failid,mis on serveris arhivi sisse';
        $conf['lang_setmode_decompr_files'] = 'Pakkima failid lahti';
        $conf['lang_setmode_decompr_files1'] = 'Lahti pakkima koormunud arhisvist serverisse';
        $conf['lang_comprpath'] = 'Pakkimise haalestused';
        $conf['lang_enter_comprpath'] = 'Naidake serveris teed, kust hakkavad pakkima failid arhivi sisse';
        $conf['lang_comprresult'] = 'Pakkimise tagajarg';
        $conf['lang_comprresult2'] = 'auae_data.auae failisse olid pakkitud jargmised failid:';
        $conf['lang_decomprpath'] = 'Lahti pakkimise viga';
        $conf['lang_enter_decomprpath'] = 'Naidake serveris teed, kuhu hakkavad failid lahti pakkima arhivist';
        $conf['lang_other_files'] = 'Jaanud failid';
        $conf['lang_newdir_perms'] = 'Oigused loomatavate katalogide jaoks';
        $conf['lang_overwrite_files'] = 'Failid umberkirjutavad';
        $conf['lang_decomprresult'] = 'Lahti pakkimise tagajarg';
        $conf['lang_decomprresult2'] = 'auae_data.auae failist on lahti pakkitud jargmised failid:';
        $conf['lang_exludesubdirs'] = 'kirjutamise failid';
        $conf['lang_onlythisext'] = 'ainult see extention';
        $conf['lang_onlyfilesize'] = 'mitte rohkem kui ('.$conf['lang_bytes'].')';
        $conf['lang_exludethisdir'] = 'mitte need katalogid';
        $conf['lang_setmode_chmod_files'] = 'Muutma &#245;igus';
        $conf['lang_setmode_chmod_files1'] = 'Muutma faili ja katalogide &#245;igused ';
        $conf['lang_enter_chmodpath'] = 'Naidake koht serveris,kus v&#245;ib  &#245;igused muutma';
        $conf['lang_dir_perms'] = 'katalogide  &#245;igused';
        $conf['lang_chmodresult'] = '&#245;iguste muutmise tagaj&#228;rg';
        $conf['lang_chmodresult2'] = '&#245;igused olid muutunud jargmistel failidel ja katalogidel:';
        $conf['lang_archivename'] = 'Arhivi nimi';
        $conf['lang_selarchive'] = 'Arhiv et ara pakendada';
        $conf['lang_notsupportedarchive'] = 'Mitte hoidav arhiv';
        $conf['lang_setmode_uninst_files'] = 'Failide deinstalerimine';
        $conf['lang_setmode_uninst_files1'] = 'Palun deinstalli failid mis on valjapakitud  AlfaUngzipperiga (rollback)';
        $conf['lang_nolog_file'] = 'Ara tee deinstallise logi';
        $conf['lang_uninstpath'] = 'Deinstallerimise soovitused';
        $conf['lang_sellog'] = 'Vali deinstallerimise log';
        $conf['lang_uninstresult'] = 'Deinstallerimise tulek';
        $conf['lang_uninstresult2'] = 'Jargmised failid on deinstallitud:';
        $conf['lang_authorisation'] = 'Logi sisse';
        $conf['lang_login'] = 'Nimi';
        $conf['lang_password'] = 'Sqlanimi';
        $conf['lang_donateus'] = 'Te vqite andma proektile nattuke raha:<br />';
        $conf['lang_donateus2'] = '<br />Script sqltub teie rahast!';
        return $conf;
        }

function lang_ret_arr_de() {
        $conf['lang_id'] = 'de';
        $conf['lang_localisation'] = '&#252;bersetzung';
        $conf['lang_localisator'] = ''; /*localisator's name*/
        $conf['lang_localisatoraddr'] = ''; /*localisator's mail*/
        $conf['lang_localisationtotal'] = '';
        $conf['lang_supportforum'] = "<a href=\"http://alfaungzipper.com\">Die Web-Seite der Unterst&#252;tzung</a>";
        $conf['lang_makedir'] = 'Ordner wird erstellt';
        $conf['lang_unpack'] = 'Datei wird entpackt';
        $conf['lang_overwrite'] = 'Datei wird ersetzt';
        $conf['lang_error'] = 'Fehler';
        $conf['lang_file'] = 'Datei';
        $conf['lang_exists'] = 'existiert';
        $conf['lang_missed'] = 'auslassen';
        $conf['lang_bytes'] = 'byte';
        $conf['lang_rirhtsfor'] = 'Rechte f&#252;r';
        $conf['lang_rirhtsfor_end_sp'] = ''; /* leave this position blanked. can be needed only if your language need word AFTER filename */
        $conf['lang_rightsseted'] = 'festgelegt in';
        $conf['lang_rightsseted_end_sp'] = ''; /* leave this position blanked. can be needed only if your language needs word AFTER rights */
        $conf['lang_passed'] = 'TEST BESTANDEN';
        $conf['lang_failed'] = 'TEST NICHT BESTANDEN';
        $conf['lang_version'] = 'version';
        $conf['lang_extentionloaded '] = 'erweiterung geladen';
        $conf['lang_maxexecutiontime'] = 'max. Zeit';
        $conf['lang_sec'] = 'sec';
        $conf['lang_maketestdir'] = 'Testordner wird erstellt';
        $conf['lang_opentestfileforwr'] = 'Testdatei wird zum beschreiben ge&#246;fnet';
        $conf['lang_writingintestfile'] = 'in Testdatei schreiben';
        $conf['lang_opentestfileforread'] = 'Testdatei wird zum lesen ge&#246;fnet';
        $conf['lang_readingfromtestfile'] = 'Testdatei wird gelesen';
        $conf['lang_tooltextwelcome'] = 'Willkommen';
        $conf['lang_tooltexttesserver'] = 'Servereinstellungen';
        $conf['lang_buttonnext'] = 'Weiter';
        $conf['lang_buttonback'] = 'Zur&#252;ck';
        $conf['lang_buttonfinish'] = 'Fertig';
        $conf['lang_welcometext1'] = 'Wilkommen in die einstellungen';
        $conf['lang_welcometext2'] = 'Jetzt werden die Servereinstellungen ferstgelegt. Es wird ein Testordner erstellt und eine Temp Datei. Maximale Zeit f&#252;r den Script wird erkundet. Dieser Test wird f&#252;r die witere arbeit des scripts ben&#246;tigt';
        $conf['lang_welcometext3'] = 'Um fortzufahren dr&#252;cken sie &laquo;'.$conf['lang_buttonnext'].'&raquo;.';
        $conf['lang_testservertext1'] = 'Ergebnisse des Servertests<br /><br />';
        $conf['lang_testservertext2'] = 'W&#228;hrend der Arbeit des Scripts sind Fehler aufgetreten. Besuchen sie '.$conf['lang_supportforum'].' oder schreiben sie dem Autor. Vergessen sie nicht das problen ausf&#252;hrlich zu beschreiben.';
        $conf['lang_testservertext3'] = 'Alles ist bereit f&#252;r die Vortsetzung des scripts. Um fortzufahren dr&#252;cken sie &laquo;'.$conf['lang_buttonnext'].'&raquo;';
        $conf['lang_access_denied'] = 'Zugriff abgelehnt!<br />&#252;berpr&#252;fen sie den Benutzernamen und das Password';
        $conf['lang_can_select_lang'] = 'Sie k&#246;nnen die bevorzugte Sprache ausw&#228;hlen';
        $conf['lang_setmode'] = 'Auswahl des Arbeitsmodus';
        $conf['lang_you_setchoice'] = 'W&#228;zhlen sie aus';
        $conf['lang_you_setmode'] = 'Unten sind m&#246;glichen Optionen<br /><br />';
        $conf['lang_setmode_compr_files'] = 'Datei kompremieren';
        $conf['lang_setmode_compr_files1'] = 'Dateien kompremieren, die sich auf dem Server im Archiv befinden';
        $conf['lang_setmode_decompr_files'] = 'Dateien entpacken';
        $conf['lang_setmode_decompr_files1'] = 'Dateien aus dem Archiv auf dem Server entpacken';
        $conf['lang_comprpath'] = 'Kompremierungsoptionen';
        $conf['lang_enter_comprpath'] = 'W&#228;hlen sie das Verzeichniss, in dem die Kompriemierung der Dateien in ein Archiv beginnen soll';
        $conf['lang_comprresult'] = 'Ergebniss der kompremierung';
        $conf['lang_comprresult2'] = 'In der Datei auae_data.auae wurden folgende Dateien kompremiert:';
        $conf['lang_decomprpath'] = 'Entpackungsoptionen';
        $conf['lang_enter_decomprpath'] = 'W&#228;hlen sie das Verzeichniss, in dem das Archiv entpackt werden soll';
        $conf['lang_other_files'] = 'andere Dateien';
        $conf['lang_newdir_perms'] = 'rechte f&#252;r das erstellen von Ordnern';
        $conf['lang_overwrite_files'] = 'Dateien werden ersetzt';
        $conf['lang_decomprresult'] = 'Ergebnisse des entpackens';
        $conf['lang_decomprresult2'] = 'Aus der datei auae_data.auae wurden folgende Dateien entpackt:';
        $conf['lang_exludesubdirs'] = 'unterordner nicht bearbeiten';
        $conf['lang_onlythisext'] = 'nur mit Erlaubniss';
        $conf['lang_onlyfilesize'] = 'nicht mehr als ('.$conf['lang_bytes'].')';
        $conf['lang_exludethisdir'] = 'nicht diese Ordner';
        $conf['lang_setmode_chmod_files'] = 'Rechte &#228;ndern';
        $conf['lang_setmode_chmod_files1'] = 'Rechte der Dateien und Ordner &#228;ndern';
        $conf['lang_enter_chmodpath'] = 'Geben sie das Verzeichniss an, in dem die Rechte ver&#228;ndert werden m&#252;ssen';
        $conf['lang_dir_perms'] = 'rechte f&#252;r ordner';
        $conf['lang_chmodresult'] = 'Ergebniss der &#228;nderund von Rechten';
        $conf['lang_chmodresult2'] = 'Die Rechte f&#252;r folgende Dateien und Ordner wurden ver&#228;ndert:';
        $conf['lang_archivename'] = 'Name des Archivs';
        $conf['lang_selarchive'] = 'Archiv, um zu dekomprimieren';
        $conf['lang_notsupportedarchive'] = 'Nicht gestütztes Archiv!';
        $conf['lang_setmode_uninst_files'] = 'Uninstall Akten';
        $conf['lang_setmode_uninst_files1'] = 'Uninstall Akten packten mit AlfaUngzipper aus (Preissenkung)';
        $conf['lang_nolog_file'] = 'uninstall Maschinenbordbuch nicht bilden';
        $conf['lang_uninstpath'] = 'Uninstall Wahlen';
        $conf['lang_sellog'] = 'Uninstall Maschinenbordbuch vorwählen';
        $conf['lang_uninstresult'] = 'Uninstall Resultat';
        $conf['lang_uninstresult2'] = 'Folgende Akten und Verzeichnisse sind uninstalled gewesen:';
        $conf['lang_authorisation'] = 'Ermächtigung';
        $conf['lang_login'] = 'Logon';
        $conf['lang_password'] = 'Kennwort';
        $conf['lang_donateus'] = 'Du kannst die Entwicklung des Indexes stützen und eine Abgabe bilden:<br />';
        $conf['lang_donateus2'] = '<br />Der Index hängt von dir ab!';
        return $conf;
        }

function lang_ret_arr_uk() {
        $conf['lang_id'] = 'uk';
        $conf['lang_localisation'] = 'переклад';
        $conf['lang_localisator'] = 'Орк'; /*localisator's name*/
        $conf['lang_localisatoraddr'] = 'http://www.delphidc.alfamoon.com'; /*localisator's mail*/
        $conf['lang_localisationtotal'] = ',&nbsp;'.$conf['lang_localisation'].':&nbsp;<a href="'.$conf['lang_localisatoraddr'].'" class="copyright">'.$conf['lang_localisator'].'</a>';
        $conf['lang_supportforum'] = "<a href=\"http://alfaungzipper.com\">сайт підтримки</a>";
        $conf['lang_makedir'] = 'Створюється каталог';
        $conf['lang_unpack'] = 'Распаковується файл';
        $conf['lang_overwrite'] = 'Перезаписується файл';
        $conf['lang_error'] = 'Помилка';
        $conf['lang_file'] = 'файл';
        $conf['lang_exists'] = 'існує';
        $conf['lang_missed'] = 'пропущенно';
        $conf['lang_bytes'] = 'байт';
        $conf['lang_rirhtsfor'] = 'права для';
        $conf['lang_rirhtsfor_end_sp'] = ''; /* leave this position blanked. can be needed only if your language need word AFTER filename */
        $conf['lang_rightsseted'] = 'встановлені у';
        $conf['lang_rightsseted_end_sp'] = ''; /* leave this position blanked. can be needed only if your language needs word AFTER rights */
        $conf['lang_passed'] = 'ТЕСТУВАННЯ ПРОЙДЕНЕНЕ';
        $conf['lang_failed'] = 'ТЕСТУВАННЯ НЕ ПРОЙДЕНЕ';
        $conf['lang_version'] = 'версія';
        $conf['lang_extentionloaded '] = 'розширення завантажене';
        $conf['lang_maxexecutiontime'] = 'макс. час завершення';
        $conf['lang_sec'] = 'сек';
        $conf['lang_maketestdir'] = 'Створюється тестовий каталог';
        $conf['lang_opentestfileforwr'] = 'відкривається тестовий файл для запису';
        $conf['lang_writingintestfile'] = 'запис до тестового файлу';
        $conf['lang_opentestfileforread'] = 'відкривається тестовий файл для читання';
        $conf['lang_readingfromtestfile'] = 'читання із тестового файла';
        $conf['lang_tooltextwelcome'] = 'Ласкаво просимо';
        $conf['lang_tooltexttesserver'] = 'Тестування сервера';
        $conf['lang_buttonnext'] = 'Далі';
        $conf['lang_buttonback'] = 'Назад';
        $conf['lang_buttonfinish'] = 'Готово';
        $conf['lang_welcometext1'] = 'Ласкаво просимо до мастра установки';
        $conf['lang_welcometext2'] = 'Зараз мастер установки визначить налаштунки сервера. Буде створен тестовий каталог і тимчасовий файл. Також буде визначене максимальний час, відпущений для працювання скрипта. Ця перевірка необхідна для подальшої впевненої роботи скрипта';
        $conf['lang_welcometext3'] = 'Для продовження натисніть &laquo;'.$conf['lang_buttonnext'].'&raquo;.';
        $conf['lang_testservertext1'] = 'Результати тестування сервера<br /><br />';
        $conf['lang_testservertext2'] = 'У процесі праці скрипта виникли помилки. Завітайте на '.$conf['lang_supportforum'].' або напишить автору. Не забудьте описати проблему.';
        $conf['lang_testservertext3'] = 'Все готово для продовження праці скрипта. Для продовження натисніть &laquo;'.$conf['lang_buttonnext'].'&raquo;';
        $conf['lang_access_denied'] = 'Доступ заборонен!<br />Перевірте правильність уведених імені та пароля.';
        $conf['lang_can_select_lang'] = 'Оберіть мову';
        $conf['lang_setmode'] = 'Вибір режима роботи';
        $conf['lang_you_setchoice'] = 'Зробіть вибір';
        $conf['lang_you_setmode'] = 'Нижче вказані доступні дії<br /><br />';
        $conf['lang_setmode_compr_files'] = 'Упакувати файли';
        $conf['lang_setmode_compr_files1'] = 'Упакувати файли, які знаходяться на сервері у архів';
        $conf['lang_setmode_decompr_files'] = 'Распакувати файли';
        $conf['lang_setmode_decompr_files1'] = 'Распакувати файли із завантаженого архіва на сервері';
        $conf['lang_comprpath'] = 'Опциї упаковки';
        $conf['lang_enter_comprpath'] = 'Вкажите шлях на сервері, звідки почнеться упаковка файлів до архиву';
        $conf['lang_comprresult'] = 'Результат упаковки';
        $conf['lang_comprresult2'] = 'У файл auae_data.auae були упаковані наступні файли:';
        $conf['lang_decomprpath'] = 'Опции распаковки';
        $conf['lang_enter_decomprpath'] = 'Вкажите шлях на сервері, куди почнеться распаковка файлів із архіва';
        $conf['lang_other_files'] = 'інші файли';
        $conf['lang_newdir_perms'] = 'права на створенні каталоги';
        $conf['lang_overwrite_files'] = 'перезаписувати файли';
        $conf['lang_decomprresult'] = 'Результат распаковки';
        $conf['lang_decomprresult2'] = 'Iз файла auae_data.auae були распаковани наступні файли:';
        $conf['lang_exludesubdirs'] = 'не обробляти підкаталоги';
        $conf['lang_onlythisext'] = 'тількі з розширенням';
        $conf['lang_onlyfilesize'] = 'не більш ('.$conf['lang_bytes'].')';
        $conf['lang_exludethisdir'] = 'не ці каталоги';
        $conf['lang_setmode_chmod_files'] = 'Змінити права';
        $conf['lang_setmode_chmod_files1'] = 'Змінити права у файлів та каталогів';
        $conf['lang_enter_chmodpath'] = 'Вкажите шлях на сервері, де потрібно змінити права';
        $conf['lang_dir_perms'] = 'права на каталоги';
        $conf['lang_chmodresult'] = 'Результат зміни прав';
        $conf['lang_chmodresult2'] = 'Були змінені права у наступних файлів та каталогів:';
        $conf['lang_archivename'] = 'Ім\'я архіву';
        $conf['lang_selarchive'] = 'Архів для розпакування';
        $conf['lang_notsupportedarchive'] = 'Не підтримуваний архів!';
        $conf['lang_setmode_uninst_files'] = 'Деінсталяція файлів';
        $conf['lang_setmode_uninst_files1'] = 'Деінсталяція файлів, розпакованих за допомогою Alfaungzipper (відкіт)';
        $conf['lang_nolog_file'] = 'не створювати лог деінсталяції';
        $conf['lang_uninstpath'] = 'Опції деінсталяції';
        $conf['lang_sellog'] = 'Укажіть лог деінсталяції';
        $conf['lang_uninstresult'] = 'Результат деінсталяції';
        $conf['lang_uninstresult2'] = 'Наступні файли й каталоги були вилучені:';
        $conf['lang_authorisation'] = 'Авторизація';
        $conf['lang_login'] = 'Логін';
        $conf['lang_password'] = 'Пароль';
        $conf['lang_donateus'] = 'Ви можете підтримати розробку скрипта, зробивши пожертвування:<br />';
        $conf['lang_donateus2'] = '<br />Розвиток скрипта залежить від вас!';
        return $conf;
        }

function _tpl_data() {
$gtpl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=[tpl]lang_charset[/tpl]" http-equiv="content-type" />
    <title>[tpl]progname[/tpl]</title>
    <style type="text/css">
         a{color:rgb(255,165,0);text-decoration:none;
         cursor: pointer;}
         b.r1{margin:0 5px}
         b.r2{margin:0 3px}
         b.r3{margin:0 2px}
         b.rtop1 b,b.rbottom1 b{display:block;background:rgb(153,153,153);height:1px;overflow:hidden}
         b.rtop1 b.r4,b.rbottom1 b.r4,b.rtop2 b.r4,b.rbottom2 b.r4,b.rtop3 b.r4,b.rbottom3 b.r4,b.rtop4 b.r4,b.rbottom4 b.r4{margin:0 1px;height:2px}
         b.rtop1,b.rbottom1{display:block;background:rgb(192,192,192)}
         b.rtop2 b,b.rbottom2 b{display:block;background:rgb(192,192,192);height:1px;overflow:hidden}
         b.rtop3 b,b.rbottom3 b{display:block;background:rgb(255,165,0);height:1px;overflow:hidden}
         b.rtop4 b,b.rbottom4 b{display:block;background:rgb(245,245,245);height:1px;overflow:hidden}
         b.rtop4,b.rbottom4,b.rtop2,b.rbottom2,b.rtop3,b.rbottom3{display:block;background:rgb(153,153,153)}
         body{color:rgb(0,0,0);background:rgb(192,192,192);font-family:Arial,Helvetica,sans-serif;cursor:default}
         div.nifty1{margin:0 14%;background:rgb(153,153,153)}
         div.nifty2{margin:0 1%;background:rgb(192,192,192)}
         div.nifty3{margin:0 1%;background:rgb(255,165,0)}
         div.nifty4{margin:0 1%;background:rgb(245,245,245)}
         input.buttons{border: 0px;color: rgb(153,153,153);background:rgb(255,165,0);font-weight:bold;cursor:pointer;  margin: 0px 6px 0px 6px;  padding: 0px 6px 0px 6px;}
         .copyright{color:rgb(192,192,192);font-size:small;font-weight:bold;}
         .divtitle{color:rgb(153,153,153);font-size:50px;font-weight:bold}
         .errprint {color: rgb(255,165,0);}
         .errbut {color: rgb(153,153,153);font-size: small;font-weight: bold;}
         </style>
</head>

<body>
    <form name="Form" action="[tpl]phpself[/tpl]" method="post">
         [tpl]datafields[/tpl]

         <div class="divtitle">[tpl]progname[/tpl]</div>
    <br />
    <div class="nifty1">
      <b class="rtop1">
        <b class="r1"></b>
        <b class="r2"></b>
        <b class="r3"></b>
        <b class="r4"></b>
      </b>

<table style="text-align: left; width: 100%; margin: auto; padding: 0px 5px 0px 5px;" border="0" cellpadding="0" cellspacing="0">

  <tbody>

    <tr>

      <td colspan="2" rowspan="1" style="width: 70%;"><span style="font-size: x-large; font-weight: bold; color: rgb(255,165,0);">[tpl]tooltext[/tpl]</span></td>

      <td align="right">
                      <a href="http://www.eomy.net" target="_blank">
                        <div class="nifty2" style="text-align: center; width: 100px;">
                          <b class="rtop2">
                            <b class="r1"></b>
                            <b class="r2"></b>
                            <b class="r3"></b>
                            <b class="r4"></b>
                          </b>
                          <span style="font-weight: bold; padding: 0px; margin: 5px; font-size: medium; color: rgb(153,153,153);">eomy.net</span>
                          <br />
                          <span style="font-size: xx-small; padding: 0px; margin: 5px; white-space: nowrap; color: rgb(153,153,153);">hosting sponsor</span>
                          <b class="rbottom2">
                            <b class="r4"></b>
                            <b class="r3"></b>
                            <b class="r2"></b>
                            <b class="r1"></b>
                          </b>
                        </div>
                      </a>

                </td>

    </tr>

    <tr>

      <td colspan="3" rowspan="1">
              <div class="nifty4" style="padding: 0px; margin: 0px; margin-top: 4px;">
                <b class="rtop4">
                  <b class="r1"></b>
                  <b class="r2"></b>
                  <b class="r3"></b>
                  <b class="r4"></b>
                </b>
              </div>

                </td>

    </tr>

    <tr>

      <td colspan="3" rowspan="1">
                <div style="height: 300px; vertical-align: top; background-color: #F5F5F5; padding: 5px;">[tpl]spannedtext[/tpl]</div>
                </td>

    </tr>

    <tr>

      <td colspan="3" rowspan="1">
              <div class="nifty4" style="padding: 0px; margin: 0px; margin-bottom: 4px;">
                <b class="rbottom4">
                  <b class="r4"></b>
                  <b class="r3"></b>
                  <b class="r2"></b>
                  <b class="r1"></b>
                </b>
              </div>

                </td>

    </tr>

    <tr>

      <td colspan="3" rowspan="1">
                <table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                <td>[tpl]copyright[/tpl]</td>
                <td style="text-align: right; width: 5%;">
                      <div class="nifty3">
                      <b class="rtop3">
                        <b class="r1"></b>
                        <b class="r2"></b>
                        <b class="r3"></b>
                        <b class="r4"></b>
                      </b>
                                                         <span style="white-space: nowrap;">[tpl]buttonbar[/tpl]</span>
                      <b class="rbottom3">
                        <b class="r4"></b>
                        <b class="r3"></b>
                        <b class="r2"></b>
                        <b class="r1"></b>
                      </b></div>
                </td>
                </tr>
                </tbody>
                </table>

                </td>

    </tr>

  </tbody>
</table>

      <b class="rbottom1">
        <b class="r4"></b>
        <b class="r3"></b>
        <b class="r2"></b>
        <b class="r1"></b>
      </b>
    </div>
</form>
</body>

</html>';

return $gtpl;
}

function error ($errdata) {
        global $conf, $output;
        $output .= '<span>'.$conf['lang_error'].': '.$errdata.'<span><br />';
        }

/*m*/
function chmode($path) {
        global $output, $site_root;
        @chdir($site_root);
        _get_chmodrec();
        return $output;
        }

function _get_chmodrec($parentdir='/') {
        global $conf, $output, $filegroup_list, $filegroup_activated, $filegroup_other_activated, $filegroup_perms, $filegroup_other_perms, $new_dir_perms, $new_dir_perms_on;

        $cwd = getcwd();
        $site_root = preg_replace('/\\\+/', '/', $cwd);

        if (file_exists('.htaccess')) {
                /*htaccess chmod*/
                if (preg_match('/\\.('.$filegroup_list[1].')$/i', '.htaccess') and $filegroup_activated[1]) {
                        if (@chmod('.htaccess', octdec('0'.$filegroup_perms[1]))) {
                                $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htaccess '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[1].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                }
                        } else if (preg_match('/\\.('.$filegroup_list[2].')$/i', '.htaccess') and $filegroup_activated[2]) {
                                if (@chmod('.htaccess', octdec('0'.$filegroup_perms[2]))) {
                                        $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htaccess '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[2].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                        }
                                } else if (preg_match('/\\.('.$filegroup_list[3].')$/i', '.htaccess') and $filegroup_activated[3]) {
                                        if (@chmod('.htaccess', octdec('0'.$filegroup_perms[3]))) {
                                                $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htaccess '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[3].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                }
                                        } else if ($filegroup_other_activated) {
                                                if (@chmod('.htaccess', octdec('0'.$filegroup_other_perms))) {
                                                        $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htaccess '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_other_perms.' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                        }
                                                }
                /*htaccess chmod*/
                }
        if (file_exists('.htpasswd')) {
                /*htpasswd chmod*/
                if (preg_match('/\\.('.$filegroup_list[1].')$/i', '.htpasswd') and $filegroup_activated[1]) {
                        if (@chmod('.htpasswd', octdec('0'.$filegroup_perms[1]))) {
                                $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htpasswd '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[1].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                }
                        } else if (preg_match('/\\.('.$filegroup_list[2].')$/i', '.htpasswd') and $filegroup_activated[2]) {
                                if (@chmod('.htpasswd', octdec('0'.$filegroup_perms[2]))) {
                                        $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htpasswd '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[2].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                        }
                                } else if (preg_match('/\\.('.$filegroup_list[3].')$/i', '.htpasswd') and $filegroup_activated[3]) {
                                        if (@chmod('.htpasswd', octdec('0'.$filegroup_perms[3]))) {
                                                $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htpasswd '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[3].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                }
                                        } else if ($filegroup_other_activated) {
                                                if (@chmod('.htpasswd', octdec('0'.$filegroup_other_perms))) {
                                                        $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/.htpasswd '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_other_perms.' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                        }
                                                }
                /*htpasswd chmod*/
                }


        foreach (glob('*') as $file) {
                if (substr($file, 0, 4) == 'auae') {continue;}
                /* extenshion fix. do not compress any *.auae */
                if (substr($file, -4, 4) == 'auae') {continue;}

                $cwd = getcwd();
                $site_root = preg_replace('/\\\+/', '/', $cwd);

                if (is_dir($file)) {
                        if ($new_dir_perms_on) {
                                if (@chmod($file, octdec('0'.$new_dir_perms))) {
                                        $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/'.$file.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$new_dir_perms.' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                        }
                                }
                        } else {
                                if (preg_match('/\\.('.$filegroup_list[1].')$/i', $file) and $filegroup_activated[1]) {
                                        if (@chmod($file, octdec('0'.$filegroup_perms[1]))) {
                                                $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/'.$file.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[1].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                }
                                        } else if (preg_match('/\\.('.$filegroup_list[2].')$/i', $file) and $filegroup_activated[2]) {
                                                if (@chmod($file, octdec('0'.$filegroup_perms[2]))) {
                                                        $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/'.$file.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[2].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                        }
                                                } else if (preg_match('/\\.('.$filegroup_list[3].')$/i', $file) and $filegroup_activated[3]) {
                                                        if (@chmod($file, octdec('0'.$filegroup_perms[3]))) {
                                                                $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/'.$file.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[3].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                                }

                                                        } else if ($filegroup_other_activated) {
                                                                if (@chmod($file, octdec('0'.$filegroup_other_perms))) {
                                                                        $output .= $conf['lang_rirhtsfor'].' ...'.substr($site_root, -50).'/'.$file.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_other_perms.' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                                        }

                                                                }
                                }

                if (!@chdir ($file)) {
                        continue;
                        }

                _get_chmodrec($parentdir.$file.'/');
                chdir("..");
                }
        }
/*m*/

/*c*/
function compress($path, $archivename) {
        global $this_script_dir, $site_root, $totalbytes, $totalfiles, $output, $add_in_contents;
        @chdir($site_root);
        _get_dir_tree();
        /*$output .= '<br /><hr />'.$totalbytes.' bytes  in '.$totalfiles.' files';*/
        /*if (extension_loaded('bz2')) {}*/
        if (extension_loaded('zlib')) {
                $fpz = gzopen ($this_script_dir.'/'.$archivename, 'wb9');
                gzwrite ($fpz, $add_in_contents);
                gzclose ($fpz);
                } else {
                        $output = '';
                        error('no gzip on server');
                        }
        return $output;
        }

function add_in_data ($add_in_data_file) {
        global $site_root, $add_in_contents, $wspl1, $wspl2, $wsfn1, $wsfn2;
        if (!isset($add_in_contents)) {$add_in_contents = '';}

        $contents = @file_get_contents($site_root.$add_in_data_file);

        $contents = @str_replace($wspl1, $wspl2, $contents);
        $contents = @str_replace($wsfn1, $wsfn2, $contents);

        $add_in_contents .= $wspl1;
        $add_in_contents .= $add_in_data_file;
        $add_in_contents .= $wsfn1;
        $add_in_contents .= @substr(sprintf('%o', fileperms($site_root.$add_in_data_file)), -3);
        $add_in_contents .= $wsfn1;
        $add_in_contents .= $contents;
        }

function _get_dir_tree($parentdir='/') {
        global $totalbytes, $totalfiles, $output, $exludesubdirs, $onlythisext, $onlythisextmode, $onlyfilesize, $onlyfilesizemode, $exludethisdir, $exludethisdirmode;

        if (file_exists('.htaccess')) {
                $add_htaccess = 1;
                $add_htaccess2 = 1;
                if (!preg_match('/\\.('.$onlythisextmode.')$/i', '.htaccess') and $onlythisext) {
                        $add_htaccess = 0;
                        }
                if ($onlyfilesize and filesize('.htaccess') > $onlyfilesizemode) {
                        $add_htaccess2 = 0;
                        }
                if ($add_htaccess == 1 and $add_htaccess2 == 1) {
                        add_in_data ($parentdir.'.htaccess');
                        $output .= ' <span class="filedir">'.ltrim($parentdir, '/').'.htaccess</span><br />';
                        }
                }

        if (file_exists('.htpasswd')) {
                $add_htpasswd = 1;
                $add_htpasswd2 = 1;
                if (!preg_match('/\\.('.$onlythisextmode.')$/i', '.htpasswd') and $onlythisext) {
                        $add_htpasswd = 0;
                        }
                if ($onlyfilesize and filesize('.htpasswd') > $onlyfilesizemode) {
                        $add_htpasswd2 = 0;
                        }
                if ($add_htpasswd == 1 and $add_htpasswd2 == 1) {
                        add_in_data ($parentdir.'.htpasswd');
                        $output .= ' <span class="filedir">'.ltrim($parentdir, '/').'.htpasswd</span><br />';
                        }
                }

        foreach (glob('*') as $file) {
                if (substr($file, 0, 4) == 'auae') {continue;}
                /* extenshion fix. do not compress any *.auae */
                if (substr($file, -4, 4) == 'auae') {continue;}
                if (is_file($file) && is_readable($file)) {
                        if ($onlyfilesize and filesize($file) > $onlyfilesizemode) {continue;}
                        if (!preg_match('/\\.('.$onlythisextmode.')$/i', $file) and $onlythisext) {continue;}
                        /*$changed = FALSE;
                        if (!is_readable($file)) {
                                if (@chmod($file, octdec('0440'))) {
                                        $output .= '<span style="color:blue;">low</span><br />';
                                        }
                                $changed = TRUE;
                                }*/

                        /*$totalbytes += filesize($file);
                        $totalfiles += 1;*/

                        add_in_data ($parentdir.$file);
                        $output .= ' <span class="filedir">'.ltrim($parentdir, '/').$file.'</span><br />';

                        /*if ($changed) {
                                @chmod($file, '0'.$perms_all_orig);
                                }*/

                        } else if (is_dir($file)) {
                                if (!$exludesubdirs) {
                                        if (preg_match('/('.$exludethisdirmode.')$/i', $file) and $exludethisdir) {continue;}
                                        /*if (!is_readable($file)) {
                                                if (@chmod($file, octdec('0440'))) {
                                                        $output .= '<span style="color:blue;">low</span><br />';
                                                        }
                                                }*/

/*                                        if (file_exists($parentdir.$file.'/.htaccess')) {
                                                add_in_data ($parentdir.$file.'/.htaccess');
                                                $output .= ' <span class="filedir">'.ltrim($parentdir, '/').$file.'/.htaccess</span><br />';
                                                }

                                        if (file_exists($parentdir.$file.'/.htpasswd')) {
                                                add_in_data ($parentdir.$file.'/.htpasswd');
                                                $output .= ' <span class="filedir">'.ltrim($parentdir, '/').$file.'/.htpasswd</span><br />';
                                                }
*/
                                        $cwd = getcwd();
                                        $site_root = preg_replace('/\\\+/', '/', $cwd);

                                        /*$output .= '<span class="filedir">'.$site_root.'/'.$file.'</span><br />';*/

                                        if (!@chdir ($file)) {
                                                continue;
                                                }

                                        _get_dir_tree($parentdir.$file.'/');
                                        chdir("..");
                                        }
                                }
                }
        }
/*c*/

/*d*/
function decompress($path, $selarchive) {
        global $conf, $site_root, $wspl1, $wspl2, $wsfn1, $wsfn2, $fileoverwrite, $filegroup_list, $filegroup_activated, $filegroup_other_activated, $output, $filegroup_perms, $filegroup_other_perms, $new_dir_perms, $new_dir_perms_on, $uninstlog, $nolog, $neverlog;

        $filename = $selarchive;

        if (!file_exists($filename)) {
                error('packed file not found!');
        } else {

        $site_root_md = explode('/', $site_root);
        $site_root_mk = '';
        for ($site_root_md_cc = 0; $site_root_md_cc < count($site_root_md); $site_root_md_cc++) {
                $site_root_mk .= $site_root_md[$site_root_md_cc].'/';
                if (!file_exists($site_root_mk)) {
                        if ($new_dir_perms_on) {
                                if (@mkdir($site_root_mk, octdec('0'.$new_dir_perms)) or error($conf['lang_file'].' '.$site_root_mk.' '.$conf['lang_missed'])) {
                                        $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root_mk, -70).'</span><br />';
                                        if ($nolog == FALSE) {
                                                $uninstlog .= $site_root_mk."\n";
                                                }
                                        }
                                } else {
                                        if (@mkdir($site_root_mk) or error($conf['lang_file'].' '.$site_root_mk.' '.$conf['lang_missed'])) {
                                                $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root_mk, -70).'</span><br />';
                                                if ($nolog == FALSE) {
                                                        $uninstlog .= $site_root_mk."\n";
                                                        }
                                                }
                                        }
                        }
        }

        $fh = gzopen($filename, 'rb');
        $unp = '';
        while (!feof($fh)) {
                $unp .= gzread($fh, 5242880);
                }
        gzclose($fh);

        $error_in_func = false;
        if (substr($unp, 0, 5) !== chr(119).chr(105).chr(115).chr(112).chr(108)) {
                error($conf['lang_notsupportedarchive']);
                $error_in_func = true;
                }

        $splarr = explode($wspl1, $unp);

        $stored_unp_path = 'winull';

        if (!$error_in_func) {
        foreach ($splarr as $splarrk => $splarrv) {
                if (strlen($splarrv) < 1) {continue;}
                list ($fileunp, $permsunp, $contentsunp) = explode($wsfn1, $splarrv);

                $contentsunp = str_replace($wspl2, $wspl1, $contentsunp);
                $contentsunp = str_replace($wsfn2, $wsfn1, $contentsunp);
                $fileunp_dir = dirname($fileunp);
                $fileunp_fn = basename($fileunp);
                $fileunp_dir = trim($fileunp_dir, '\\/ ');
                $fileunp_dir_sl_trigger = (strlen($fileunp_dir) == 0) ? '' : '/';
                $fileunp_dir = $fileunp_dir_sl_trigger.$fileunp_dir.'/';
                $fileunp_dir_dirs_v_pluser = '';
                if ($stored_unp_path != $fileunp_dir) {
                        $fileunp_dir_trimmed = trim($fileunp_dir, '\\/ ');
                        if (substr_count($fileunp_dir_trimmed, '/') > 0) {
                                $fileunp_dir_dirs = explode('/', $fileunp_dir_trimmed);
                                foreach ($fileunp_dir_dirs as $fileunp_dir_dirs_v) {
                                        $fileunp_dir_dirs_v_pluser .= '/'.$fileunp_dir_dirs_v;
                                        if (!file_exists($site_root.$fileunp_dir_dirs_v_pluser)) {
                                                if ($new_dir_perms_on) {
                                                        if (@mkdir($site_root.$fileunp_dir_dirs_v_pluser, octdec('0'.$new_dir_perms)) or error($conf['lang_file'].' '.$site_root.$fileunp_dir_dirs_v_pluser.' '.$conf['lang_missed'])) {
                                                                $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root, -50).$fileunp_dir_dirs_v_pluser.'</span><br />';
                                                                if ($nolog == FALSE) {
                                                                        $uninstlog .= $site_root.$fileunp_dir_dirs_v_pluser."\n";
                                                                        }
                                                                }
                                                        } else {
                                                                if (@mkdir($site_root.$fileunp_dir_dirs_v_pluser) or error($conf['lang_file'].' '.$site_root.$fileunp_dir_dirs_v_pluser.' '.$conf['lang_missed'])) {
                                                                        $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root, -50).$fileunp_dir_dirs_v_pluser.'</span><br />';
                                                                        if ($nolog == FALSE) {
                                                                                $uninstlog .= $site_root.$fileunp_dir_dirs_v_pluser."\n";
                                                                                }
                                                                        }
                                                                }
                                                }
                                        }
                                } elseif (strlen($fileunp_dir_trimmed) > 0) {
                                        if (!file_exists($site_root.$fileunp_dir)) {
                                                if ($new_dir_perms_on) {
                                                        if (@mkdir($site_root.$fileunp_dir, octdec('0'.$new_dir_perms)) or error($conf['lang_file'].' '.$site_root.$fileunp_dir.' '.$conf['lang_missed'])) {
                                                                $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root, -50).$fileunp_dir.'</span><br />';
                                                                if ($nolog == FALSE) {
                                                                        $uninstlog .= $site_root.$fileunp_dir."\n";
                                                                        }
                                                                }
                                                        } else {
                                                                if (@mkdir($site_root.$fileunp_dir) or error($conf['lang_file'].' '.$site_root.$fileunp_dir.' '.$conf['lang_missed'])) {
                                                                        $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root, -50).$fileunp_dir.'</span><br />';
                                                                        if ($nolog == FALSE) {
                                                                                $uninstlog .= $site_root.$fileunp_dir."\n";
                                                                                }
                                                                        }
                                                                }
                                                }
                                        } else {
                                                if (!file_exists($site_root.$fileunp_dir)) {
                                                        if ($new_dir_perms_on) {
                                                                if (@mkdir($site_root.$fileunp_dir, octdec('0'.$new_dir_perms)) or error($conf['lang_file'].' '.$site_root.$fileunp_dir.' '.$conf['lang_missed'])) {
                                                                        $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root, -50).$fileunp_dir.'</span><br />';
                                                                        if ($nolog == FALSE) {
                                                                                $uninstlog .= $site_root.$fileunp_dir."\n";
                                                                                }
                                                                        }
                                                                } else {
                                                                        if (@mkdir($site_root.$fileunp_dir) or error($conf['lang_file'].' '.$site_root.$fileunp_dir.' '.$conf['lang_missed'])) {
                                                                                $output .= '<span class="filedir">'.$conf['lang_makedir'].': ...'.substr($site_root, -50).$fileunp_dir.'</span><br />';
                                                                                if ($nolog == FALSE) {
                                                                                        $uninstlog .= $site_root.$fileunp_dir."\n";
                                                                                        }

                                                                                }
                                                                        }
                                                        }
                                                }
                                                $stored_unp_path = $fileunp_dir;
                        }

                if (file_exists($site_root.$fileunp_dir.$fileunp_fn)) {
                        if ($fileoverwrite) {
                                @$fp = fopen ($site_root.$fileunp_dir.$fileunp_fn, "wb") or error($conf['lang_file'].' '.$fileunp_fn.' '.$conf['lang_missed']);
                                if (@fwrite ($fp, $contentsunp)) {
                                        $output .= '<span class="filefile">'.$conf['lang_overwrite'].': '.$fileunp_fn.'<span><br />';
                                        if ($nolog == FALSE) {
                                                $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                                }
                                        }
                                @fclose ($fp);
                                } else {
                                        $output .= '<span class="filefile">'.$fileunp_fn.' '.$conf['lang_exists'].'. '.$conf['lang_missed'].'.<span><br />';
                                        if ($nolog == FALSE) {
                                                $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                                }
                                        }
                        } else {
                                @$fp = fopen ($site_root.$fileunp_dir.$fileunp_fn, "wb") or error($conf['lang_file'].' '.$fileunp_fn.' '.$conf['lang_missed']);
                                if (@fwrite ($fp, $contentsunp)) {
                                        $output .= '<span class="filefile">'.$conf['lang_unpack'].': '.$fileunp_fn.'<span><br />';
                                        if ($nolog == FALSE) {
                                                $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                                }
                                        }
                                @fclose ($fp);
                                }

                if (preg_match('/\\.('.$filegroup_list[1].')$/i', $fileunp_fn) and $filegroup_activated[1]) {
                        if (@chmod($site_root.$fileunp_dir.$fileunp_fn, octdec('0'.$filegroup_perms[1]))) {
                                $output .= $conf['lang_rirhtsfor'].' '.$fileunp_fn.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[1].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                if ($nolog == FALSE) {
                                        $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                        }
                                }
                        } else if (preg_match('/\\.('.$filegroup_list[2].')$/i', $fileunp_fn) and $filegroup_activated[2]) {
                                if (@chmod($site_root.$fileunp_dir.$fileunp_fn, octdec('0'.$filegroup_perms[2]))) {
                                        $output .= $conf['lang_rirhtsfor'].' '.$fileunp_fn.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[2].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                        if ($nolog == FALSE) {
                                                $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                                }
                                        }
                                } else if (preg_match('/\\.('.$filegroup_list[3].')$/i', $fileunp_fn) and $filegroup_activated[3]) {
                                        if (@chmod($site_root.$fileunp_dir.$fileunp_fn, octdec('0'.$filegroup_perms[3]))) {
                                                $output .= $conf['lang_rirhtsfor'].' '.$fileunp_fn.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_perms[3].' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                if ($nolog == FALSE) {
                                                        $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                                        }
                                                }
                                        } else if ($filegroup_other_activated) {
                                                if (@chmod($site_root.$fileunp_dir.$fileunp_fn, octdec('0'.$filegroup_other_perms))) {
                                                        $output .= $conf['lang_rirhtsfor'].' '.$fileunp_fn.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$filegroup_other_perms.' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                        if ($nolog == FALSE) {
                                                                $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                                                }
                                                        }
                                                } else {
                                                        if (@chmod($site_root.$fileunp_dir.$fileunp_fn, octdec('0'.$permsunp))) {
                                                                $output .= $conf['lang_rirhtsfor'].' '.$fileunp_fn.' '.$conf['lang_rirhtsfor_end_sp'].' '.$conf['lang_rightsseted'].' '.$permsunp.' '.$conf['lang_rightsseted_end_sp'].'<br />';
                                                                if ($nolog == FALSE) {
                                                                        $uninstlog .= $site_root.$fileunp_dir.$fileunp_fn."\n";
                                                                        }
                                                                }
                                                        }
                }
                }

        if ($neverlog == 0 and $nolog == FALSE and !file_exists($selarchive.'.aulg')) {
                $fplog = fopen ($selarchive.'.aulg', 'w');
                fwrite ($fplog, $uninstlog);
                fclose ($fplog);
                }
        }
        return $output;
        }
/*d*/

/*u*/
function uninstall($sellog) {
        global $output;
        if (file_exists($sellog)) {
                $loglist = file($sellog);
                $loglist = array_reverse($loglist);
                foreach ($loglist as $loglist_line) {
                        $loglist_line = trim($loglist_line);
                        if (file_exists($loglist_line)) {
                                if (is_file($loglist_line)) {
                                        @chmod($loglist_line, octdec('0660'));
                                        if (@unlink($loglist_line)) {
                                                $output .= '...'.substr($loglist_line, -100).'<br />';
                                                }
                                        } else if (is_dir($loglist_line)) {
                                                @chmod($loglist_line, octdec('0770'));
                                                if (@rmdir("$loglist_line")) {
                                                        $output .= '...'.substr($loglist_line, -100).'<br />';
                                                        }
                                                }
                                }
                        }
                @chmod($sellog, octdec('0660'));
                if (@unlink($sellog)) {
                        $output .= $sellog.'<br />';
                        }
                } else {
                        error('no uninstall log');
                        }

        return $output;
        }
/*u*/
?>
