var contentArea;
var camera, scene, renderer;
var mouseX = 0, mouseY = 0;
var windowHalfX = window.innerWidth / 2;
var windowHalfY = window.innerHeight / 2;
var mod1 = new THREE.Object3D();

// $(window).load(function () {
//     $('#slider').nivoSlider({
//         effect: 'random',
//         slices: 15,
//         boxCols: 8,
//         boxRows: 4,
//         animSpeed: 500,
//         pauseTime: 3000,
//         startSlide: 0,
//         directionNav: true,
//         controlNav: true,
//         controlNavThumbs: false,
//         pauseOnHover: true,
//         manualAdvance: false,
//         prevText: 'Prev',
//         nextText: 'Next',
//         randomStart: false,
//         beforeChange: function () { },
//         afterChange: function () { },
//         slideshowEnd: function () { },
//         lastSlide: function () { },
//         afterLoad: function () { }
//     });
// });

$(document).ready(function () {

    ParseParam();

    init();
    animate();

    SliderFunc();

    $("button#toReg").click(function () {
        self.location.href = "register.html";
    });

    $("button#toTop").click(function () {
        self.location.href = "#loginArea";
    });
})

function SliderFunc() {
    $("#slider").clientWidth = contentArea.clientWidth;
    $("#slider").clientHeight = contentArea.clientWidth / 1.7777;
    $("#slider").nivoSlider();
}

function init() {
    // container = document.createElement('div');
    // document.body.appendChild(container);
    contentArea = document.getElementById("contentArea");

    camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 2000);
    camera.position.z = 80;

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

    loader.load('resource/obj/head/tou.jpg', function (image) {
        texture.image = image;
        texture.needsUpdate = true;
    });

    // model
    var loader = new THREE.OBJLoader(manager);
    loader.load('resource/obj/head/001.obj', function (object) {
        object.traverse(function (child) {
            if (child instanceof THREE.Mesh) {
                // child.material.map = texture;
                //alert("resource/obj/head/"+child.name+".jpg");
                child.material.map = THREE.ImageUtils.loadTexture("resource/obj/head/"+child.name+".jpg");
            }
        });
        object.position.y = 10;
        mod1 = object;
        // object.rotateY(1.0);
        scene.add(object);
    }, onProgress, onError);

    //
    renderer = new THREE.WebGLRenderer();
    renderer.setPixelRatio(window.devicePixelRatio);
    //renderer.setSize(window.innerWidth, window.innerHeight);
    //renderer.setSize(800, 600);
    renderer.setSize(contentArea.clientWidth, contentArea.clientWidth / 1.7777);
    renderer.setClearColor(0xffffff, 0);
    //container.appendChild(renderer.domElement);
    $("#contentArea").prepend(renderer.domElement);
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
    // renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setSize(contentArea.clientWidth, contentArea.clientWidth / 1.7777);
    $("#slider").clientWidth = contentArea.clientWidth;
    $("#slider").clientHeight = contentArea.clientWidth / 1.7777;
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
    mod1.rotateY(0.01);
    camera.position.x += (mouseX - camera.position.x) * .05;
    camera.position.y += (- mouseY - camera.position.y) * .05;
    camera.lookAt(scene.position);
    renderer.render(scene, camera);
}

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