<?php $title = "上传文件";
include_once('header.php'); ?>
<div class="row">
    <form enctype="multipart/form-data">
        <div class="form-group">
            <!--label for="filename" class="col-sm-2 control-label">文件名</label-->
            <div class="col-sm-10">
                <input type="file" class="form-control" id="filename" name="upload_file" enctype="multipart/form-data"/>
                <progress></progress>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2">
                <button class="btn form-control btn-primary" id="upload">上传</button>
            </div>
        </div>

    </form>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(':file').change(function(){
            var file = this.files[0];
            name = file.name;
            size = file.size;
            type = file.type;
            //your validation
        });
        $('button#upload').click(function () {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url: 'upload.php',  //server script to process data
                type: 'POST',
                xhr: function () {  // custom xhr
                    myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) { // check if upload property exists
                        myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // for handling the progress of the upload
                    }
                    return myXhr;
                },
                //Ajax事件
                beforeSend: beforeSendHandler,
                success: completeHandler,
                error: errorHandler,
                // Form数据
                data: formData,
                //Options to tell JQuery not to process data or worry about content-type
                cache: false,
                contentType: false,
                processData: false
            });
        });
        function progressHandlingFunction(e){
            if(e.lengthComputable){
                $('progress').attr({value:e.loaded,max:e.total});
            }
        }

        function beforeSendHandler(){}
        function completeHandler(){
            $('body').append($('<div></div>').text('上传成功'));
        }
        function errorHandler(){
            alert('上传失败!');
        }
    });
</script>
<?php include_once('footer.php'); ?>
