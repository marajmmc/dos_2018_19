<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = & get_instance();

$system_crops=Query_helper::get_info($CI->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
$results=Query_helper::get_info($CI->config->item('table_login_setup_classification_crop_types'),array('id value','name text','crop_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
$system_types=array();
foreach($results as $result)
{
    $system_types[$result['crop_id']][]=$result;
}
$system_divisions=Query_helper::get_info($CI->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$CI->config->item('system_status_active').'"'));
$results=Query_helper::get_info($CI->config->item('table_login_setup_location_zones'),array('id value','name text','division_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
$system_zones=array();
foreach($results as $result)
{
    $system_zones[$result['division_id']][]=$result;
}
$results=Query_helper::get_info($CI->config->item('table_login_setup_location_territories'),array('id value','name text','zone_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
$system_territories=array();
foreach($results as $result)
{
    $system_territories[$result['zone_id']][]=$result;
}
$results=Query_helper::get_info($CI->config->item('table_login_setup_location_districts'),array('id value','name text','territory_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
$system_districts=array();
foreach($results as $result)
{
    $system_districts[$result['territory_id']][]=$result;
}

$this->db->from($this->config->item('table_login_csetup_customer').' lsc');
$this->db->join($this->config->item('table_login_csetup_cus_info').' lsci','lsci.customer_id = lsc.id','LEFT');
$this->db->select('lsc.id');
$this->db->select('lsci.type, lsci.district_id, lsci.customer_id value, lsci.name text');
$this->db->where('lsc.status',$CI->config->item('system_status_active'));
$items=$this->db->get()->result_array();
$system_customers=array();
$system_outlets=array();
$system_all_customers=array();
foreach($items as $result)
{
    if($result['type']==$CI->config->item('system_customer_type_customer_id'))
    {
        $system_customers[$result['district_id']][]=$result;
    }
    elseif($result['type']==$CI->config->item('system_customer_type_outlet_id'))
    {
        $system_outlets[$result['district_id']][]=$result;
    }
    else
    {

    }
    $system_all_customers[]=$result;
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login V 2.0.1.8.19.1</title>
        <link rel="icon" type="image/ico" href="http://malikseeds.com/favicon.ico"/>
        <meta charset="utf-8">
        <link rel="stylesheet" href="<?php echo base_url('css/bootstrap.min.css');?>">
        <link rel="stylesheet" href="<?php echo base_url('css/style.css');?>">
        <link rel="stylesheet" href="<?php echo base_url('css/jquery-ui/jquery-ui.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/jquery-ui/jquery-ui.theme.css'); ?>">

        <link rel="stylesheet" href="<?php echo base_url('css/jqx/jqx.base.css'); ?>">

    </head>
    <body>
        <script src="<?php echo base_url('js/jquery-2.1.1.js'); ?>"></script>
        <script src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/bootstrap-filestyle.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery-ui.min.js'); ?>"></script>

        <!--    for jqx grid finish-->
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxcore.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxscrollbar.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.edit.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.sort.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.pager.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxbuttons.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxcheckbox.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxlistbox.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxdropdownlist.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxmenu.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.filter.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.selection.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.columnsresize.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxdata.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxdatatable.js'); ?>"></script>
        <!--    only for color picker-->
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxcolorpicker.js'); ?>"></script>
        <!--    For column reorder-->
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.columnsreorder.js'); ?>"></script>
        <!--    For print-->
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxdata.export.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.export.js'); ?>"></script>
        <!--        for footer sum-->
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxgrid.aggregates.js'); ?>"></script>
        <!-- for header tool tip-->
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxtooltip.js'); ?>"></script>
        <!-- popup-->
        <script type="text/javascript" src="<?php echo base_url('js/jqx/jqxwindow.js'); ?>"></script>

        <!--    for jqx grid end-->

        <?php
        /*$system_divisions=array();
        $system_zones=array();
        $system_territories=array();
        $system_districts=array();*/
        ?>
        <script type="text/javascript">
            var base_url = "<?php echo base_url(); ?>";
            var display_date_format = "dd-M-yy";
            var SELECT_ONE_ITEM = "<?php echo $CI->lang->line('SELECT_ONE_ITEM'); ?>";
            var DELETE_CONFIRM = "<?php echo $CI->lang->line('DELETE_CONFIRM'); ?>";
            var resized_image_files=[];
            var system_crops=JSON.parse('<?php echo json_encode($system_crops);?>');
            var system_types=JSON.parse('<?php echo json_encode($system_types);?>');
            var system_divisions=JSON.parse('<?php echo json_encode($system_divisions);?>');
            var system_zones=JSON.parse('<?php echo json_encode($system_zones);?>');
            var system_territories=JSON.parse('<?php echo json_encode($system_territories);?>');
            var system_districts=JSON.parse('<?php echo json_encode($system_districts);?>');
        </script>
        <header class="hidden-print">

            <img alt="Logo" height="40" class="site_logo pull-left" src="<?php echo base_url('images/logo.png'); ?>">
            <div class="site_title pull-left">A.R. Malik Seeds (Pvt.) Ltd.</div>

        </header>

        <div class="container-fluid" style="margin-bottom: 40px;">
            <div id="system_menus">
                <?php
                $CI->load->view('menu');
                ?>
            </div>

            <div class="row dashboard-wrapper">
                <div class="col-sm-12" id="system_content">

                </div>
            </div>
        </div>
        <footer class="hidden-print navbar-fixed-bottom">
            <div>
                All Rights Reserved & &copy; Copyright 2017 by A.R. Malik Seeds (Pvt.) Ltd. Design & Developed by :
                <a href="http://disb.solutions/" target="_blank" class="external">
                    <img src="<?php echo base_url()?>images/logo_disb.png" alt="DISB Logo" style="width: 50px"/>
                </a>
            </div>
            <div class="clearfix"></div>
        </footer>
        <div id="system_loading"><img src="<?php echo base_url('images/spinner.gif'); ?>"></div>
        <div id="system_message"></div>
        <div id="popup_window">
            <div id="popup_window_title">Details</div>
            <div id="popup_content" style="overflow: auto;">
            </div>
        </div>
        <script type="text/javascript" src="<?php echo base_url('js/system_common.js?version='.time()); ?>"></script>
    </body>
</html>
