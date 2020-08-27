<?php

use app\models\Material;
use wizard\models\forms\WizardForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/** @var View $this */
/** @var WizardForm $form */
/** @var Material[] $records */
/** @var Material[] $materials */
/** @var array $room */

$this->title = 'Wizard - Step 4';
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->registerJsVar('records', $records);
$this->registerJsVar('room', $room);
$this->registerJsVar('selectedElements', $form->materials);
$this->registerJsVar('usages', $form->usage_material);
$this->registerJsVar('allMaterials', $materials);

$this->registerJs(
    <<<'JS'

$('#usage-list label input').change(function () {
  if (this.checked) {
    if (usages.indexOf(this.value) === -1) {
      usages.push(this.value)
    }
  } else {
    let idx = usages.indexOf(this.value)
    if (idx !== -1) {
      usages.splice(idx, 1)
    }
  }
  
  displayBlocks()
})

$('.block').click(onBlockClick)

$('.block').click(function () {
  let id = this.id.replace('material-', '')
  
  let parent = $(this).parent().get(0).id.replace('block-', '');
  
  if (parent in selectedElements && selectedElements[parent] === id) {
    $('#material-' + selectedElements[parent]).removeClass('block-active')
    $('#material-' + selectedElements[parent]).find('.label-use').hide()
    $('.input-' + parent).remove();
    delete selectedElements[parent]
  } else {
    if (parent in selectedElements) {
      $('#material-' + selectedElements[parent]).removeClass('block-active')
      $('#material-' + selectedElements[parent]).find('.label-use').hide()
      $('.input-' + parent).remove()
    }
    selectedElements[parent] = id
    $('#material-' + selectedElements[parent]).addClass('block-active')
    $('#material-' + selectedElements[parent]).find('.label-use').show()
    $('#material-' + selectedElements[parent]).append('<input class="input-' + parent + '" type="hidden" name="WizardForm[materials][' + parent + ']" value="' + id + '">')
  }
})

let timerId = null

$('.block').mouseenter(function () {
  timerId = setTimeout(function(el) {
    $(el).popover('show')
  }, 1000, this)
})

$('.block').mouseleave(function () {
  if (timerId !== null) {
    $(this).popover('hide')
    clearTimeout(timerId)
    timerId = null
  }
})

JS
    ,
    View::POS_END
);

$this->registerCss(
    <<<'CSS'

.form {
    width: 100%;
}

.submit {
    width: 100%;
}

.block {
    transition: 0.25s;
}

.block:hover {
    background-color: #acacac;
    box-shadow: #acacac 0 0 4px 4px;
    transition: 0.5s;
}

.block-active, .block-active:hover {
    background-color: #acc8ac;
    box-shadow: #acc8ac 0 0 4px 4px;
}

CSS
)

?>

<script src="/js/three.js"></script>
<script src="/js/OrbitControls.js"></script>

<h1><?=Html::encode($this->title)?></h1>
<div style="display: flex; flex-direction: column;">
    <div class="canvas" style="display: block">

    </div>
    <div style="display: flex; justify-content: space-around; padding: 8px; border-radius: 16px; height: 350px; background-color: #DDDDDD">
        <div id="room-materials" style="flex-basis: 200px; flex-grow: 2; margin: 0 8px; overflow: auto; display: none">
            <?php foreach ($materials as $key => $typeMaterials): ?>
                <div id="block-<?=$key?>">
                    <h1><?=ucfirst($key)?></h1>
                    <?php /** @var Material $material */ ?>
                    <?php foreach ($typeMaterials as $material): ?>
                        <?php $details = json_decode($material->details) ?>
                        <div style="display: flex; margin: 8px 0; justify-content: space-between"
                             class="block<?php if ($material->vendor_code == (isset($form->materials[$key])
                                     ? $form->materials[$key] : null)): ?> block-active<?php endif ?>"
                             id="material-<?=$material->vendor_code?>"
                             data-toggle="popover" data-placement="top"
                             data-content="<?=isset($details->text) ? $details->text : 'Material for '.$key?>">
                            <div style="display: flex;">
                                <img style="width: 108px; height: 108px; margin: 0 8px 0 0"
                                     src="<?='/img/materials_photo/'.$material->photo?>" alt="">
                                <div style="display: flex; flex-direction: column;">
                                    <h4><?=$material->display_name?></h4>
                                    <h5>Price: <?=$material->price?> per <?=$material->type->measurements?></h5>
                                    <h5>Price per m<span
                                                style="vertical-align: super; font-size: 8pt">2</span>: <?=$material->price * $material->multiplier?>
                                    </h5>
                                </div>
                            </div>
                            <label style="margin: auto 8px auto 0;<?php if ($material->vendor_code != (isset($form->materials[$key])
                                    ? $form->materials[$key] : null)): ?> display: none;<?php endif ?>"
                                   class="label-use">
                                Use this material
                            </label>
                            <?php if ($material->vendor_code == (isset($form->materials[$key]) ? $form->materials[$key]
                                    : null)): ?>
                                <input class="input-<?=$key?>" type="hidden" name="WizardForm[materials][<?=$key?>]"
                                       value="<?=$form->materials[$key]?>">
                            <?php endif ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="size-options" style="display: flex; justify-content: space-between; flex-basis: 200px; flex-grow: 2; margin: 0 8px">
            <div id="room-options" style="flex-basis: 100px; flex-grow: 1; margin: 0 8px">
                <div style="display: flex;  height: 100%; flex-direction: column; justify-content: space-between">
                    <div>
                        <h3>Room</h3>

                        <div class="form-group">
                            <label>Floor Width</label>
                            <input id="room-floor-width" class="form-control" type="number" value="<?=$room['sizeX']?>">
                        </div>
                        <div class="form-group">
                            <label>Floor Height</label>
                            <input id="room-floor-height" class="form-control" type="number" value="<?=$room['sizeZ']?>">
                        </div>
                        <div class="form-group">
                            <label>Wall Height</label>
                            <input id="room-wall-height" class="form-control" type="number" value="<?=$room['sizeY']?>">
                        </div>
                    </div>
                    <div style="display: flex; ">
                        <div class="form-group" style="flex-grow: 1; flex-basis: 100">
                            <a id="change-room-size" class="btn btn-primary" style="width: 100%">Change Size</a>
                        </div>
                    </div>
                </div>
            </div>
            <div style="flex-basis: 100px; flex-grow: 1; margin: 0 8px">
                <div id="target-options" style="display: none; height: 100%">
                    <div style="display: flex;  height: 100%; flex-direction: column; justify-content: space-between">
                        <div>
                            <h3>Opening</h3>

                            <div class="form-group">
                                <label>Width</label>
                                <input id="target-width" class="form-control" type="number" value="1">
                            </div>
                            <div class="form-group">
                                <label>Height</label>
                                <input id="target-height" class="form-control" type="number" value="1">
                            </div>
                        </div>
                        <div style="display: flex; ">
                            <div class="form-group" style="flex-basis: 100px; flex-grow: 1; margin-right: 4px">
                                <a id="remove-opening" class="btn btn-danger" style="width: 100%">Remove</a>
                            </div>
                            <div class="form-group" style="flex-basis: 100px; flex-grow: 1; margin-left: 4px">
                                <a id="change-opening-size" class="btn btn-primary" style="width: 100%;">Change Size</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="flex-basis: 100px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; margin: 0 8px">
            <div style="flex-basis: 100px; flex-grow: 1;">
                <h3>Menu</h3>
                <a id="new-opening" class="btn btn-success" style="margin-top: 20px; width: 100%">Add New Opening</a>
                <a id="select-materials" class="btn btn-primary" style="margin-top: 20px; width: 100%">Select
                    Materials</a>
                <a id="size-options-btn" class="btn btn-primary" style="margin-top: 20px; width: 100%; display: none">Select
                    Room Size</a>
            </div>
            <div style="flex-basis: 100px; flex-grow: 1; display: flex; flex-direction: column-reverse">
                <?php $htmlForm = ActiveForm::begin(
                    ['id' => 'model', 'action' => '/wizard?step=5', 'options' => ['class' => 'form']]
                ); ?>

                <?=$htmlForm->field($form, 'floor_width')->hiddenInput(['id' => 'hidden-floor-width'])->label(false)?>
                <?=$htmlForm->field($form, 'floor_height')->hiddenInput(['id' => 'hidden-floor-height'])->label(false)?>
                <?=$htmlForm->field($form, 'wall_height')->hiddenInput(['id' => 'hidden-wall-height'])->label(false)?>
                <div class="form-group openings">
                    <?php foreach ($form->openings as $idx => $opening): ?>
                        <input id="opening-width-<?=$idx?>" type="hidden" class="form-control"
                               name="WizardForm[openings][<?=$idx?>][width]"
                               value="<?=$opening['width']?>">
                        <input id="opening-height-<?=$idx?>" type="hidden" class="form-control"
                               name="WizardForm[openings][<?=$idx?>][height]"
                               value="<?=$opening['height']?>">
                    <?php endforeach ?>
                </div>
                <div class="form-group materials">
                    <?php foreach ($form->usage_material as $idx => $usage_material): ?>
                        <input id="usage-material-<?=$usage_material?>" type="hidden" class="form-control" name="WizardForm[usage_material][<?=$idx?>]"
                               value="<?=$usage_material?>">
                    <?php endforeach ?>
                    <?php foreach ($form->materials as $idx => $material): ?>
                        <input id="materials-<?=$idx?>" type="hidden" class="form-control" name="WizardForm[materials][<?=$idx?>]"
                               value="<?=$material?>">
                    <?php endforeach ?>
                </div>

                <div class="form-group">
                    <?=Html::submitButton('Generate Bill', ['class' => 'btn btn-success submit'])?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
  // TODO : добавить изменение материалов комнаты + передвеженение controls.target в рамках комнаты

  var defaultCameraAngle = 0
  var objectDepth = 0.1
  var openingDepth = 0.05

  var scene = new THREE.Scene()
  var camera = new THREE.PerspectiveCamera(90, 1.6, 0.1, 1000)
  var renderer = new THREE.WebGLRenderer()
  var globalLight = new THREE.PointLight(0xFFFFFF, 0.4)
  var controls = new THREE.OrbitControls(camera, renderer.domElement, document.getElementsByClassName('canvas')[0])
  var raycaster = new THREE.Raycaster()
  var transparencyMaterial = new THREE.MeshPhongMaterial({ color: 0xFFFFFF, opacity: 0.3, transparent: true })
  var openingLastIndex = 0
  var statePosition = 0
  var openings = []
  var openingsOnWall = []
  var walls = []
  var cell = null
  var floor = null
  var roomLight = null
  var _dragged = false
  var _target = null
  var _click = false

  let setCanvasSize = function () {
    let parentWidth = document.getElementsByClassName('canvas')[0].parentElement.clientWidth

    renderer.setSize(parentWidth, parentWidth / 16 * 9)
  }

  let setObjectSize = function (object, size) {
    object.scale.y = size.y
    object.scale.x = size.x
    object.scale.z = size.z

    if (object.rotation.y !== 0) {
      object.scale.x = size.z
      object.scale.z = size.x
    }
  }

  let showMaterials = function() {
    document.getElementById('select-materials').style.display = 'none'
    document.getElementById('size-options-btn').style.display = 'block'
    document.getElementById('size-options').style.display = 'none'
    document.getElementById('room-materials').style.display = 'block'
  }

  let showSizes = function() {
    document.getElementById('select-materials').style.display = 'block'
    document.getElementById('size-options-btn').style.display = 'none'
    document.getElementById('size-options').style.display = 'flex'
    document.getElementById('room-materials').style.display = 'none'
  }

  let getObjectSize = function (object) {
    return new THREE.Vector3(object.scale.x, object.scale.y, object.scale.z)
  }

  let getTouchingWall = function (opening) {
    if (opening.rotation.y === 0) {
      return opening.position.z > 0 ? 0 : 2
    }

    return opening.position.x > 0 ? 1 : 3
  }

  let sortOpenings = function () {
    openingsOnWall = []
    openingsOnWall[0] = []
    openingsOnWall[1] = []
    openingsOnWall[2] = []
    openingsOnWall[3] = []

    openings.forEach(function (opening) {
      openingsOnWall[getTouchingWall(opening)].push(opening)
    })
  }

  let initDefaultValues = function () {
    renderer.domElement.addEventListener('mousedown', onPointerDown, false)
    renderer.domElement.addEventListener('touchstart', onPointerDown, false)
    renderer.domElement.addEventListener('mousemove', onPointerHover, false)
    renderer.domElement.addEventListener('touchmove', onPointerHover, false)
    renderer.domElement.addEventListener('mouseup', onPointerUp, false)
    renderer.domElement.addEventListener('mouseout', onPointerUp, false)
    renderer.domElement.addEventListener('touchend', onPointerUp, false)
    renderer.domElement.addEventListener('touchcancel', onPointerUp, false)
    renderer.domElement.addEventListener('touchleave', onPointerUp, false)

    controls.target = new THREE.Vector3(0, (room.sizeY + objectDepth) / 2, 0)
    //controls.noZoom = true
    controls.minY = objectDepth
    controls.zoomSpeed = 2
    controls.noPan = true
    controls.minDistance = 0.1
    controls.maxDistance = 2.5 * Math.max(room.sizeY, Math.max(room.sizeX, room.sizeZ))
    controls.rotateSpeed = 0.5

    setCanvasSize()
    renderer.setClearColor(0xFFFFFF, 1)

    document.getElementsByClassName('canvas')[0].appendChild(renderer.domElement)
  }

  let initSceneObjects = function (room, materials) {
    let boxGeometry = new THREE.BoxGeometry()

    walls[0] = new THREE.Mesh(boxGeometry, materials['walls'])
    walls[2] = new THREE.Mesh(boxGeometry, materials['walls'])
    walls[1] = new THREE.Mesh(boxGeometry, materials['walls'])
    walls[3] = new THREE.Mesh(boxGeometry, materials['walls'])
    floor = new THREE.Mesh(boxGeometry, materials['floors'])
    cell = new THREE.Mesh(boxGeometry, materials['cells'])
    roomLight = new THREE.PointLight(0xFFFFFF, 1.0)

    walls[0].scale.set(room.sizeX, room.sizeY + objectDepth * 2, objectDepth)
    walls[2].scale.set(room.sizeX, room.sizeY + objectDepth * 2, objectDepth)
    walls[1].scale.set(objectDepth, room.sizeY + objectDepth * 2, room.sizeZ)
    walls[3].scale.set(objectDepth, room.sizeY + objectDepth * 2, room.sizeZ)
    floor.scale.set(room.sizeX, objectDepth, room.sizeZ)
    cell.scale.set(room.sizeX, objectDepth, room.sizeZ)

    Object.values(room.openings).forEach(function (opening, idx) {
      let openingMesh = new THREE.Mesh(boxGeometry, getMaterial())
      openingMesh.scale.set(opening.width, opening.height, openingDepth)

      openingMesh.name = '' + (idx + 1)

      openings.push(openingMesh)
      scene.add(openingMesh)

      openingMesh.position.z = (room.sizeZ - objectDepth) / 2 * ((statePosition % 4 === 0 || statePosition % 4 === 2) ? 1 : -1)
      openingMesh.position.x = (room.sizeX - opening.width) / 2 * ((statePosition % 4 === 0 || statePosition % 4 === 1) ? 1 : -1)
      openingMesh.position.y = (statePosition % 8 < 4) ? ((opening.height + objectDepth) / 2) : (room.sizeY + (objectDepth - opening.height) / 2)

      statePosition++
      openingLastIndex++
    })
    sortOpenings()

    scene.add(floor)
    scene.add(walls[0])
    scene.add(walls[1])
    scene.add(walls[2])
    scene.add(walls[3])
    scene.add(cell)
    scene.add(roomLight)
    scene.add(globalLight)

    walls[0].position.set(0, (room.sizeY + objectDepth) / 2, (room.sizeZ + objectDepth) / 2)
    walls[2].position.set(0, (room.sizeY + objectDepth) / 2, (room.sizeZ + objectDepth) / -2)
    walls[1].position.set((room.sizeX + objectDepth) / 2, (room.sizeY + objectDepth) / 2, 0)
    walls[3].position.set((room.sizeX + objectDepth) / -2, (room.sizeY + objectDepth) / 2, 0)

    cell.position.y = room.sizeY + objectDepth

    roomLight.position.set(0, (room.sizeY + objectDepth / 2) / 4, 0)
    globalLight.position.set(0, room.sizeY + Math.max(room.sizeX, room.sizeZ), 0)

    camera.position.set(room.sizeX + 3, (room.sizeY + objectDepth) * 2, room.sizeZ + 3)
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

        document.getElementById('target-width').value = getObjectSize(_target.object).x
        document.getElementById('target-height').value = getObjectSize(_target.object).y
        document.getElementById('target-options').style.display = 'block'
      } else {
        _click = false
      }
    }
  }

  let showOpenings = function (wall) {
    openingsOnWall[wall].forEach(function (opening) {
      opening.material = getMaterial()
    })
  }

  let hideOpenings = function (wall) {
    openingsOnWall[wall].forEach(function (opening) {
      opening.material = transparencyMaterial
    })
  }

  let transparencyCheck = function() {
    if (camera.position.x > room.sizeX / 2 - 0.01) {
      walls[1].material = transparencyMaterial
      hideOpenings(1)
    } else {
      walls[1].material = materials['walls']
      showOpenings(1)

      if (camera.position.x < room.sizeX / -2 + 0.01) {
        walls[3].material = transparencyMaterial
        hideOpenings(3)
      } else {
        walls[3].material = materials['walls']
        showOpenings(3)
      }
    }

    if (camera.position.z > room.sizeZ / 2 - 0.01) {
      walls[0].material = transparencyMaterial
      hideOpenings(0)
    } else {
      walls[0].material = materials['walls']
      showOpenings(0)

      if (camera.position.z < room.sizeZ / -2 + 0.01) {
        walls[2].material = transparencyMaterial
        hideOpenings(2)
      } else {
        walls[2].material = materials['walls']
        showOpenings(2)
      }
    }

    if (camera.position.y > room.sizeY) {
      cell.material = transparencyMaterial
    } else {
      cell.material = materials['cells']
    }

    if (Math.abs(camera.position.x) > room.sizeX / 2 || Math.abs(camera.position.z) > room.sizeZ / 2 || camera.position.y > room.sizeY) {
      globalLight.position.x = camera.position.x
      globalLight.position.y = camera.position.y
      globalLight.position.z = camera.position.z

      globalLight.visible = true
    } else {
      globalLight.visible = false
    }
  }

  let onPointerHover = function (event) {
    transparencyCheck()

    if (_dragged === false) return

    let rect = renderer.domElement.getBoundingClientRect()
    let pointer = event.changedTouches ? event.changedTouches[0] : event
    let point = new THREE.Vector2((pointer.clientX - rect.left) / rect.width * 2 - 1, -(pointer.clientY - rect.top) / rect.height * 2 + 1)
    raycaster.setFromCamera(point, camera)
    let intersects = raycaster.intersectObjects(openings)

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

      let targetSizeX = getObjectSize(_target.object).x
      let targetSizeY = getObjectSize(_target.object).y

      if (_target.object.rotation.y === 0) {
        if (Math.abs(_target.object.position.x) > room.sizeX / 2 - targetSizeX / 2) {
          _target.object.position.z = (_target.object.position.z > 0 ? 1 : -1) * (room.sizeZ - targetSizeX) / 2 + (_target.object.position.z > 0 ? openingDepth / -2 : openingDepth / 2)
          _target.object.position.x = room.sizeX / 2 * (_target.object.position.x > 0 ? 1 : -1) + (_target.object.position.x > 0 ? openingDepth / -2 : openingDepth / 2)
          _target.object.rotation.y = Math.PI / 180 * 90

          sortOpenings()
        }
      } else {
        if (Math.abs(_target.object.position.z) > room.sizeZ / 2 - targetSizeX / 2) {
          _target.object.position.x = (_target.object.position.x > 0 ? 1 : -1) * (room.sizeX - targetSizeX) / 2 + (_target.object.position.x > 0 ? openingDepth / -2 : openingDepth / 2)
          _target.object.position.z = room.sizeZ / 2 * (_target.object.position.z > 0 ? 1 : -1) + (_target.object.position.z > 0 ? openingDepth / -2 : openingDepth / 2)
          _target.object.rotation.y = 0

          sortOpenings()
        }
      }

      if (_target.object.position.y > room.sizeY - (targetSizeY - objectDepth) / 2) {
        _target.object.position.y = room.sizeY - (targetSizeY - objectDepth) / 2

        sortOpenings()
      } else if (_target.object.position.y < (objectDepth + targetSizeY) / 2) {
        _target.object.position.y = (objectDepth + targetSizeY) / 2

        sortOpenings()
      }
    }
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

  let materialLastIndex = 2
  let onBlockClick = function () {
    let id = this.id.replace('material-', '')

    let parent = $(this).parent().get(0).id.replace('block-', '')

    let material = null

    if (parent in selectedElements && selectedElements[parent] === id) {
      material = getMaterial()

      document.getElementById('materials-' + parent).remove()
      document.getElementById('usage-material-' + parent).remove()
    } else {
      if (allMaterials[parent][id].use_pattern === 'picture') { // Picture Material
        material = getMaterial(allMaterials[parent][id].picture)
      } else { // Color material
        material = getMaterial(null, stringColorToInt(allMaterials[parent][id].color.slice(1)))
      }

      if (document.getElementById('usage-material-' + parent) !== null) {
        document.getElementById('materials-' + parent).value = id
      } else {
        materialLastIndex++

        document.getElementsByClassName('materials')[0].innerHTML += '<input id="usage-material-' + parent + '" type="hidden" class="form-control" name="WizardForm[usage_material][' + materialLastIndex + ']" value="' + parent + '">'
        document.getElementsByClassName('materials')[0].innerHTML += '<input id="materials-' + parent + '" type="hidden" class="form-control" name="WizardForm[materials][' + parent + ']" value="' + id + '">'
      }
    }

    if (parent === 'floors') {
      floor.material.dispose()
      materials['floors'] = material
      floor.material = material

    }

    if (parent === 'walls') {
      walls.forEach(function (wall) {
        wall.material.dispose()
        materials['walls'] = material
        wall.material = material
      })
    }

    if (parent === 'cells') {
      cell.material.dispose()
      materials['cells'] = material
      cell.material = material
    }

    transparencyCheck()
  }

  document.getElementById('remove-opening').onclick = function () {
    document.getElementById('opening-width-' + _target.object.name).remove()
    document.getElementById('opening-height-' + _target.object.name).remove()
    document.getElementById('target-options').style.display = 'none'

    _target.object.geometry.dispose()
    _target.object.material.dispose()
    scene.remove(_target.object)

    _target = null
  }

  document.getElementById('new-opening').onclick = function () {
    let boxGeometry = new THREE.BoxGeometry()
    let openingMesh = new THREE.Mesh(boxGeometry, getMaterial())
    openingMesh.scale.set(Math.min(room.sizeX, 1), Math.min(room.sizeY, 1), openingDepth)
//openings
    openingLastIndex++
    openingMesh.name = '' + openingLastIndex

    openings.push(openingMesh)
    scene.add(openingMesh)

    openingMesh.position.z = (room.sizeZ - objectDepth) / 2 * ((statePosition % 4 === 0 || statePosition % 4 === 2) ? 1 : -1)
    openingMesh.position.x = (room.sizeX - openingMesh.scale.x) / 2 * ((statePosition % 4 === 0 || statePosition % 4 === 1) ? 1 : -1)
    openingMesh.position.y = (statePosition % 8 < 4) ? ((openingMesh.scale.y + objectDepth) / 2) : (room.sizeY + (objectDepth - openingMesh.scale.y) / 2)

    statePosition++
    _target = { object: openingMesh }

    let openingWidth = document.createElement('input')
    let openingHeight = document.createElement('input')

    openingWidth.id = 'opening-width-' + openingLastIndex
    openingWidth.type = 'hidden'
    openingWidth.classList.add('form-control')
    openingWidth.name = 'WizardForm[openings][' + openingLastIndex + '][width]'
    openingWidth.value = 1

    openingHeight.id = 'opening-height-' + openingLastIndex
    openingHeight.type = 'hidden'
    openingHeight.classList.add('form-control')
    openingHeight.name = 'WizardForm[openings][' + openingLastIndex + '][height]'
    openingHeight.value = 1

    document.getElementsByClassName('openings')[0].appendChild(openingWidth)
    document.getElementsByClassName('openings')[0].appendChild(openingHeight)

    document.getElementById('target-width').value = getObjectSize(_target.object).x
    document.getElementById('target-height').value = getObjectSize(_target.object).y
    document.getElementById('target-options').style.display = 'block'

    showSizes()
  }

  document.getElementById('change-room-size').onclick = function () {
    let sizeX = parseFloat(document.getElementById('room-floor-width').value)
    let sizeZ = parseFloat(document.getElementById('room-floor-height').value)
    let sizeY = parseFloat(document.getElementById('room-wall-height').value)

    sizeX = Math.max(sizeX, 0.1)
    sizeY = Math.max(sizeY, 0.1)
    sizeZ = Math.max(sizeZ, 0.1)

    let minSizeX = sizeX
    let minSizeY = sizeY
    let minSizeZ = sizeZ

    openingsOnWall[0].concat(openingsOnWall[2]).forEach(function (opening) {
      minSizeX = Math.max(minSizeX, opening.scale.x)
      minSizeY = Math.max(minSizeY, opening.scale.y)
    })

    openingsOnWall[1].concat(openingsOnWall[3]).forEach(function (opening) {
      minSizeZ = Math.max(minSizeZ, opening.scale.x)
      minSizeY = Math.max(minSizeY, opening.scale.y)
    })

    sizeX = Math.max(minSizeX, sizeX)
    sizeY = Math.max(minSizeY, sizeY)
    sizeZ = Math.max(minSizeZ, sizeZ)

    floor.scale.set(sizeX, objectDepth, sizeZ)
    cell.scale.set(sizeX, objectDepth, sizeZ)
    walls[0].scale.set(sizeX, sizeY + objectDepth * 2, objectDepth)
    walls[2].scale.set(sizeX, sizeY + objectDepth * 2, objectDepth)
    walls[1].scale.set(objectDepth, sizeY + objectDepth * 2, sizeZ)
    walls[3].scale.set(objectDepth, sizeY + objectDepth * 2, sizeZ)

    walls[0].position.set(0, (sizeY + objectDepth) / 2, (sizeZ + objectDepth) / 2)
    walls[2].position.set(0, (sizeY + objectDepth) / 2, (sizeZ + objectDepth) / -2)
    walls[1].position.set((sizeX + objectDepth) / 2, (sizeY + objectDepth) / 2, 0)
    walls[3].position.set((sizeX + objectDepth) / -2, (sizeY + objectDepth) / 2, 0)
    cell.position.set(0, sizeY + objectDepth, 0)
    roomLight.position.set(0, (sizeY + objectDepth / 2) / 4, 0)

    openings.forEach(function (opening) {
      let wall = getTouchingWall(opening)
      let width = opening.scale.x
      let height = opening.scale.y

      if (wall === 0 || wall === 2) {
        opening.position.z = (sizeZ - openingDepth) / (wall === 0 ? 2 : -2)

        if (opening.position.x + width / 2 > sizeX / 2) {
          opening.position.x -= opening.position.x + width / 2 - sizeX / 2
        }

        if (opening.position.x - width / 2 < sizeX / -2) {
          opening.position.x += sizeX / -2 - opening.position.x + width / 2
        }
      }

      if (wall === 1 || wall === 3) {
        opening.position.x = (sizeX - openingDepth) / (wall === 1 ? 2 : -2)

        if (opening.position.z + width / 2 > sizeZ / 2) {
          opening.position.z -= opening.position.z + width / 2 - sizeZ / 2
        }

        if (opening.position.z - width / 2 < sizeZ / -2) {
          opening.position.z += sizeZ / -2 - opening.position.z + width / 2
        }
      }

      if (opening.position.y + height / 2 > sizeY + objectDepth) {
        opening.position.y -= (_target.object.position.y + height / 2) - sizeY - objectDepth
      }

      if (opening.position.y - height / 2 < objectDepth / 2) {
        opening.position.y += objectDepth / 2 - (opening.position.y - height / 2)
      }
    })

    room.sizeX = sizeX
    room.sizeY = sizeY
    room.sizeZ = sizeZ

    document.getElementById('room-floor-width').value = sizeX
    document.getElementById('room-floor-height').value = sizeZ
    document.getElementById('room-wall-height').value = sizeY
  }

  document.getElementById('select-materials').onclick = function () {
    showMaterials()
  }

  document.getElementById('size-options-btn').onclick = function () {
    showSizes()
  }

  document.getElementById('change-opening-size').onclick = function () {
    if (_target === null) return

    let width = parseFloat(document.getElementById('target-width').value)
    let height = parseFloat(document.getElementById('target-height').value)

    /// ============= VALIDATION START ================
    width = Math.max(width, 0.1)
    height = Math.max(height, 0.1)

    let wall = getTouchingWall(_target.object)
    if ((wall === 0 || wall === 2) && width > room.sizeX) {
      width = room.sizeX
      _target.object.position.x = 0
    } else if (wall === 0 || wall === 2) {
      if (_target.object.position.x + width / 2 > room.sizeX / 2) {
        _target.object.position.x -= _target.object.position.x + width / 2 - room.sizeX / 2
      }

      if (_target.object.position.x - width / 2 < room.sizeX / -2) {
        _target.object.position.x += room.sizeX / -2 - _target.object.position.x + width / 2
      }
    }

    if ((wall === 1 || wall === 3) && width > room.sizeZ) {
      width = room.sizeZ
      _target.object.position.z = 0
    } else if (wall === 1 || wall === 3) {
      if (_target.object.position.z + width / 2 > room.sizeZ / 2) {
        _target.object.position.z -= _target.object.position.z + width / 2 - room.sizeZ / 2
      }

      if (_target.object.position.z - width / 2 < room.sizeZ / -2) {
        _target.object.position.z += room.sizeZ / -2 - _target.object.position.z + width / 2
      }
    }

    if (height > room.sizeY) {
      height = room.sizeY
      _target.object.position.y = room.sizeY / 2 + objectDepth
    } else {
      if (_target.object.position.y + height / 2 > room.sizeY + objectDepth) {
        _target.object.position.y -= (_target.object.position.y + height / 2) - room.sizeY - objectDepth
      }

      if (_target.object.position.y - height / 2 < objectDepth / 2) {
        _target.object.position.y += objectDepth / 2 - (_target.object.position.y - height / 2)
      }
    }

    document.getElementById('target-width').value = width
    document.getElementById('target-height').value = height
    /// ============= VALIDATION END ================

    _target.object.scale.set(width, height, _target.object.scale.z)
    document.getElementById('opening-width-' + _target.object.name).value = width
    document.getElementById('opening-height-' + _target.object.name).value = height
  }
</script>
