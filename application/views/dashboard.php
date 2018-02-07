<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$user=User_helper::get_user();
$CI = & get_instance();
$sites=User_helper::get_accessed_sites();

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
    <?php
    if($CI->is_site_offline())
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_SITE_OFFLINE');?></h3>
        </div>
    <?php
    }
    ?>
    <div class="col-sm-12 ">
        <!--<h1><?php /*echo $user->name;*/?></h1>
        <img style="max-width: 250px;" src="<?php /*echo $CI->config->item('system_base_url_profile_picture').$user->image_location; */?>" alt="<?php /*echo $user->name; */?>">-->

        <div class="jumbotron">
            <div class="row">
                <div class="col-md-4 col-xs-12 col-sm-6 col-lg-4">
                    <img class="img img-responsive img-thumbnail" src="<?php echo $CI->config->item('system_base_url_profile_picture').$user->image_location; ?>" alt="<?php echo $user->name; ?>" class="img">
                </div>
                <div class="col-md-8 col-xs-12 col-sm-6 col-lg-8">
                    <div class="container" style="border-bottom:1px solid black">
                        <h2><?php echo $user->name;?></h2>
                    </div>
                    <hr>
                    <ul class="container details">
                        <li><p><span class="glyphicon glyphicon-earphone one" style="width:50px;"></span><?php echo !empty($user->mobile_no)?$user->mobile_no:'Mobile number not set'?></p></li>
                        <li><p><span class="glyphicon glyphicon-envelope one" style="width:50px;"></span><?php echo !empty($user->email)?$user->email:'Email not set'?></p></li>
                        <li><p><span class="glyphicon glyphicon-user one" style="width:50px;"></span><?php echo !empty($user->designation)?$user->designation:'Designation not set'?></p></li>
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
                                <a href="<?php echo base_url()?>profile_picture/index/edit/<?php echo $user->id;?>" class="btn btn-warning">
                                    <span class="glyphicon glyphicon-edit one"></span>
                                    Change Profile Picture
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
    <?php
        if(sizeof($sites)>0)
        {
            ?>
            <div class="widget-header">
                <div class="text-center text-danger underline">
                    <strong>You will be login with your accessible below application:</strong>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
            foreach($sites as $site)
            {
                ?>
                <a class="external" target="_blank" href="<?php echo site_url('other_sites_visit/visit_site/'.$site['id']); ?>">
                    <div class="col-md-3">
                        <div class="content-box text-center">
                            <span class="content-box-icon bg-vaiolet">
                                <img src="<?php echo base_url()?>images/logo.png" alt="" style="width: 100%"/>
                            </span>
                            <h4><?php echo strtoupper($site['short_name']);?></h4>
                        </div>
                    </div>
                </a>
                <?php
            }
        }
    ?>

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