<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Maraj
 * Date: 5/12/18
 * Time: 9:19 AM
 */
class Test extends CI_Controller
{
    function index()
    {
        $str="Current Status : LC Completed \n New Status: Received";
        echo nl2br($str);
        echo $this;
    }
}