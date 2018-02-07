<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/acres/'.$info['id'])
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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_amount_acres');?>" method="post">
    <input type="hidden" id="type_id" name="type_id" value="<?php echo $info['id']; ?>" />
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $info['crop_name'];;?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $info['name'];;?></label>
<!--                <label class="control-label">--><?php //echo $info['id'];;?><!--</label>-->
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
                        <option value="<?php echo $division['value']?>" <?php if($division['value']==$item['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
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
                        <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$item['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
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
                        <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$item['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
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
                <select id="district_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($districts as $district)
                    {?>
                        <option value="<?php echo $district['value']?>" <?php if($district['value']==$item['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!(sizeof($upazillas)>0)){echo 'display:none';} ?>" class="row show-grid" id="upazilla_id_container">
            <div class="col-xs-4">
                <label for="upazilla_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="upazilla_id" name="item[upazilla_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($upazillas as $upazilla)
                    {?>
                        <option value="<?php echo $upazilla['value']?>" <?php if($upazilla['value']==$item['upazilla_id']){ echo "selected";}?>><?php echo $upazilla['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="price" class="control-label pull-right">Acres<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[quantity_acres]" id="price" class="form-control float_type_positive" value="<?php echo $item['quantity_acres'];?>"/>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(document).off('change','#pack_size_id');
        $(document).off('change','#division_id');
        $(document).off('change','#zone_id');
        $(document).off('change','#territory_id');
        $(document).off('change','#district_id');
        $(document).off('change','#upazilla_id');
        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#upazilla_id").val("");
            var division_id=$('#division_id').val();
            if(division_id>0)
            {
                $('#zone_id_container').show();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
            }
            else
            {
                $('#zone_id_container').hide();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
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
            $("#upazilla_id").val("");
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                $('#district_id_container').show();
                $('#upazilla_id_container').hide();
                $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
            }
            else
            {
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
            }
        });
        $(document).on("change","#district_id",function()
        {
            $("#upazilla_id").val("");
            var district_id=$("#district_id").val();
            if(district_id>0)
            {
                $('#upazilla_id_container').show();
                $.ajax({
                    url: '<?php echo site_url('common_controller/get_dropdown_upazillas_by_districtid'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{
                        district_id:district_id,
                        html_container_id:'#upazilla_id'
                        //select_label:'All'
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#upazilla_id_container').hide();
            }
        });
    });
</script>
