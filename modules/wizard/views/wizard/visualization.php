<?php

use app\models\Material;
use wizard\models\forms\WizardForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/** @var View $this */
/** @var WizardForm $form */
/** @var Material[] $records */
/** @var array $room */

$this->title = 'Wizard - Step 4';
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->registerJsVar('records', $records);
$this->registerJsVar('room', $room);

?>

<script src="/js/three.js"></script>
<script src="/js/OrbitControls.js"></script>

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
  var defaultCameraAngle = 0
  var objectDepth = 0.2

  var scene = new THREE.Scene()
  var camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000)
  var renderer = new THREE.WebGLRenderer()
  var controls = new THREE.OrbitControls(camera, renderer.domElement)
  var raycaster = new THREE.Raycaster()
  var openings = []
  var _dragged = false
  var _target = null
  var _click = false

  let initDefaultValues = function () {
    renderer.domElement.addEventListener('mousedown', onPointerDown, false)
    renderer.domElement.addEventListener('touchstart', onPointerDown, false)
    renderer.domElement.addEventListener('mousemove', onPointerMove, false)
    renderer.domElement.addEventListener('touchmove', onPointerMove, false)
    renderer.domElement.addEventListener('mousemove', onPointerHover, false)
    renderer.domElement.addEventListener('touchmove', onPointerHover, false)
    renderer.domElement.addEventListener('mouseup', onPointerUp, false)
    renderer.domElement.addEventListener('mouseout', onPointerUp, false)
    renderer.domElement.addEventListener('touchend', onPointerUp, false)
    renderer.domElement.addEventListener('touchcancel', onPointerUp, false)
    renderer.domElement.addEventListener('touchleave', onPointerUp, false)

    controls.target = new THREE.Vector3(0, (room.sizeY + objectDepth) / 2, 0)
    controls.noZoom = true
    controls.noPan = true
    controls.minDistance = 0.1
    controls.maxDistance = 0.1
    controls.rotateSpeed = 0.5

    renderer.setSize(400, 400)
    renderer.setClearColor(0xFFFFFF, 1)

    document.getElementsByClassName('canvas')[0].appendChild(renderer.domElement)
  }

  let initSceneObjects = function (room, materials) {
    let floorGeometry = new THREE.BoxGeometry()
    let wallGeometryZ = new THREE.BoxGeometry()
    let wallGeometryX = new THREE.BoxGeometry()

    floorGeometry.scale(room.sizeX + objectDepth * 2, 0.2, room.sizeZ + objectDepth * 2)
    wallGeometryX.scale(room.sizeX, room.sizeY, 0.2)
    wallGeometryZ.scale(0.2, room.sizeY, room.sizeZ)

    let floorMesh = new THREE.Mesh(floorGeometry, materials['floors'])
    let wallMeshX1 = new THREE.Mesh(wallGeometryX, materials['walls'])
    let wallMeshX2 = new THREE.Mesh(wallGeometryX, materials['walls'])
    let wallMeshY1 = new THREE.Mesh(wallGeometryZ, materials['walls'])
    let wallMeshY2 = new THREE.Mesh(wallGeometryZ, materials['walls'])
    let cellMesh = new THREE.Mesh(floorGeometry, materials['cells'])
    let light = new THREE.PointLight(0xFFFFFF, 1.0)

    let statePosition = 0;

    Object.values(room.openings).forEach(function (opening) {
      let openingGeometry = new THREE.BoxGeometry()
      openingGeometry.scale(opening.width, opening.height, 0.2)
      let openingMesh = new THREE.Mesh(openingGeometry, getMaterial())

      openings.push(openingMesh)
      scene.add(openingMesh)

      openingMesh.position.z = room.sizeZ / 2 * ((statePosition % 4 === 0 || statePosition % 4 === 2) ? 1 : -1)
      openingMesh.position.x = (room.sizeX - opening.width) / 2 * ((statePosition % 4 === 0 || statePosition % 4 === 1) ? 1 : -1)
      openingMesh.position.y = (statePosition % 8 < 4) ? ((opening.height + objectDepth) / 2) : (room.sizeY + (objectDepth - opening.height) / 2)

      statePosition++
    })

    scene.add(floorMesh)
    scene.add(wallMeshX1)
    scene.add(wallMeshX2)
    scene.add(wallMeshY1)
    scene.add(wallMeshY2)
    scene.add(cellMesh)
    scene.add(light)

    wallMeshX1.position.y = (room.sizeY + objectDepth) / 2
    wallMeshX1.position.z = (room.sizeZ + objectDepth) / 2

    wallMeshX2.position.y = (room.sizeY + objectDepth) / 2
    wallMeshX2.position.z = (room.sizeZ + objectDepth) / -2

    wallMeshY1.position.x = (room.sizeX + objectDepth) / 2
    wallMeshY1.position.y = (room.sizeY + objectDepth) / 2

    wallMeshY2.position.x = (room.sizeX + objectDepth) / -2
    wallMeshY2.position.y = (room.sizeY + objectDepth) / 2

    cellMesh.position.y = room.sizeY + objectDepth

    light.position.set(0, (room.sizeY + objectDepth / 2) / 4, 0)

    camera.position.x = 0
    camera.position.y = (room.sizeY + objectDepth) / 2
    camera.position.z = 1
    camera.rotateY(THREE.PI / 180 * defaultCameraAngle)
  }

  let getTexture = function (pictureFileName) {
    let texture = new THREE.TextureLoader().load('img/materials/' + pictureFileName)
    texture.wrapS = THREE.RepeatWrapping
    texture.wrapT = THREE.RepeatWrapping
    texture.repeat.set(4, 4)

    return texture
  }

  let getMaterial = function (pictureFileName = null, color = 0xFFFFFF) {
    if (pictureFileName === null) {
      return new THREE.MeshPhysicalMaterial({ color: color })
    } else {
      return new THREE.MeshPhysicalMaterial({ map: getTexture(pictureFileName) })
    }
  }

  let stringColorToInt = function (stringColor) {
    let intColor = 0

    for (let i = 0; i < stringColor.length; i++) {
      intColor = stringColor.charCodeAt(i) + ((intColor << 5) - intColor)
    }

    if (stringColor === '000000') {
      return 0x000000
    }

    return intColor
  }

  let onPointerDown = function (event) {
    let pointer = event.changedTouches ? event.changedTouches[0] : event

    if (pointer.button === 2 || pointer.button === undefined) {
      let rect = renderer.domElement.getBoundingClientRect()
      let point = new THREE.Vector2((pointer.clientX - rect.left) / rect.width * 2 - 1, -(pointer.clientY - rect.top) / rect.height * 2 + 1)
      raycaster.setFromCamera(point, camera)
      let intersects = raycaster.intersectObjects(openings)

      if (intersects.length > 0) {
        _dragged = true

        event.preventDefault()
        event.stopPropagation()

        _target = intersects[0]
      } else {
        _click = false
      }
    }
  }

  let onPointerMove = function (event) {

  }

  let onPointerHover = function (event) {
    if (_dragged === false) return

    let rect = renderer.domElement.getBoundingClientRect()
    let pointer = event.changedTouches ? event.changedTouches[0] : event
    let point = new THREE.Vector2((pointer.clientX - rect.left) / rect.width * 2 - 1, -(pointer.clientY - rect.top) / rect.height * 2 + 1)
    raycaster.setFromCamera(point, camera)
    let intersects = raycaster.intersectObjects(openings)
    //_target.object.position.x = intersects[0].point.x

    let j = 0
    for (let i = 0; i < intersects.length; i++) {
      if (intersects[i].object.uuid === _target.object.uuid) {
        j = i

        break
      }
    }

    if (intersects.length > 0) {
      _target.object.position.y -= _target.point.y - intersects[j].point.y

      if (_target.object.rotation.y === 0) {
        _target.object.position.x -= _target.point.x - intersects[j].point.x
      } else {
        _target.object.position.z -= _target.point.z - intersects[j].point.z
      }

      _target = intersects[j]

      if (!_target.object.geometry.boundingBox) _target.object.geometry.computeBoundingBox()

      let targetSizeX = _target.object.geometry.boundingBox.max.x - _target.object.geometry.boundingBox.min.x
      let targetSizeY = _target.object.geometry.boundingBox.max.y - _target.object.geometry.boundingBox.min.y

      if (_target.object.rotation.y === 0) {
        if (Math.abs(_target.object.position.x) > room.sizeX / 2 - targetSizeX / 2) {
          _target.object.position.z = (_target.object.position.z > 0 ? 1 : -1) * (room.sizeZ - targetSizeX) / 2
          _target.object.position.x = room.sizeX / 2 * (_target.object.position.x > 0 ? 1 : -1)
          _target.object.rotation.y = Math.PI / 180 * 90
        }
      } else {
        if (Math.abs(_target.object.position.z) > room.sizeZ / 2 - targetSizeX / 2) {
          _target.object.position.x = (_target.object.position.x > 0 ? 1 : -1) * (room.sizeX - targetSizeX) / 2
          _target.object.position.z = room.sizeZ / 2 * (_target.object.position.z > 0 ? 1 : -1)
          _target.object.rotation.y = 0
        }
      }

      if (_target.object.position.y > room.sizeY - (targetSizeY - objectDepth) / 2) {
        _target.object.position.y = room.sizeY - (targetSizeY - objectDepth) / 2
      } else if (_target.object.position.y < (objectDepth + targetSizeY) / 2) {
        _target.object.position.y = (objectDepth + targetSizeY) / 2
      }
    }

    //_target.object.position.z = intersects[0].point.z
  }

  let onPointerUp = function (event) {
    _dragged = false
  }

  initDefaultValues()

  let materialNames = ['floors', 'walls', 'cells']
  let materials = []

  materialNames.forEach(function (materialName) {
    if (Object.keys(records).includes(materialName)) {
      if (records[materialName].use_pattern === 'picture') { // Picture Material
        materials[materialName] = getMaterial(records[materialName].picture)
      } else { // Color material
        materials[materialName] = getMaterial(null, stringColorToInt(records[materialName].color.slice(1)))
      }
    } else { // Empty material(default color 0xFFFFFF)
      materials[materialName] = getMaterial()
    }
  })

  initSceneObjects(room, materials)

  var animate = function () {
    requestAnimationFrame(animate)
    controls.update()
    renderer.render(scene, camera)
  }

  animate()
</script>
