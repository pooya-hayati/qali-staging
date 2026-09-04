import React from "react";
import { Vector2 as _Vector2, Plane as _Plane, Vector3 as _Vector3 } from "three";
interface CustomModelProps {
    glbSrc: string;
    stubSize: [number, number];
    stubOpacity: number;
    pos2D: _Vector2;
    yRotation: number;
    floorPlane: _Plane;
    pos3D: _Vector3;
}
export declare const CustomModel: React.ForwardRefExoticComponent<CustomModelProps & React.RefAttributes<import("three").Object3D<import("three").Event>>>;
export declare const AnimatedCustomModel: React.ForwardRefExoticComponent<CustomModelProps & {
    set2DPos: Function;
    setYRotation: Function;
    showRugControl: Function;
    hideRugControl: Function;
} & React.RefAttributes<import("three").Object3D<import("three").Event>>>;
export {};
