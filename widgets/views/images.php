<?php

/** @var $this \yii\web\View */

seiweb\sortable\assets\RubaxaAsset::register($this);
seiweb\image\assets\NotyAsset::register($this);

seiweb\image\assets\FontawesomeAsset::register($this);
seiweb\image\assets\LoadingOverlayAsset::register($this);

use kartik\widgets\FileInput;

use yii\web\View;
use yii\widgets\ListView;



$widgetId = $this->context->getId();

$sortableinitjs = <<<JS
var srt = Sortable.create(imagescnt$widgetId, {
  group: 'shared',
  animation: 150,
  draggable: '.preview',
  	onEnd: function (/**Event*/evt) {
      $.post({
          url:$(this.el).data('sortable-url'),
          data:{sorting:this.toArray()}
      })
	}
});
JS;

$this->registerJs($sortableinitjs);

$sortableJS = <<<JS

$.LoadingOverlaySetup({
    color           : "rgba(0, 0, 0, 0.4)",

    minSize         : "20px",
    resizeInterval  : 100,
    size            : "50%"
});

var srt = Sortable.create(imagescnt$widgetId, {
  group: 'shared',
  animation: 150,
  draggable: '.preview',
  	onEnd: function (/**Event*/evt) {
      $.post({
          url:$(this.el).data('sortable-url'),
          data:{sorting:this.toArray()}
      })
	}
});

/**
 * Редактирование файла в модальном окне
 */

$(document).on('click', '.editImageModal', function () {
    $('#modal').modal('show')
        .find('#modalContent')
        .load($(this).data('url'));
});

$(document).on('beforeSubmit','#image_form', function(e){
    var form = jQuery(this);
    var btn_name = form.data('yiiActiveForm').submitObject.attr('name');
    var data = form.serializeArray();
    var imageId = form.data('image-id');
    
    jQuery.post(
        form.attr("action"),
        data
    )
    .done(function(result) {
           if(btn_name=='save'){
                $("#modal").modal("hide");
                replaceImage(imageId,form.data('render-url'));
                return;
            }
         form.parent().html(result);
    })
    .fail(function() {
        console.log("server error");
    });
    return false;
});

function replaceImage(id,url)
{
    var html = $.get({url:url,async:false});
    $('[data-id='+id+']').replaceWith(html.responseText);
}

/**
* Set image as main
*/

 $(document).on('click', '.set-as-main', function(e){
        e.preventDefault();
        var url = $(this).data('url');
       
        $.ajax({
            url: url,
            type: 'get',
            dataType:'json',
            success: function(result) {
                srt.sort(result.order);
                    }
        });
    });
/*
    Delete one image
 */

  $(document).on('click', '.js-remove', function(e){
        e.preventDefault();
        
        var item = $(this);
        var url = $(this).data('url');
       
        $.ajax({
            url: url,
            type: 'get',
            dataType:'json',
            success: function(result) {
                        $('[data-id='+item.data('image-id')+']').fadeOut(200);
                        $('[data-id='+item.data('image-id')+']').remove();
                    }
        });
    });

JS;

$this->registerJs($sortableJS,View::POS_READY,'images_js');

?>


<?php
\yii\bootstrap4\Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    //'header' => null,
    'closeButton' => false,
    'id' => 'modal',
    'size' => 'modal-lg',
    //'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
    'clientOptions' => false,
]);
echo "<div id='modalContent'>Загрузка</div>";
\yii\bootstrap4\Modal::end();
?>


<style>
    .imagescnt {
        padding: 0 10px;
        min-height: 150px;
    }

    ul.images li {
        float: left;
    }

    div.preview {
        position: relative;
        margin-bottom: 10px;
        padding-left: 5px;
        padding-right: 5px;
        /*
        display: table;
        margin: 8px;
        margin-right: 5px;
        margin-bottom: 5px;
        border: 1px solid #ddd;
        box-shadow: 1px 1px 5px 0 #a2958a;
        padding: 6px;
        float: left;
        text-align: center;
        */
    }

    div.preview img {
        border: 1px solid #ddd;
        box-shadow: 1px 1px 5px 0 #a2958a;
    }

    div.preview button, div.preview button:hover, div.preview button:active:hover, div.preview button:active:focus {
        outline-width: 0;
        border: 0px;
        background-color: #000;
        color: #ffffff;
    }

    div.preview img:hover {
        box-shadow: 3px 3px 5px 0 #333;
    }

    div.preview .file-footer-buttons1 {
        opacity: 0;
        visibility: hidden;
        display: none;
    }

    div.preview:hover .file-footer-buttons1 {
        opacity: 0.9;
        visibility: visible;
        position: absolute;
        bottom: 10px;
        right: 20px;
        transition: opacity 0.9s, visibility 0.9s;
        display: block;
    }

    span.noty_text {
        font-size: 12px;
        line-height: 14px;
    }
</style>



        <div class="row">
            <div class="col-md-10">

                <?= ListView::widget([
                    'dataProvider' => $imagesProvider,
                    'itemView' => '_image',
                    'emptyText'=>'Ни одного изображения еще не загружено',
                    'emptyTextOptions' => ['class' => 'empty col-sm-12'],
                    'layout' => "{items}",
                    'options' => [
                        'tag' => 'div',
                        'class' => 'row imagescnt',
                        'id' => 'imagescnt' . $widgetId,
                        'data-sortable-url' => \yii\helpers\Url::to(['/swb_image/admin/sort'])
                    ],
                    'itemOptions' => [
                        'tag' => null,
                        //'class' => 'col-sm-2 col-xs-4 preview',
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-2">
                <?php

                $uploadJS = <<<JS
function(e){
    $('#imagescnt$widgetId').LoadingOverlay("show");
    $(this).fileinput('upload');
 }
JS;

                $fileuploaderror = <<<EOD
function(event, data, msg) {
    if(data.jqXHR.responseJSON!=null)
        msg = data.jqXHR.responseJSON.message;
    new Noty({type: 'error',layout: 'topRight',text: msg}).show();
    $('#imagescnt$widgetId').LoadingOverlay("hide");
}
EOD;

                $batchuploadCompleteJS = <<<JS
function(event, files, extra) {
    //$.pjax.reload({container: '#pjax$widgetId'});
    //$('#imagescnt$widgetId').unblock();
        $('#imagescnt$widgetId').LoadingOverlay("hide");
}
JS;

                $fileuploaded = <<<JS
function(event, data, previewId, index) {
    var response = data.response;
    var new_image = $(response.html).hide();
    var test = new_image.find('img').attr('src');
    $(new Image()).attr('src', test).on('load',function(){
    $('#imagescnt$widgetId').append(new_image);
    new_image.fadeIn(100);
    });
     $('#imagescnt$widgetId .empty').hide();
}
JS;

                ?>
                <?php
                echo FileInput::widget([
                    'name' => 'swb_image',
                    'language' => 'ru',
                    'options' => [
                        'multiple' => $this->context->multiple,
                        'accept' => implode(',',preg_filter('/^/', '.', $allowedFileExtensions)),
                        'id' => $widgetId,

                    ],
                    'pluginOptions' => [
                        'maxFileSize' => $maxFileSize,
                        'allowedFileExtensions' => $allowedFileExtensions,
                        'maxFileCount' => $maxFileCount,
                        'showPreview' => false,
                        'uploadAsync' => true,
                        'showUpload' => false,
                        'showCaption' => false,
                        'showRemove' => false,
                        'showCancel' => false,
                        'overwriteInitial' => false,

                        'browseClass' => 'btn btn-primary btn-block',
                        'browseIcon' => '<i class="fas fa-images" aria-hidden="true"></i> ',
                        'browseLabel' => 'Загрузить фото',

                        'uploadUrl' => urldecode(Yii::$app->urlManager->createUrl(['/swb_image/admin/upload'])),
                        'uploadExtraData' => [
                            'model_class' => $model::className(),
                            'model_id' => $model->primaryKey,
                            'model_attribute'=>$model_attribute == null ? '' : $model_attribute,
                            'replace' => $this->context->replace,
                        ],
                        'dragSettings' => [
                            'animation' => 500,
                        ],
                    ],
                    'pluginEvents' => [
                        'filebatchselected' => new \yii\web\JsExpression($uploadJS),
                        'fileuploaderror' => new \yii\web\JsExpression($fileuploaderror),
                        'filebatchuploadcomplete' => new \yii\web\JsExpression($batchuploadCompleteJS),
                        'fileuploaded' => new \yii\web\JsExpression($fileuploaded)
                    ]
                ]);

                ?>
            </div>
        </div>
