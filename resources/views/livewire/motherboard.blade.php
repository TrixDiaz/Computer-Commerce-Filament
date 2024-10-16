<div>
    <div id="motherboard-container" style="width: 100%; height: 600px;"></div>

    <script type="module">
        import * as THREE from 'three';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

        let scene, camera, renderer, motherboard, controls;

        function init() {
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x333333); // Lighter gray background

            const container = document.getElementById('motherboard-container');
            const aspect = container.clientWidth / container.clientHeight;
            camera = new THREE.PerspectiveCamera(75, aspect, 0.1, 1000);
            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);

            // Improved lighting setup
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(ambientLight);

            const directionalLight1 = new THREE.DirectionalLight(0xffffff, 1);
            directionalLight1.position.set(5, 10, 7);
            scene.add(directionalLight1);

            const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight2.position.set(-5, -5, -5);
            scene.add(directionalLight2);

            const pointLight1 = new THREE.PointLight(0xffffff, 1);
            pointLight1.position.set(0, 10, 0);
            scene.add(pointLight1);

            const pointLight2 = new THREE.PointLight(0xffffff, 0.8);
            pointLight2.position.set(10, 0, 10);
            scene.add(pointLight2);

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;

            const loader = new GLTFLoader();
            loader.load('{{ $modelPath }}', (gltf) => {
                motherboard = gltf.scene;
                scene.add(motherboard);
                
                // Center the model
                const box = new THREE.Box3().setFromObject(motherboard);
                const center = box.getCenter(new THREE.Vector3());
                motherboard.position.sub(center);
                
                // Adjust camera position
                const size = box.getSize(new THREE.Vector3());
                const maxDim = Math.max(size.x, size.y, size.z);
                const fov = camera.fov * (Math.PI / 180);
                let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
                camera.position.set(0, 0, cameraZ * 1.5);
                
                // Add a spotlight to highlight the model
                const spotlight = new THREE.SpotLight(0xffffff, 2);
                spotlight.position.set(0, 50, 50);
                spotlight.target = motherboard;
                scene.add(spotlight);
                
                controls.update();
            });

            animate();
        }

        function animate() {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }

        init();

        window.addEventListener('resize', () => {
            const container = document.getElementById('motherboard-container');
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        });
    </script>
</div>
