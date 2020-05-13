<?php

use app\models\Material;
use wizard\models\forms\WizardForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;


$this->title = 'Wizard - Step 4';

$this->params['breadcrumbs'][] = ['label' => $this->title];

/** @var View $this */
/** @var WizardForm $form */
/** @var Material[] $records */

?>

<script src="/js/three.js"></script>

<h1><?=Html::encode($this->title)?></h1>
<div style="display: flex;">
    <div class="canvas" style="margin-right: 16px">

    </div>
    <div class="form-group">
        <?php $htmlForm = ActiveForm::begin(['id' => 'model', 'action' => '/wizard?step=5']); ?>

        <?=$htmlForm->field($form, 'floor_width')->hiddenInput()->label(false)?>
        <?=$htmlForm->field($form, 'floor_height')->hiddenInput()->label(false)?>
        <?=$htmlForm->field($form, 'wall_height')->hiddenInput()->label(false)?>
        <div class="form-group openings">
            <?php foreach ($form->openings as $idx => $opening): ?>
                <input type="hidden" class="form-control" name="WizardForm[openings][<?=$idx?>][width]"
                       value="<?=$opening['width']?>">
                <input type="hidden" class="form-control" name="WizardForm[openings][<?=$idx?>][height]"
                       value="<?=$opening['height']?>">
            <?php endforeach ?>
        </div>
        <div class="form-group materials">
            <?php foreach ($form->usage_material as $idx => $usage_material): ?>
                <input type="hidden" class="form-control" name="WizardForm[usage_material][<?=$idx?>]"
                       value="<?=$usage_material?>">
            <?php endforeach ?>
            <?php foreach ($form->materials as $idx => $material): ?>
                <input type="hidden" class="form-control" name="WizardForm[materials][<?=$idx?>]"
                       value="<?=$material?>">
            <?php endforeach ?>
        </div>

        <div class="form-group">
            <?=Html::submitButton('Generate Bill', ['class' => 'btn btn-success'])?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
  var scene = new THREE.Scene()
  var camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000)
  var renderer = new THREE.WebGLRenderer()
  renderer.setSize(400, 400)
  renderer.setClearColor( 0x9dd0ff, 1 );
  document.getElementsByClassName('canvas')[0].appendChild(renderer.domElement)

  var floor = new THREE.BoxGeometry()

  <?php if (isset($records['floors']) && $records['floors']->use_pattern === 'picture'): ?>
  var textureFloor = new THREE.TextureLoader().load('img/materials/<?=$records['floors']->picture?>')
  textureFloor.wrapS = THREE.RepeatWrapping
  textureFloor.wrapT = THREE.RepeatWrapping
  textureFloor.repeat.set(4, 4)
  var floorMaterial = new THREE.MeshPhysicalMaterial({ map: textureFloor })
  <?php elseif (isset($records['floors'])):?>
  var floorMaterial = new THREE.MeshPhysicalMaterial({ color: <?=str_replace('#', '0x', $records['floors']->color)?> })
  <?php else:?>
  var floorMaterial = new THREE.MeshPhysicalMaterial({ color: 0xFFFFFF })
  <?php endif;?>
  var floorObj = new THREE.Mesh(floor, floorMaterial)

  var wall1 = new THREE.BoxGeometry()
  var wall2 = new THREE.BoxGeometry()

  <?php if (isset($records['walls']) && $records['walls']->use_pattern === 'picture'): ?>
  var textureWall = new THREE.TextureLoader().load('img/materials/<?=$records['walls']->picture?>')
  textureWall.wrapS = THREE.RepeatWrapping
  textureWall.wrapT = THREE.RepeatWrapping
  textureWall.repeat.set(4, 4)
  var wallMaterial = new THREE.MeshPhysicalMaterial({ map: textureWall })
  <?php elseif (isset($records['walls'])):?>
  var wallMaterial = new THREE.MeshPhysicalMaterial({ color: <?=str_replace('#', '0x', $records['walls']->color)?> })
  <?php else:?>
  var wallMaterial = new THREE.MeshPhysicalMaterial({ color: 0xFFFFFF })
  <?php endif;?>
  var wall1Obj = new THREE.Mesh(wall1, wallMaterial)
  var wall2Obj = new THREE.Mesh(wall2, wallMaterial)

  var cell = new THREE.BoxGeometry()

  <?php if (isset($records['cells']) && $records['cells']->use_pattern === 'picture'): ?>
  var textureCell = new THREE.TextureLoader().load('img/materials/<?=$records['cells']->picture?>')
  textureCell.wrapS = THREE.RepeatWrapping
  textureCell.wrapT = THREE.RepeatWrapping
  textureCell.repeat.set(4, 4)
  var cellMaterial = new THREE.MeshPhysicalMaterial({ map: textureCell })
  <?php elseif (isset($records['cells'])):?>
  var cellMaterial = new THREE.MeshPhysicalMaterial({ color: <?=str_replace('#', '0x', $records['cells']->color)?> })
  <?php else:?>
  var cellMaterial = new THREE.MeshPhysicalMaterial({ color: 0xFFFFFF })
  <?php endif;?>
  var cellObj = new THREE.Mesh(cell, cellMaterial)

  floor.scale(6, 0.2, 6)
  cell.scale(6, 0.2, 6)
  wall1.scale(0.2, 4, 6)
  wall2.scale(6, 4, 0.2)

  cellObj.position.y = 4
  wall1Obj.position.x = -3
  wall1Obj.position.z = 0
  wall1Obj.position.y = 2
  wall2Obj.position.x = 0
  wall2Obj.position.z = -3
  wall2Obj.position.y = 2

  scene.add(floorObj)
  scene.add(wall1Obj)
  scene.add(wall2Obj)
  scene.add(cellObj)

  var light = new THREE.PointLight(0xFFFFFF, 1.0)
  light.position.set(0, 3.0, 0)
  scene.add(light)

  var light2 = new THREE.PointLight(0xFFFFFF, 0.3)
  light2.position.set(7, 2, 7)
  //scene.add(light2)

  camera.position.x = 4
  camera.position.y = 1
  camera.position.z = 4
  camera.rotateY(3.4 / 360 * 85)

  var animate = function () {
    requestAnimationFrame(animate)
    let a = scene.getObjectById('Box1', true)
    if (a != null) {
      console.log('test')
    }

    renderer.render(scene, camera)
  }

  animate()
</script>
