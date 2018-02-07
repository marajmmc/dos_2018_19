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
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_document');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $customer['id']; ?>" />
    <div class="row widget">
        <div id="files_container">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;"><?php echo $title;?></th>
                            <th style="min-width: 50px;">Upload</th>
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
                                            ?><a class="external" href="<?php echo $CI->config->item('system_base_url_customer_document').$file['file_location'];?>" target="_blank"><?php echo $file['file_name'];?></a><?php
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <input type="file" id="file_<?php echo $index+1; ?>" name="file_<?php echo $index+1; ?>" data-current-id="<?php echo $index+1;?>" data-preview-container="#preview_container_file_<?php echo $index+1;?>" class="browse_button"><br>
                                    <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                    <input type="hidden" name="files[file_<?php echo $index+1;?>]" value="<?php  echo $file['file_name'];?>">
                                    <input type="hidden" name="files[file_type_<?php echo $index+1;?>]" value="<?php  echo $file['file_type'];?>">
                                </td>
                                <td style="max-width: 100px;">
                                    <textarea class="form-control remarks" id="remarks" name="remarks[<?php echo $index+1;?>]"><?php if(isset($file['file_remarks'])){echo $file['file_remarks'];} ?></textarea>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($file_details);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
            </div>
            <div class="col-xs-4">

            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>


<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <div class="preview_container_file">
                </div>
            </td>
            <td>
                <input type="file" class="browse_button_new"><br>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                <input type="hidden" class="is_preview" name="" value="0">
            </td>
            <td>
                <textarea class="form-control remarks"></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</div>



<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off("click", ".system_button_add_more");
        $(document).off("click", ".system_button_add_delete");

        $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .browse_button_new').attr('data-preview-container','#preview_container_file_'+current_id);
            $(content_id+' .browse_button_new').attr('name','file_'+current_id);
            $(content_id+' .browse_button_new').attr('id','file_'+current_id);
            $(content_id+' .remarks').attr('name','remarks['+current_id+']');
            $(content_id+' .preview_container_file').attr('id','preview_container_file_'+current_id);
            $(content_id+' .is_preview').attr('name','files['+current_id+']');

            var html=$(content_id).html();
            $("#files_container tbody").append(html);

            $(content_id+' .browse_button_new').removeAttr('name');
            $(content_id+' .browse_button_new').removeAttr('data-preview-container');
            $(content_id+' .browse_button_new').removeAttr('id');
            $(content_id+' .preview_container_file').removeAttr('id');
            $('#file_'+current_id).filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

        });
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });
    });


</script>
