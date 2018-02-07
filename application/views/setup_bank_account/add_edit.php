<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
        'id'=>'button_action_save_new',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select name="item[bank_id]" id="bank_id" class="form-control ">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($banks as $bank)
                    {
                        ?>
                        <option value="<?php echo $bank['value']?>" <?php if($bank['value']==$item['bank_id']){echo "selected='selected'";}?>><?php echo $bank['text']?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BRANCH_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[branch_name]" id="branch_name" class="form-control " value="<?php echo $item['branch_name'];?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ACCOUNT_NUMBER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[account_number]" id="account_number" class="form-control  " value="<?php echo $item['account_number'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ACCOUNT_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label for="account_type_receive">
                    <input type="checkbox" name="item[account_type_receive]" id="account_type_receive" class="" value="1" <?php if($item['account_type_receive']==1){echo "checked='checked'";}?> />
                    Receive
                </label>
                <label for="account_type_expense">
                    <input type="checkbox" name="item[account_type_expense]" id="account_type_expense" class="" value="1" <?php if($item['account_type_expense']==1){echo "checked='checked'";}?> />
                    Expense
                </label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DESCRIPTION');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[description]" id="description" class="form-control" ><?php echo $item['description'];?></textarea>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="status" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="item[status]" class="form-control">
                    <!--<option value=""></option>-->
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>" <?php if ($item['status'] == $CI->config->item('system_status_active')) { echo "selected='selected'"; } ?> ><?php echo $CI->lang->line('ACTIVE') ?></option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>" <?php if ($item['status'] == $CI->config->item('system_status_inactive')) { echo "selected='selected'"; } ?> ><?php echo $CI->lang->line('INACTIVE') ?></option>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-offset-4 col-xs-8 text-center">
                <?php
                $system_bank_account_purpose=$this->config->item('system_bank_account_purpose');
                ?>
                <table class="table table-responsive table-bordered" style="width: 50%">
                    <thead>
                    <tr>
                        <th colspan="<?php echo count($system_bank_account_purpose);?>" class="text-center"><?php echo $this->lang->line('LABEL_ACCOUNT_PURPOSE')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <?php
                        $purpose=array();
                        foreach($items as $row)
                        {
                            $purpose[$row['purpose']]=$row['purpose'];
                        }

                        foreach($system_bank_account_purpose as $key=>$value)
                        {
                            ?>
                            <td>
                                <label for="purpose_<?php echo $key?>">
                                    <input type="checkbox" name="items[purpose][]" id="purpose_<?php echo $key?>" class="" value="<?php echo $key;?>" <?php if(isset($purpose[$key])){echo "checked='checked'";}?> />
                                    <?php echo $value;?>
                                </label>
                            </td>
                        <?php
                        }
                        ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
