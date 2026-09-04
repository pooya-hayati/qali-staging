/**
 * Important class, responsible for storing each uploaded room data.
 */
export class RugsInRoomProvider {
    rugInRoomData: {};
    CAMERA_HEIGHT: number;
    CAMERA_FOV: number;
    setData(cachedDataJson: any): void;
    /**
     * Processes uploaded room image for putting rug into room function
     * @param {*} roomPhoto
     */
    processRoomImage(roomPhoto: any): Promise<void>;
    setCarpet2DPos(carpet2DPos: any): void;
    setCarpetRotation(carpetRotation: any): void;
    setDataFromStoredImage(storedImage: any): void;
    setDemoRoomDataFromJson(jsonResponse: any): void;
    downloadDataFromAI(roomPhoto: any): Promise<void>;
    renderRugImageInRoom(glbUrl: any, glbBuffer: any): Promise<string>;
}
