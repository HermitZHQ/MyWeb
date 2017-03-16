
var container;
var camera, scene, renderer, mod1;
var mouseX = 0, mouseY = 0;
var windowHalfX = window.innerWidth / 2;
var windowHalfY = window.innerHeight / 2;

function init() {
    // container = document.createElement('div');
    // document.body.appendChild(container);
    container = document.getElementById("contentArea");

    camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 2000);
    camera.position.z = 250;

    // scene
    scene = new THREE.Scene();
    var ambient = new THREE.AmbientLight(0xdddddd);
    scene.add(ambient);
    var directionalLight = new THREE.DirectionalLight(0xffeedd);
    directionalLight.position.set(0, 0, 1);
    scene.add(directionalLight);
    
    // texture
    var manager = new THREE.LoadingManager();
    manager.onProgress = function (item, loaded, total) {
        console.log(item, loaded, total);
    };
    var texture = new THREE.Texture();

    var onProgress = function (xhr) {
        if (xhr.lengthComputable) {
            var percentComplete = xhr.loaded / xhr.total * 100;
            console.log(Math.round(percentComplete, 2) + '% downloaded');
        }
    };

    var onError = function (xhr) {
        alert("Load model failed"); 
    };

    THREE.ImageUtils.crossOrigin = "anonymous";

    var loader = new THREE.ImageLoader(manager);

    loader.load('resource/obj/male02/male-02-1noCulling.jpg', function (image) {
        texture.image = image;
        texture.needsUpdate = true;
    });

    // model
    var loader = new THREE.OBJLoader(manager);

    loader.load('resource/obj/male02/male02.obj', function (object) {
        object.traverse(function (child) {
            if (child instanceof THREE.Mesh) {
                child.material.map = texture;
            }
        });
        object.position.y = - 95;
        mod1 = object;
        // object.rotateY(1.0);
        scene.add(object);
    }, onProgress, onError);

    //
    renderer = new THREE.WebGLRenderer();
    renderer.setPixelRatio(window.devicePixelRatio);
    //renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setSize(800, 600);
    renderer.setClearColor(0xffffff,1);
    container.appendChild(renderer.domElement);
    // container.setAttribute("background-color", "#eeeeee");
    document.addEventListener('mousemove', onDocumentMouseMove, false);

    //
    window.addEventListener('resize', onWindowResize, false);
}

function onWindowResize() {
    windowHalfX = window.innerWidth / 2;
    windowHalfY = window.innerHeight / 2;
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
}

function onDocumentMouseMove(event) {
    // mouseX = (event.clientX - windowHalfX) / 2;
    // mouseY = (event.clientY - windowHalfY) / 2;
}

//
function animate() {
    requestAnimationFrame(animate);
    render();
}

function render() {
    // mod1.rotateY(0.01);
    camera.position.x += (mouseX - camera.position.x) * .05;
    camera.position.y += (- mouseY - camera.position.y) * .05;
    camera.lookAt(scene.position);
    renderer.render(scene, camera);
}

$(document).ready(function () {
    //alert("main page ready");
    ParseParam();
    //LoadObjTest();

    init();
    animate();

    $("button#toReg").click(function () {
        self.location.href = "register.html";
    });

    $("button#toTop").click(function () {
        self.location.href = "#loginArea";
    });
})

function ParseParam() {
    var strParams = location.href.split("?")[1];
    if (null == strParams)
        return;

    var vArr = strParams.split(",");
    if (vArr.length > 0) {
        alert("received " + vArr.length + " params");

        if (vArr.length == 1 && vArr[0].split("=")[0] == "account") {

        }
    }
}

function LoadObjTest() {
    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 2000);
    camera.position.z = 250;

    //light
    var ambient = new THREE.AmbientLight(0x101030);

    scene.add(ambient);
    var directionalLight = new THREE.DirectionalLight(0xffeedd);

    directionalLight.position.set(0, 0, 1);

    scene.add(directionalLight);

    //renderer
    var renderer = new THREE.WebGLRenderer();
    var content = document.getElementById("contentArea");
    renderer.setSize(800, 600);
    content.appendChild(renderer.domElement);
    //document.body.appendChild(renderer.domElement);

    var geometry = new THREE.BoxGeometry(1, 1, 1);
    var material = new THREE.MeshBasicMaterial({ color: 0x00ff00 });
    var cube = new THREE.Mesh(geometry, material);
    scene.add(cube);

    // var loader = new THREE.OBJLoader();
    // // load a resource
    // var obj = loader.load(
    //     // resource URL
    //     'resource/obj/male02.obj',
    //     // Function when resource is loaded
    //     function (object) {
    //         scene.add(object);
    //     }
    // );

    // texture
    var manager = new THREE.LoadingManager();
    manager.onProgress = function (item, loaded, total) {
        console.log(item, loaded, total);
    };
    var texture = new THREE.Texture();
    var onProgress = function (xhr) {
        if (xhr.lengthComputable) {
            var percentComplete = xhr.loaded / xhr.total * 100;
            console.log(Math.round(percentComplete, 2) + '% downloaded');
        }
    };

    var onError = function (xhr) {

    };

    var loader = new THREE.ImageLoader(manager);
    loader.load('resource/textures/UV_Grid_Sm.jpg', function (image) {
        texture.image = image;
        texture.needsUpdate = true;
    });

    // model
    var loader = new THREE.OBJLoader(manager);
    loader.load('resource/obj/male02.obj', function (object) {
        object.traverse(function (child) {
            if (child instanceof THREE.Mesh) {
                child.material.map = texture;
            }
        });
        object.position.y = - 95;
        scene.add(object);
    }, onProgress, onError);

    var render = function () {
        requestAnimationFrame(render);

        cube.rotation.x += 0.1;
        cube.rotation.y += 0.1;

        renderer.render(scene, camera);
    };

    render();
}