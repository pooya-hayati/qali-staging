export class ImageDataControler {
    deleteDataIfVersionIsDiff(): void;
    getArray(): any;
    setArray(array: any): void;
    findObjectById(id: any): any;
    storeImageData(imageData: any): boolean;
    deleteImageDataById(id: any): void;
    addDataFromJsonObject(imageJson: any): ImageData;
}
import { ImageData } from "./ImageData";
