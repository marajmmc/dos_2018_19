<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_EDIT"),
    'href'=>site_url($CI->controller_url).'/index/edit/'.$customer['id']
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $customer['id']; ?>" />
<input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
<div class="row widget">
<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_TYPE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['type_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INCHARGE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['incharge_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['division_name'];?></label>
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['zone_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['territory_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['district_name'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_SHORT_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name_short']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_CODE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['customer_code']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_CREDIT_LIMIT');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['credit_limit']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME_OWNER');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name_owner']; ?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TIN');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['tin']; ?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NID');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['nid'] ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PROFILE_PICTURE');?></label>
    </div>
    <div class="col-xs-1"></div>
    <div class="col-xs-4" id="image_profile">
        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_customer_profile_picture').$customer_info['image_location']; ?>">
    </div>
    <div class="col-xs-3"></div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME_MARKET');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name_market'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ADDRESS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['address']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $customer_info['type_name'];?> Location</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php if($customer_info['map_address']) {echo $customer_info['map_address'];}?>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PHONE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['phone'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_EMAIL');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['email'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AGREEMENT');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['status_agreement']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label for="opening_date" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php if($customer_info['opening_date'])  echo System_helper::display_date($customer_info['opening_date']); ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label for="closing_date" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CLOSING');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php if($customer_info['closing_date']) {echo System_helper::display_date($customer_info['closing_date']);}?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['ordering']?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('STATUS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer['status']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['remarks']; ?></label>
    </div>
</div>
<?php if($assign_upazillas){?>
<div style="overflow-x: auto;" class="row show-grid">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="max-width: 300px;">Assigned Upazilla</th>
        </tr>
        <tr>
            <th style="max-width: 100px;">Serial No.</th>
            <th style="max-width: 200px;">Upazilla Name</th>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach($assign_upazillas as $index=>$upazillas)
            {
        ?>
            <tr>
                <td style="max-width: 100px;">
                    <h5><?php echo $index+1;?></h5>
                </td>
                <td style="max-width: 200px;">
                    <h5><?php echo $upazillas;?></h5>
                </td>
            </tr>
            <?php
            }
            ?>

        </tbody>
    </table>
</div>
<?php }?>

<div style="overflow-x: auto;" class="row show-grid">
<table class="table table-bordered">
    <thead>
    <tr>
        <th style="min-width: 150px;">Outlet Documents</th>
    </tr>
    <tr>
        <th style="min-width: 70px;">Serial No.</th>
        <th style="min-width: 250px;">Files</th>
        <th style="max-width: 150px;">Remarks</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($file_details as $index=>$file)
    {
        $type=substr($file['file_type'],0,5);
        $is_image=false;
        if($type=='image')
        {
            $is_image=true;
        }
        ?>
        <tr>
            <td style="max-width: 70px;">
                <h4><?php echo $index+1;?></h4>
            </td>
            <td>
                <div class="preview_container_file" id="preview_container_file_<?php echo $index+1;?>">
                    <?php
                    if($is_image)
                    {
                        ?>
                        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_customer_document').$file['file_location']; ?>">
                    <?php
                    }
                    else
                    {
                        ?><a class="external" href="<?php echo $CI->config->item('system_base_url_customer_document').$file['file_location'];?>" target="_tab"><?php echo $file['file_name'];?></a><?php
                    }
                    ?>
                </div>
            </td>
            <td style="max-width: 100px;">
                <label class="control-label"><?php if(isset($file['file_remarks'])){echo $file['file_remarks'];} ?></label>
            </td>
        </tr>
    <?php
    }
    ?>

    </tbody>
</table>
</div>

</div>

<div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>