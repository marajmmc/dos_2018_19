<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/packing_setup/'.$info['id'])
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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_pack_item');?>" method="post">
    <input type="hidden" id="variety_id" name="item[variety_id]" value="<?php echo $info['id']; ?>" />
    <input type="hidden" id="id" name="id" value="<?php echo $item['pack_size_id']; ?>" />
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
                <label class="control-label"><?php echo $info['crop_type_name'];;?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $info['name'];;?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="pack_size_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($item['pack_size_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $item['name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="pack_size_id" name="item[pack_size_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($pack_sizes as $pack_size)
                        {
                            ?>
                            <option value="<?php echo $pack_size['value']?>" <?php if($pack_size['value']==$item['pack_size_id']){ echo "selected";}?>><?php echo $pack_size['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="masterfoil" class="control-label pull-right"><?php echo $this->lang->line('LABEL_MASTERFOIL');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[masterfoil]" id="price" class="form-control float_type_positive" value="<?php echo $item['masterfoil'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="foil" class="control-label pull-right"><?php echo $this->lang->line('LABEL_FOIL');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[foil]" id="foil" class="form-control float_type_positive" value="<?php echo $item['foil'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="sticker" class="control-label pull-right"><?php echo $this->lang->line('LABEL_STICKER');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[sticker]" id="sticker" class="form-control float_type_positive" value="<?php echo $item['sticker'];?>"/>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).off('change','#pack_size_id');
    });
</script>
