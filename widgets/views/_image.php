<?php /** @var $model \seiweb\image\models\Image */ ?>
<div class='col-sm-2 col-xs-6 preview' data-id='<?= $model->id ?>'>
    <?= \yii\helpers\Html::img($model->getFitUrl(300, 300), ['class' => 'img-responsive','style'=>'display:block;min-height:100px']) ?>
    <div class='file-footer-buttons1'>
        <?= \yii\helpers\Html::a('<i class=\'fas fa-file-download\' aria-hidden=\'true\'></i>',['/swb_image/admin/download','id'=>$model->id],['class'=>'btn btn-success btn-xs']) ?>
        <button type='button' data-url='<?= \yii\helpers\Url::to(['/swb_image/admin/update', 'id' => $model->id]) ?>'
                class='btn btn-xs btn-default editImageModal' title='Редактор'><i class='fas fa-cog'
                                                                                  aria-hidden='true'></i>
        </button>
        <button type='button' class='btn btn-xs btn-default js-remove' title='Удалить файл'
                data-image-id="<?= $model->id ?>"
                data-url='<?= \yii\helpers\Url::to(['/swb_image/admin/delete', 'id' => $model->id]) ?>'>
            <i class='fas fa-trash-alt text-danger' aria-hidden='true'></i>
        </button>

        <!--
        <button type='button' class='btn btn-xs btn-default set-as-main' title='Сделать главной'
                data-url='<?= \yii\helpers\Url::to(['/swb_image/admin/set-as-main', 'id' => $model->id]) ?>'><i
                    class='glyphicon glyphicon-home text-primary'></i></button>
                    -->
    </div>
</div>