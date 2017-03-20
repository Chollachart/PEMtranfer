<?php

/**
 * Created by IntelliJ IDEA.
 * User: Chanon.S
 * Date: 3/3/2559
 * Time: 23:21
 */
class SSO
{
    private $ENV;
    private $AID;

    private $actual_link = null;
    private $ssoHOST = "192.168.0.195";
    private $ssoPORT = "1903";

    public function __construct($aid, $env = 'WEB_PAGE')
    {
        $this->ENV = $env;
        $this->AID = $aid;
        $this->_init();
    }

    private function _init()
    {
        session_start();
        $this->actual_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function getAuthentication()
    {
        $personDetail = NULL;
        if (!isset($_SESSION['ssoToken']) || empty($_SESSION['ssoToken'])) {
            if ($this->ENV == "WEB_PAGE") {
                if (isset($_POST['ssoToken'])) {
                    $_SESSION['ssoToken'] = $_POST['ssoToken'];
                    echo "<script> parent.location.reload(true); </script>";
                    exit();
                } else {
                    $this->renderSignPage();
                }
            } else {
                http_response_code(401);
                exit();
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->ssoHOST . ':' . $this->ssoPORT . '/sso/token/validator/' . $this->AID);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "ssoToken=" . $_SESSION['ssoToken']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if (!curl_errno($ch)) {
            $resCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($this->ENV == 'WEB_PAGE') {
                switch ($resCode) {
                    case 200:
                        break;
                    case 400:
                        http_response_code($resCode);
                        echo "<h1 style='margin:50px;'>400 Bad Request</h1>";
                        exit;
                    case 401:
                        unset($_SESSION['ssoToken']);
                        header("refresh:1");
                        exit;
                    case 403:
                        http_response_code($resCode);
                        echo $response;
                        exit;
                    default:
                        http_response_code(404);
                        echo "<h1 style='margin:50px;'>404 Not Found</h1>";
                        exit;
                }
            } else {
                if ($resCode >= 400) {
                    http_response_code($resCode);
                    exit;
                } else {
                }
            }
        } else {
            $errCode = curl_errno($ch);
            http_response_code(500);
            echo "<div style='margin: 100px;font-size: 1.1em;'><kbd>[ " . $errCode . " ] Authentication server message:</kbd><hr><kbd style='font-size: 2.2em;'>" . curl_strerror($errCode) . "</kbd></div>";
            curl_close($ch);
            exit;
        }
        curl_close($ch);
        $res = json_decode($response, true);
        header('Content-Type: text/html; charset=utf-8');
        return array('personDetail' => json_decode($res['personDetail'], true), 'panelLogout' => $res['panelLogout']);
    }

    private function renderSignPage()
    {
        echo '<iframe src="http://' . $this->ssoHOST . ':' . $this->ssoPORT . '/sso/token/generate/' . $this->AID . '?url=' . urlencode($this->actual_link) . '" style="width: 100%; height: 100%; position: fixed; z-index: 999999; left: 0; top: 0; border: none; background:#fff"></iframe>';
        exit;
    }
}

?>
