@extends('layout.app')
@section('content')
    <style type="text/css">
        ul.top-menu > li > .logout{
            font-weight: 900;
            color: #378fe5;
        }
        ul.top-menu > li > a:hover, ul.top-menu > li > a:focus{
            border: 2px solid #ffffff !important;
            background-color: #378fe5 !important;
        }
        .event_container{
            margin: auto;
            float: none;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            width: 66.66% !important;
        }
        .image_container,.pdf_container,.video_container,.google_drive {
            margin-bottom: 5px;
            padding: 0px;
            padding-left: 12px;
            display: inline-flex;
        }
        .file_container{
           margin: 5px; 
        }
        .margin{
            margin: 10px;
        }
        .tag {
            padding: 5px;
            background: #378fe5;
            color: #fff;
            margin-right: 5px;
            border-radius: 5px;
        }
        span{
            font-size: 16px;
        }
        img{
            width:100px;height:100px;border: 2px solid #dadada;border-radius: 10px;
        }
        .fa{
            width:100px;height:100px;border: 2px solid #dadada;border-radius: 10px;
            padding: 25px !important;
            font-size: 50px;
            vertical-align: middle;
            color: #378fe5;
        }
        .icon:hover{
            border-color: #378fe5;
        }
        .label h3{
            width: 15%;
            text-align: center;
            background: #378fe5;
            color: #fff;
            border-radius: 25px;
            padding: 5px;
            font-size: 20px;
        }
        .comment-box{
            height: 350px;
            overflow-y: scroll;
        }
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
            background: #eaeaea;
        }
        .comment-content{
            /*background: #3a90e1;
            border-radius: 10px;*/
            margin-bottom: 10px;
            padding: 10px;
            border-bottom: 1px solid #d8d7d7;
            border-radius: 0;
        }
        .comment-box{
            padding: 10px 10px;
            border-radius: 5px;
            /*border: 5px solid #3a90e1;*/
            background: #f1f1f1;
            padding: 30px;
        }
        .comment-by{
            margin: 5px;
        }
        .comment-txt-box{
            margin: 10px;
            color: #fff;
            text-align:left;
        }
        .event-comment{
            font-weight: normal;
            font-size: 20px;
            color: #676767;
        }
        .comment-on, .comment-by{
            color: #676767;
        }
    </style>
    <section class="wrapper">
        <div class="row event_container">
            <div class="col-xs-12 col-sm-12 col-md-12 txt_center">
                <?php if(@$msg == ""){ ?>
                    <div>
                        <h1 class="theam_color" style="font-weight: 900;">Event</h1>
                    </div>
                    <div>
                        <div class="col-md-12 txt_left">
                            <div class="col-md-12 label">
                                <h3>Event Details</h3> 
                            </div>
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Title : </strong></span>
                                <span><?php print_r($event->title); ?></span> 
                            </div>
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Category : </strong></span>
                                <span><?php print_r($event->category_name); ?></span>
                            </div> 
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Description : </strong></span>
                                <pre style="background: transparent;border: none;font-family: 'Source Sans Pro', sans-serif !important;font-size: 16px;padding: 0px;"><?php print_r($event->description); ?></pre>
                            </div> 
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Tag : </strong></span>
                                <span>
                                    <?php 
                                        $tag = explode(',',$event->tag); 
                                        if(count(array_filter($tag)) > 0){
                                            foreach ($tag as $val) {
                                                echo '<span class="tag">'.$val.'</span>';
                                            }
                                        }    
                                    ?>
                                </span>
                            </div> 
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Location : </strong></span>
                                <span><?php print_r($event->location); ?></span>
                            </div>
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Start Date / Time : </strong></span>
                                <span class="start_date"><?php echo date('d M Y H:i A',strtotime($start_date_time)); ?></span>
                            </div>
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>End Date / Time : </strong></span>
                                <span class="end_date"><?php echo date('d M Y H:i A',strtotime($end_date_time)); ?></span>
                            </div>
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Created By : </strong></span>
                                <span class="end_date"><?php echo $created_by; ?></span>
                            </div>
                            <div class="margin col-md-12">
                                <span class="col-md-2"><strong>Last updated by : </strong></span>
                                <span class="end_date"><?php echo $last_updated_by; ?></span>
                            </div>

                            <?php 
                                if($event->media !=""){
                            ?>
                                <div class="col-md-12 label">
                                    <h3>Media</h3> 
                                </div>
                            <?php } ?>    
                            <div class="margin col-md-12">
                                <?php 
                                    // $media = explode(',',$event->media); 
                                    $image = "";
                                    $pdf = "";
                                    $video = "";
                                    $google_drive = "";
                                    $container = "";
                                    foreach ($media as  $value) {
                                        // $info = pathinfo(url('/')."/".$value);
                                        $info = pathinfo($value->file_url);

                                        if(isset($info['extension'])){
                                          $extension = $info['extension'];      
                                        }elseif(isset($value->file_type) && $value->file_type !=""){
                                          $extension =  $value->file_type;
                                        }else{
                                          $extension = "google_drive";  
                                        }
                                        //  echo $extension;
                                        // continue;
                                        // $url = $value;
                                        $image_icon = url("/")."/public/uploads/icons/image_icon.png";
                                        $pdf_icon = url("/")."/uploads/icons/pdf_icon.png";
                                        $video_icon = url("/")."/uploads/icons/video_icon.png";
                                        if(($extension == "png" || $extension == "jpeg" || $extension == "jpg") && $value->file_url !=""){
                                            $container .='<div class="file_container">
                                                            <a href="'.$value->file_url.'" target="_blank">
                                                                <img class="icon" src="'.$value->file_url.'">
                                                            </a>
                                                          </div>';
                                        }elseif ($extension == "pdf" && $value->file_url !="") { 
                                            $container .='<div class="file_container">
                                                            <a href="'.$value->file_url.'" target="_blank">
                                                                <i class="fa fa-file-pdf-o icon" aria-hidden="true"></i>
                                                            </a>
                                                        </div>';
                                        }else if($extension !="" && $extension !="google_drive"){ 
                                            $container .='<div class="file_container">
                                                            <a href="'.$value->file_url.'" target="_blank">
                                                                <i class="fa fa-video-camera icon" aria-hidden="true"></i>
                                                            </a>
                                                        </div>';
                                        }else{
                                            $container .='<div class="file_container">
                                                            <a href="'.$value->file_url.'" target="_blank">
                                                                <i class="fa fa-google icon" aria-hidden="true"></i>
                                                            </a>
                                                         </div>';

                                        }

                                    }
                                ?>
                                <div class="image_container col-md-12" >
                                    <?php echo $container ?>
                                </div>
                                <!-- <div class="video_container col-md-12">
                                    <?php echo $container ?>
                                </div>
                                <div class="pdf_container col-md-12">
                                    <?php echo $container ?>
                                </div>
                                <div class="google_drive col-md-12">
                                    <?php echo $container ?>
                                </div> -->
                            </div> 
                            <div class="col-md-12 label">
                                <h3>Comments</h3> 
                            </div>

                            <!-- COMMENT LISTING -->

                            <div class="col-md-12 comment-box">
                                
                            </div>

                            <!-- ADD COMMENT -->

                            <div class="col-md-12 label">
                                <h3>Your Comment</h3> 
                            </div>
                            <div class="col-md-12">
                                <textarea id="comment" class="col-md-10" style="height: 100px;border: 2px solid #eaeaea;border-radius: 10px;outline: none;font-size: 14px;"></textarea>
                                <button class="btn add_comment" onclick="add_comment('<?php echo $event->event_id; ?>');" style="padding:10px 25px;background: #378fe5;color: white;font-weight: 900;margin: 30px;">ADD</button>
                            </div>
                             
                        </div>
                    </div>
                <?php }else if(isset($event_error) && @$event_error != ""){ ?>
                    <h1 class="theam_color" style="margin: 20% 0px;">Now you can view the event which is shared with you. To view the event use the link you received from Calendar App.</h1>
                    <script type="text/javascript">
                        swal({
                            title: "Calendar App",
                            text: "<?php print_r($event_error); ?>",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Logout",
                            cancelButtonText: "Cancel",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm){
                            if(isConfirm == true){
                                window.location.href='<?php echo url('/logout'); ?>';
                            }
                            if(isConfirm == false){
                                window.location.href='<?php echo url('/'); ?>';
                                $('.default').show();
                            }
                        });
                    </script> 

                <?php }else{ ?>

                    <div>
                        <h1 class="theam_color" style="margin: 20% 0px;"><?php print_r($msg); ?></h1>
                    </div>

                <?php } ?>                  

            </div>    
        </div>    
    </section>
    <?php if(@$msg == ""){ ?>
        <script type="text/javascript">

            // $(document).ready(function(){
            //   setTimeout(function(){
            //     var start_date =  covert_utc_to_regional('<?php echo date('Y-m-d H:i:s',strtotime($start_date_time)); ?>');
            //    $('.start_date').text(start_date);
            //     var end_date =  covert_utc_to_regional('<?php echo date('Y-m-d H:i:s',strtotime($end_date_time)); ?>');
            //     $('.end_date').text(end_date);
            //   },50);
            // });
          
          function covert_utc_to_regional(created_at){
            var timezone_offset_minutes = new Date().getTimezoneOffset();
            timezone_offset_minutes = timezone_offset_minutes == 0 ? 0 : -timezone_offset_minutes;
            var date = new Date(created_at.replace(' ', 'T'));
            var milliseconds = date.getTime();
            if(timezone_offset_minutes > 0){
              var time = parseInt(milliseconds) + parseInt(timezone_offset_minutes* 60 * 1000);
            } else{
              var time = parseInt(milliseconds) - parseInt(timezone_offset_minutes* 60 * 1000);
            }
            var created_date = moment(time); 
            var q_date = created_date.format("DD MMMM Y hh:mm a");
            return q_date;
          }

        </script>

        <!-- ADD COMMENT ON EVENT -->

        <script type="text/javascript">
            $( "#comment" ).keypress(function(e) {
                if (e.which == 13 || e.keyCode == 13) {
                    $('.add_comment').trigger('click');
                }
            });
            function add_comment(event_id){
                var comment = $('#comment').val();
               
                if(comment == ''){
                    swal('Eclat App','Please enter comment.','warning');
                    return false;
                }
                if(comment.length  > 256 ){
                    swal('Eclat App','The maximum length of comment that you can add is 256 characters.','warning');
                    return false;
                }
                $.ajax({
                    url: '/event/add_event_comment',
                    type: 'post',
                    data: {
                        'event_id': event_id,
                        'comment': comment
                    },
                    success: function (res) {
                      $('#comment').val('');
                      get_comment();
                      swal('Eclat App','Your comment added.','success');
                    }
                });
            }
        </script>
    <?php } ?>
    <script type="text/javascript">

        //-------------- GET ALL COMMENT ----------------- //

        $(function(){
            get_comment();
        });

        //-------------- REFRESH COMMENT ON 10 SECOND ----------------- //

        // setInterval(function(){
        //     get_comment();
        // },5000);

        //-------------- GET COMMENT BY ID ----------------- //

        function get_comment(){
            var event_id = '<?php echo @$event->event_id; ?>';
            $.ajax({
                url: '/event/get_event_comment',
                type: 'post',
                data: {
                    'event_id': event_id
                },
                success: function (res) {
                    var obj = JSON.parse(res);
                    var html = "";
                    var base_url = '<?php echo url("/")."/" ?>';
                    for (var i = 0, len = obj.length; i < len; i++){
                    var commentDate = covert_utc_to_regional(obj[i].comment_created_at);
                    html += '<div class="comment-content comment_'+obj[i].comment_id+' col-md-12">'+
                                '<div class="col-md-12 commented_user">'+
                                    '<span class="comment-by" style="float: left;text-transform: capitalize;"><img src="'+base_url+''+obj[i].profile_pic+'" style="width:50;height:50px;border-radius:50%;margin:5px;"><b>'+obj[i].name+'</span>'+
                                    '<span class="comment-on" style="float: right;">'+commentDate+'</span>'+
                                '</div>'+
                                '<div class="col-md-12 comment-txt-box">'+
                                    '<span class="event-comment">'+obj[i].comment+'</span>'+
                                '</div>'+
                            '</div>';
                    }
                    if(obj.length == 0){
                        $('.comment-box').css({
                                                'height':'100px',
                                                'text-align':'center'
                                             });
                        $('.comment-box').html('<h3 style="margin:0;color: #dadada;">No comment yet.</h3>');
                    }else{
                        $('.comment-box').css({
                                                'height':'350px'
                                             });
                        $('.comment-box').html(html); 
                        $( ".comment-box" ).scrollTop( 0 );
                    }
                    

                }
            });
        }
    </script>

@endsection
