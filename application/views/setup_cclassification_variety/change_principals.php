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
    'data-message-confirm'=>'Are you sure to change principals?',
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'href'=>site_url($CI->controller_url.'/index/change_principals/'.$item['id'])
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_principals');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="row show-grid" style="overflow-x: auto;">
            <div class="col-xs-2"></div>
            <div class="col-xs-8">
                <table class="table table-bordered" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Principal Name</th>
                            <th style="width: 50px;"></th>
                            <th style="text-align: center;">Import Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($principals as $principal)
                            {
                                ?>
                                <tr>
                                    <td>
                                        <label style="font-weight: normal;" for="principal_id_<?php echo $principal['value']; ?>"><?php echo $principal['text']; ?></label>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="principal_ids[]" value="<?php echo $principal['value']; ?>" <?php if(array_key_exists($principal['value'],$assigned_principals)){echo 'checked';} ?> id="principal_id_<?php echo $principal['value']; ?>" data-id="<?php echo $principal['value']; ?>" class="principal_id">
                                    </td>
                                    <td>
                                        <input type="text" name="name_imports[<?php echo $principal['value']; ?>]" id="name_import_<?php echo $principal['value']; ?>" value="<?php if(isset($assigned_principals[$principal['value']])){echo $assigned_principals[$principal['value']]['name_import'];} ?>" class="form-control input-sm" <?php if(!isset($assigned_principals[$principal['value']])){echo 'disabled';} ?>>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-2"></div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off('change','.principal_id');
        $(document).on('change','.principal_id',function(event)
        {
            if($(this).is(':checked'))
            {
                $('#name_import_'+$(this).attr('data-id')).removeAttr('disabled').val('<?php echo $item['name']; ?>').focus();
            }
            else
            {
                $('#name_import_'+$(this).attr('data-id')).val('').attr('disabled',true);
            }
        });
    });
</script>
