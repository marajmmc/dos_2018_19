<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=7;

$config['system_status_yes']='Yes';
$config['system_status_no']='No';
$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';
$config['system_status_closed']='Closed';
$config['system_status_pending']='Pending';
$config['system_status_forwarded']='Forwarded';
$config['system_status_complete']='Complete';
$config['system_status_approved']='Approved';
$config['system_status_delivered']='Delivered';
$config['system_status_received']='Received';
$config['system_status_rejected']='Rejected';
$config['system_status_paid']='Paid';


$config['system_status_not_done']='Not Done';
$config['system_status_done']='Done';

/* this config for SMS Transfer Rollback */
$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;

