<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
    'id'=>'button_action_save_new',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
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
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_TYPE');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select name="customer_info[type]" class="form-control">
            <option value=""><?php echo $this->lang->line('SELECT');?></option>
            <?php
            foreach($customer_types as $types)
            {?>
                <option value="<?php echo $types['value']?>" <?php if($types['value']==$customer_info['type']){ echo "selected";}?>><?php echo $types['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INCHARGE');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select name="customer_info[incharge]" class="form-control">
            <option value=""><?php echo $this->lang->line('SELECT');?></option>
            <?php
            foreach($incharge as $incharge)
            {?>
                <option value="<?php echo $incharge['value']?>" <?php if($incharge['value']==$customer_info['incharge']){ echo "selected";}?>><?php echo $incharge['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="division_id" class="form-control">
            <option value=""><?php echo $this->lang->line('SELECT');?></option>
            <?php
            foreach($divisions as $division)
            {
            ?>
                <option value="<?php echo $division['value']?>" <?php if($division['value']==$customer_info['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>

<div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="zone_id" class="form-control">
            <option value=""><?php echo $this->lang->line('SELECT');?></option>
            <?php
            foreach($zones as $zone)
            {?>
                <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$customer_info['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="territory_id" class="form-control">
            <option value=""><?php echo $this->lang->line('SELECT');?></option>
            <?php
            foreach($territories as $territory)
            {?>
                <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$customer_info['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="district_id" name="customer_info[district_id]" class="form-control">
            <option value=""><?php echo $this->lang->line('SELECT');?></option>
            <?php
            foreach($districts as $district)
            {?>
                <option value="<?php echo $district['value']?>" <?php if($district['value']==$customer_info['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[name]" id="name" class="form-control" value="<?php echo $customer_info['name'];?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_SHORT_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[name_short]" class="form-control" value="<?php echo $customer_info['name_short'];?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_CODE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[customer_code]" id="customer_code" class="form-control" value="<?php echo $customer_info['customer_code'];?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_CREDIT_LIMIT');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[credit_limit]" id="credit_limit" class="form-control" value="<?php echo $customer_info['credit_limit'];?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME_OWNER');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[name_owner]" id="name_owner" class="form-control" value="<?php echo $customer_info['name_owner'];?>"/>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TIN');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[tin]" id="tin" class="form-control" value="<?php echo $customer_info['tin'] ?>" >
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NID');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[nid]" id="nid" class="form-control" value="<?php echo $customer_info['nid'] ?>" >
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PICTURE');?></label>
    </div>
    <div class="col-sm-2">
        <input type="file" class="browse_button" data-preview-container="#image_profile" name="image_profile">
    </div>
    <div class="col-xs-4" id="image_profile">
        <?php
        //new customer create time
        if($customer_info['image_location'])
        {
            ?>
            <input type="hidden" name="customer_info[image_name]" value="<?php echo $customer_info['image_name']; ?>">
            <input type="hidden" name="customer_info[image_location]" value="<?php echo $customer_info['image_location']; ?>">
            <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_customer_profile_picture').$customer_info['image_location']; ?>">
            <?php
        }
        ?>
    </div>
    <div class="col-sm-2"></div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME_MARKET');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[name_market]" id="name_market" class="form-control" value="<?php echo $customer_info['name_market'];?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ADDRESS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <textarea class="form-control" name="customer_info[address]"><?php echo $customer_info['address'];?></textarea>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">
            <?php echo $this->lang->line('LABEL_MAP_ADDRESS').'<br>';?>
            <p style="color: #942724;cursor:pointer" id="map">(Google Maps)</p>
        </label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <textarea class="form-control" name="customer_info[map_address]"><?php echo $customer_info['map_address'];?></textarea>
    </div>
</div>



<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PHONE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[phone]" id="phone" class="form-control" value="<?php echo $customer_info['phone'];?>"/>
    </div>
</div>



<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_EMAIL');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[email]" id="email" class="form-control" value="<?php echo $customer_info['email'];?>"/>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AGREEMENT');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="status" name="customer_info[status_agreement]" class="form-control">
            <option value="<?php echo $CI->config->item('system_status_active'); ?>"
                <?php
                if ($customer_info['status_agreement'] == $CI->config->item('system_status_done')) {
                    echo "selected='selected'";
                }
                ?> ><?php echo $CI->config->item('system_status_done'); ?>
            </option>
            <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"
                <?php
                if ($customer_info['status_agreement'] == $CI->config->item('system_status_not_done')) {
                    echo "selected='selected'";
                }
                ?> ><?php echo $CI->config->item('system_status_not_done'); ?></option>
        </select>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label for="opening_date" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[opening_date]" id="date_open" class="form-control" value="<?php echo System_helper::display_date($customer_info['opening_date'])?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label for="closing_date" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CLOSING');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[closing_date]" id="date_close" class="form-control" value="<?php echo System_helper::display_date($customer_info['closing_date'])?>"/>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="customer_info[ordering]" id="ordering" class="form-control" value="<?php echo $customer_info['ordering'] ?>" >
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('STATUS');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="status" name="customer[status]" class="form-control">
            <option value="<?php echo $CI->config->item('system_status_active'); ?>"
                <?php
                if ($customer['status'] == $CI->config->item('system_status_active')) {
                    echo "selected='selected'";
                }
                ?> ><?php echo $CI->lang->line('ACTIVE') ?>
            </option>
            <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"
                <?php
                if ($customer['status'] == $CI->config->item('system_status_inactive')) {
                    echo "selected='selected'";
                }
                ?> ><?php echo $CI->lang->line('INACTIVE') ?>
            </option>
        </select>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <textarea name="customer_info[remarks]" class="form-control"><?php echo $customer_info['remarks']; ?></textarea>
    </div>
</div>

</div>

<div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off('change','#division_id');
        $(document).off('change','#zone_id');
        $(document).off('change','#territory_id');
        $(document).off('click','#map');

        $(":file").filestyle({input: false,buttonText: "<?php echo $CI->lang->line('UPLOAD');?>", buttonName: "btn-primary"});
        $("#date_open").datepicker({
            dateFormat : display_date_format,
            changeMonth: true,
            changeYear: true,
            yearRange: "-5:+5",
            onClose: function( selectedDate ) {
            $( "#date_close" ).datepicker( "option", "minDate", selectedDate );}
        });

        $("#date_close").datepicker({
            dateFormat : display_date_format,
            changeMonth: true,
            changeYear: true,
            yearRange: "-5:+5",
            onClose: function( selectedDate ) {
            $( "#date_open" ).datepicker( "option", "maxDate", selectedDate );}
        });

        $(document).on('click','#map',function()
        {
            var win = window.open('https://maps.google.com', '_blank');
            if (win) {
                //Browser has allowed it to be opened
                win.focus();
            } else {
                //Browser has blocked it
                alert('Please allow popups for this website');
            }
        });

        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            var division_id=$('#division_id').val();
            if(division_id>0)
            {
                $('#zone_id_container').show();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
            }
            else
            {
                $('#zone_id_container').hide();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
            }
        });
        $(document).on("change","#zone_id",function()
        {
            $("#territory_id").val("");
            $("#district_id").val("");
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                $('#territory_id_container').show();
                $('#district_id_container').hide();
                $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
            }
            else
            {
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
            }
        });
        $(document).on("change","#territory_id",function()
        {
            $("#district_id").val("");
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                $('#district_id_container').show();
                $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
            }
            else
            {
                $('#district_id_container').hide();
            }
        });
    });
</script>
