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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
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
                <label for="crop_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$item['crop_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="<?php if(!($item['crop_type_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label for="crop_type_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_type_id" name="item[crop_type_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($crop_types as $crop_type)
                    {?>
                        <option value="<?php echo $crop_type['value']?>" <?php if($crop_type['value']==$item['crop_type_id']){ echo "selected";}?>><?php echo $crop_type['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_WHOSE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <div class="radio-inline">
                    <label><input type="radio" value="ARM" <?php if($item['whose']=='ARM'){echo 'checked';} ?> name="item[whose]">ARM</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" value="Competitor" <?php if($item['whose']=='Competitor'){echo 'checked';} ?> name="item[whose]">Competitor</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" value="Upcoming" <?php if($item['whose']=='Upcoming'){echo 'checked';} ?> name="item[whose]">Upcoming</label>
                </div>
            </div>
        </div>
        <div style="<?php if($item['whose']!='Competitor'){echo 'display:none';} ?>" class="row show-grid" id="competitor_id_container">
            <div class="col-xs-4">
                <label for="competitor_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPETITOR_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="competitor_id" name="item[competitor_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($competitors as $competitor)
                    {?>
                        <option value="<?php echo $competitor['value']?>" <?php if($competitor['value']==$item['competitor_id']){ echo "selected";}?>><?php echo $competitor['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="name" class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[name]" id="name" class="form-control" value="<?php echo $item['name'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="stock_id" class="control-label pull-right"><?php echo $this->lang->line('LABEL_STOCK_ID');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[stock_id]" id="stock_id" class="form-control" value="<?php echo $item['stock_id'];?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="hybrid" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_HYBRID');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="hybrid" name="item[hybrid]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($hybrids as $hybrid)
                    {?>
                        <option value="<?php echo $hybrid['value']?>" <?php if($hybrid['value']==$item['hybrid']){ echo "selected";}?>><?php echo $hybrid['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="description" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DESCRIPTION');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[description]" id="description" class="form-control"><?php echo $item['description'] ?></textarea>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="date_release" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_RELEASE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_release]" id="date_release" class="form-control" value="<?php echo $item['date_release']; ?>">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="trial_completed" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TRIAL_COMPLETED');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[trial_completed]" id="trial_completed" class="form-control" value="<?php echo $item['trial_completed']; ?>">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control"><?php echo $item['remarks'] ?></textarea>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="ordering" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[ordering]" id="ordering" class="form-control" value="<?php echo $item['ordering'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="status" class="control-label pull-right"><?php echo $CI->lang->line('STATUS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="item[status]" class="form-control">
                    <!--<option value=""></option>-->
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>"
                        <?php
                        if ($item['status'] == $CI->config->item('system_status_active')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->lang->line('ACTIVE') ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"
                        <?php
                        if ($item['status'] == $CI->config->item('system_status_inactive')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->lang->line('INACTIVE') ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off('change','#crop_id');
        $(document).off('change','#type_id');
        $(document).off('change','input[name="item[whose]"]:radio');
        $(document).off('change','#hybrid');

        $("#date_release").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "-100:+0"});

        $(document).on("change","#crop_id",function()
        {
            $("#crop_type_id").val("");
            var crop_id=$('#crop_id').val();
            if(crop_id>0)
            {
                $('#crop_type_id_container').show();
                $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
            }
            else
            {
                $('#crop_type_id_container').hide();
            }
        });
        $(document).on("change",'input[name="item[whose]"]:radio',function()
        {
            var whose=$(this).val();
            if(whose=='Competitor')
            {
                $("#competitor_id_container").show();
            }
            else
            {
                $("#competitor_id_container").hide();
            }
        });
    });
</script>
