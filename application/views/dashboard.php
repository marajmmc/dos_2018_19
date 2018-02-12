<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$user=User_helper::get_user();
$CI = & get_instance();

?>
<div class="row widget">
    <?php
    if($user->user_group==0)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_NOT_ASSIGNED_GROUP');?></h3>

        </div>
    <?php
    }
    ?>
    <div class="col-sm-12 ">
        <!--<h1><?php /*echo $user->name;*/?></h1>
        <img style="max-width: 250px;" src="<?php /*echo $CI->config->item('system_base_url_profile_picture').$user->image_location; */?>" alt="<?php /*echo $user->name; */?>">-->

        <div class="jumbotron">
            <div class="row">
                <div class="col-md-8 col-xs-12 col-sm-6 col-lg-8">
                    <div class="container" style="border-bottom:1px solid black">
                        <h2><?php echo $user->name;?></h2>
                    </div>
                    <hr>
                    <ul class="container details">
                        <li>
                            <p class="btn-group btn-group-lg">
                                <a href="<?php echo base_url()?>profile_info/index/details/<?php echo $user->id;?>" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-edit one"></span>
                                    Profile View
                                </a>
                                <a href="<?php echo base_url()?>profile_password/" class="btn btn-danger">
                                    <span class="glyphicon glyphicon-edit one"></span>
                                    Change Password
                                </a>
                            </p>
                        </li>
                    </ul>
                    <style>
                        .details li
                        {
                            list-style: none;
                        }
                    </style>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<style>
    .content-box
    {
        position: relative;
        box-shadow: 0px 2px 20px 5px #222;
        margin-bottom: 20px;
        padding: 10px;
        margin-top:30px;
        border-radius: 6px;
    }
    .content-box:hover .content-box-icon{
        box-shadow: 1px 4px 10px -6px;
    }
    .content-box:hover ,.content-box:hover a ,.content-box:active a ,.content-box:focus a
    {
        text-decoration: none;
        box-shadow: 0px 2px 20px 5px grey;
    }
    .content-box a h4 {
        color: #222;
        font-weight: 600;
        font-family: serif;
        font-size: 17px;
    }
    .content-box-icon {
        width: 50px;
        height: 50px;
        display: block;
        text-align: center;
        line-height: 50px;
        font-size: 25px;
        border-radius: 100%;
        margin: auto;
        color: #fff;
    }
</style>