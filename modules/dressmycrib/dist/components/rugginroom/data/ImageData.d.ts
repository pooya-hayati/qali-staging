export class ImageData {
    rugsInRoomProvider: RugsInRoomProvider;
    setData(cachedDataJson: any): void;
    id: any;
    imageUrl: any;
    imageAlt: any;
    width: any;
    height: any;
    title: any;
    isUploadedRoom: any;
    addedToImageArray: any;
    renderedImageUrl: any;
    createNewRoomImage(uploadedRoomPhoto: any): Promise<void>;
    isImageValid(): boolean;
}
import { RugsInRoomProvider } from "./RugsInRoomProvider";
