<?php

/** @var $image \seiweb\image\models\Image */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'image_form',
    'action' => Url::to(['/swb_image/admin/update', 'id' => $image->id]),
    'layout' => 'horizontal',
    'options' => [
        'enctype' => 'multipart/form-data',
        'method' => 'POST',
        'data-image-id'=>$image->id,
        'data-render-url'=>Url::to(['/swb_image/admin/render-image', 'id' => $image->id])
    ]
]); ?>

<div class="row">
    <div class="col-sm-3">
        <button type="submit" name="btn-rotate" value="rotate"
                formaction="<?= Url::to(['/swb_image/admin/rotate', 'id' => $image->id]) ?>"><i class="fas fa-redo"
                                                                                                aria-hidden="true"></i>
        </button>
        <button type="submit" name="btn-rotate" value="rotate"
                formaction="<?= Url::to(['/swb_image/admin/rotate', 'id' => $image->id, 'angle' => 90]) ?>"><i
                    class="fas fa-undo" aria-hidden="true"></i></button>
        <hr/>
        <?= Html::img($image->getResizedUrl(300, 300) . '?t=' . time(), ['class' => 'img-responsive']) ?>
    </div>
    <div class="col-sm-9">
        <?= $form->field($image, 'title')->textInput(); ?>
        <?= $form->field($image, 'description')->textarea(); ?>

        <div class="form-group">
            <label class="control-label col-sm-3">Выравнивание миниатюр</label>
            <div class="col-sm-2">
                <table>
                    <tr>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'top-left' ? 'checked=""' : '' ?>
                                   value="top-left"></td>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'top' ? 'checked=""' : '' ?>
                                   value="top"></td>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'top-right' ? 'checked=""' : '' ?>
                                   value="top-right"></td>
                    </tr>
                    <tr>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'left' ? 'checked=""' : '' ?>
                                   value="left"></td>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'center' ? 'checked=""' : '' ?>
                                   value="center"></td>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'right' ? 'checked=""' : '' ?>
                                   value="right"></td>
                    </tr>
                    <tr>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'bottom-left' ? 'checked=""' : '' ?>
                                   value="bottom-left"></td>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'bottom' ? 'checked=""' : '' ?>
                                   value="bottom"></td>
                        <td><input type="radio"
                                   name="Image[position]" <?= $image->position == 'bottom-right' ? 'checked=""' : '' ?>
                                   value="bottom-right"></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-3">
                <?= Html::img($image->getFitUrl(300, 300, $image->position) . '?t=' . time(),['width'=>'100px']) ?>
            </div>

        </div>


    </div>
</div>



<?php // Html::label('Загрузить другой файл вместо этого') ?>
<?php // Html::fileInput('swb_file') ?>
<div class="pull-right">
    <?= Html::submitButton('Сохранить', ['name' => 'save']) ?>
    <?= Html::submitButton('Применить', ['name' => 'apply']) ?>
    <?= Html::resetButton('Отмена', ['data-dismiss' => "modal"]) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
?>
