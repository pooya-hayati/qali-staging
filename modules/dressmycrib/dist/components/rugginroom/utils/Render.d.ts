export class Render {
    constructor(rugInRoomData: any);
    scene: THREE.Scene;
    camera: THREE.PerspectiveCamera;
    sceneOrtho: THREE.Scene;
    cameraOrtho: THREE.OrthographicCamera;
    renderer: THREE.WebGLRenderer;
    light: THREE.AmbientLight;
    init(rugInRoomData: any): Promise<void>;
    loadGLTFFromBuffer(buffer: any): Promise<any>;
    onAfterModelRender(renderer: any, scene: any, camera: any, geometry: any, material: any, group: any): Promise<void>;
    onAfterSpriteRender(renderer: any, scene: any, camera: any, geometry: any, material: any, group: any): Promise<void>;
    renderRugInImage(Url_GLB: any, rugInRoomData: any, buffer: any): Promise<string>;
}
import * as THREE from "three";
