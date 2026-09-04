import { Vector2 as _Vector2, Plane as _Plane, Vector3 as _Vector3 } from "three";
interface RugModelWithControlProps {
    glbSrc: string;
    stubSize: [number, number];
    stubOpacity: number;
    pos2D: _Vector2;
    yRotation: number;
    floorPlane: _Plane;
    pos3D: _Vector3;
    set2DPos: Function;
    setYRotation: Function;
}
export declare const RugModelWithControl: ({ glbSrc, stubSize, stubOpacity, pos2D, yRotation, floorPlane, pos3D, set2DPos, setYRotation }: RugModelWithControlProps) => import("react/jsx-runtime").JSX.Element;
export {};
