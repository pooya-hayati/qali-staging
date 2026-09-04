import React from "react";
import { Vector2 as _Vector2, Vector4 as _Vector4 } from "three";
export interface CarpetControlProps extends React.HTMLAttributes<HTMLCanvasElement> {
    imgSrc: string;
    glbSrc: string;
    stubSize?: [number, number];
    stubOpacity?: number;
    cameraQuaternion: _Vector4;
    cameraHeight?: number;
    cameraFov?: number;
    carpet2DPos: _Vector2;
    setCarpet2DPos: Function;
    carpetRotation: number;
    setCarpetRotation: Function;
    cutoutSrc: string;
}
export declare const CarpetControl: React.FC<CarpetControlProps>;
